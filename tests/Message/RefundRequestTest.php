<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Tranzila\Message\Requests\RefundRequest;
use Omnipay\Tranzila\Tests\Concerns\TransactionStatus;

class RefundRequestTest extends TestCase
{
    use TransactionStatus;

    protected RefundRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'app_key' => 'test_app_key',
            'secret' => 'test_secret',
            'terminal_name' => 'test_terminal',
            'amount' => '10.00',
            'currency' => 'ILS',
            'transaction_reference' => '12345',
            'authorization_number' => '0000000',
            'card' => new \Omnipay\Common\CreditCard([
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
        $this->assertEquals('credit', $data['txn_type']);
        $this->assertEquals(12345, $data['reference_txn_id']);
        $this->assertEquals('0000000', $data['authorization_number']);
        // Card details are not included when authorization number is provided
        $this->assertEquals(10.0, $data['items'][0]['unit_price']);
        $this->assertEquals('Refund', $data['items'][0]['name']);
        $this->assertEquals('ILS', $data['items'][0]['currency_code']);
    }

    public function testSendData()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('216', $response->getTransactionReference());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertEquals('000', $response->getCode());
    }

    public function testRefundWithInvalidReference()
    {
        $this->setMockHttpResponse('RefundFailure.txt');

        $response = $this->request->send();

        $this->assertTransaction(
            $response,
            null,
            'Transaction failed - gateway error code: 23002, processor code: 002',
            '002',
            false
        );
    }

    public function testRefundWithCard()
    {
        // Remove authorization number to test card-only scenario
        $this->request->setAuthorizationNumber(null);
        $this->request->setCard(new \Omnipay\Common\CreditCard([
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]));
        $data = $this->request->getData();

        $this->assertEquals('4111111111111111', $data['card_number']);
        $this->assertEquals(12, $data['expire_month']);
        $this->assertEquals(2025, $data['expire_year']);
        $this->assertEquals('123', $data['cvv']);
    }

    public function testRefundWithDescription()
    {
        $this->request->setDescription('Test Refund');
        $data = $this->request->getData();

        $this->assertEquals('Test Refund', $data['items'][0]['name']);
    }

    public function testRefundWithToken(): void
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        // Create a new request instance for token-based refund
        $tokenRequest = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $tokenRequest->initialize([
            'app_key' => 'test_app_key',
            'secret' => 'test_secret',
            'terminal_name' => 'test_terminal',
            'amount' => '5.00',
            'currency' => 'ILS',
            'transaction_reference' => '12345',
            'authorization_number' => '0000000',
            'token' => 'U99e9abcd81c2ca4444',
        ]);

        $response = $tokenRequest->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('216', $response->getTransactionReference());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertEquals('000', $response->getCode());
    }

    public function testRefundWithCreditCard(): void
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        // Create a new request instance for card-based refund
        $cardRequest = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $cardRequest->initialize([
            'app_key' => 'test_app_key',
            'secret' => 'test_secret',
            'terminal_name' => 'test_terminal',
            'amount' => '10.00',
            'currency' => 'ILS',
            'transaction_reference' => '12345',
            'authorization_number' => '0000000',
            'card' => new \Omnipay\Common\CreditCard([
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'cvv' => '123',
            ]),
        ]);

        $response = $cardRequest->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('216', $response->getTransactionReference());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertEquals('000', $response->getCode());
    }
}
