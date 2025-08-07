<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;
use Omnipay\Tranzila\Message\Requests\AuthorizeRequest;
use Omnipay\Tranzila\Tests\Concerns\TransactionStatus;

class AuthorizeRequestTest extends TestCase
{
    use TransactionStatus;

    protected AuthorizeRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
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
        $this->assertEquals('verify', $data['txn_type']);
        $this->assertEquals(5, $data['verify_mode']);
        $this->assertEquals(12, $data['expire_month']);
        $this->assertEquals(2025, $data['expire_year']);
        $this->assertEquals('123', $data['cvv']);
        $this->assertEquals('4111111111111111', $data['card_number']);
        $this->assertEquals(10.0, $data['items'][0]['unit_price']);
        $this->assertEquals('Authorization', $data['items'][0]['name']);
        $this->assertEquals('USD', $data['items'][0]['currency_code']);
    }

    public function testSendData()
    {
        $this->setMockHttpResponse('AuthorizeSuccess.txt');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('211', $response->getTransactionReference());
        $this->assertEquals('0000000', $response->getAuthorizationNumber());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertEquals('000', $response->getCode());
    }

    public function testZeroAmountResponse(): void
    {
        $this->setMockHttpResponse('AmountZero.txt');

        $response = $this->request
            ->setCard(new CreditCard([
                'number' => '112233',
                'expiryMonth' => '12',
                'expiryYear' => '2039',
                'cvv' => '123',
            ]))
            ->send();

        $this->assertTransaction(
            $response,
            null,
            'Transaction failed - gateway error code: 20014, processor code: 20014',
            '20014',
            false
        );
    }

    public function testCardExpired(): void
    {
        $this->setMockHttpResponse('Expired.txt');

        $response = $this->request->setAmount('100')
            ->setCard(new CreditCard([
                'number' => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear' => '2020',
                'cvv' => '123',
            ]))
            ->send();

        $this->assertTransaction(
            $response,
            '28-0000000',
            'Transaction failed - gateway error code: 36, processor code: 036',
            '036',
            false
        );
    }

    public function testRefusal(): void
    {
        $this->setMockHttpResponse('Refusal.txt');

        $response = $this->request->setAmount('100')
            ->setCard(new CreditCard([
                'number' => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'cvv' => '122',
            ]))
            ->send();

        $this->assertTransaction(
            $response,
            '29-0000000',
            'Transaction failed - gateway error code: 4, processor code: 004',
            '004',
            false
        );
    }

    public function testAuthorize(): void
    {
        $this->setMockHttpResponse('Authorize.txt');

        $response = $this->request->setAmount('100')
            ->setCard(new CreditCard([
                'number' => '4444333322221111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'cvv' => '1234',
            ]))
            ->setCredType('1')
            ->setCurrency('ILS')
            ->send();

        $this->assertTransaction(
            $response,
            '60-0000000',
            'Transaction approved',
            '000'
        );
    }

    public function testAuthorizeWithToken(): void
    {
        $this->setMockHttpResponse('AuthorizeTokenCard.txt');

        // Create a new request instance for token-based authorization
        $tokenRequest = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $tokenRequest->initialize([
            'app_key' => 'test_app_key',
            'secret' => 'test_secret',
            'terminal_name' => 'test_terminal',
            'amount' => '0.01',
            'currency' => 'ILS',
            'token' => 'U99e9abcd81c2ca4444',
        ]);

        $response = $tokenRequest->send();

        $this->assertTransaction(
            $response,
            '214',
            'Success',
            '000',
            true,
            false,
            'Od3df2079abc0894111'
        );
    }

    public function testAuthorizeWithCreditCard(): void
    {
        $this->setMockHttpResponse('AuthorizeSuccess.txt');

        // Create a new request instance for card-based authorization
        $cardRequest = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
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
        $this->assertEquals('211', $response->getTransactionReference());
        $this->assertEquals('0000000', $response->getAuthorizationNumber());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertEquals('000', $response->getCode());
    }
}
