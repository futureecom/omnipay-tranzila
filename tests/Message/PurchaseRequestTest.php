<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;
use Omnipay\Tranzila\Message\Requests\PurchaseRequest;
use Omnipay\Tranzila\Tests\Concerns\TransactionStatus;

class PurchaseRequestTest extends TestCase
{
    use TransactionStatus;

    protected PurchaseRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'app_key' => 'test_app_key',
            'secret' => 'test_secret',
            'terminal_name' => 'test_terminal',
            'amount' => '10.00',
            'currency' => 'USD',
            'card' => new CreditCard([
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'cvv' => '123',
            ]),
        ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertEquals('test_terminal', $data['terminal_name']);
        $this->assertEquals('debit', $data['txn_type']);
        $this->assertEquals('USD', $data['items'][0]['currency_code']);
        $this->assertEquals('4111111111111111', $data['card_number']);
        $this->assertEquals(12, $data['expire_month']);
        $this->assertEquals(2025, $data['expire_year']);
        $this->assertEquals('123', $data['cvv']);
        $this->assertEquals(10.0, $data['items'][0]['unit_price']);
        $this->assertEquals('Purchase', $data['items'][0]['name']);
    }

    public function testSendData()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('217', $response->getTransactionReference());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertEquals('000', $response->getCode());
    }

    public function testPurchaseWithDescription()
    {
        $this->request->setDescription('Test Purchase');
        $data = $this->request->getData();

        $this->assertEquals('Test Purchase', $data['items'][0]['name']);
    }

    public function testPurchaseWithToken(): void
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        // Create a new request instance for token-based purchase
        $tokenRequest = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $tokenRequest->initialize([
            'app_key' => 'test_app_key',
            'secret' => 'test_secret',
            'terminal_name' => 'test_terminal',
            'amount' => '10.00',
            'currency' => 'USD',
            'token' => 'U99e9abcd81c2ca4444',
        ]);

        $response = $tokenRequest->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('217', $response->getTransactionReference());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertEquals('000', $response->getCode());
    }

    public function testPurchaseWithCreditCard(): void
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        // Create a new request instance for card-based purchase
        $cardRequest = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $cardRequest->initialize([
            'app_key' => 'test_app_key',
            'secret' => 'test_secret',
            'terminal_name' => 'test_terminal',
            'amount' => '10.00',
            'currency' => 'USD',
            'card' => new CreditCard([
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'cvv' => '123',
            ]),
        ]);

        $response = $cardRequest->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('217', $response->getTransactionReference());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertEquals('000', $response->getCode());
    }
}
