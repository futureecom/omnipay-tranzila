<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Tranzila\Message\Requests\VoidRequest;
use Omnipay\Tranzila\Tests\Concerns\TransactionStatus;

class VoidRequestTest extends TestCase
{
    use TransactionStatus;

    protected VoidRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'app_key' => 'test_app_key',
            'secret' => 'test_secret',
            'terminal_name' => 'test_terminal',
            'transaction_reference' => '12345',
            'authorization_number' => '0000000',
        ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('test_terminal', $data['terminal_name']);
        $this->assertSame('cancel', $data['txn_type']);
        $this->assertSame(12345, $data['reference_txn_id']);
        $this->assertSame('0000000', $data['authorization_number']);
        $this->assertSame('english', $data['response_language']);
    }

    public function testSendData()
    {
        $this->setMockHttpResponse('VoidSuccess.txt');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertNull($response->getCode());
    }

    public function testVoidWithInvalidReference()
    {
        $this->setMockHttpResponse('VoidFailure.txt');

        $response = $this->request->send();

        $this->assertTransaction(
            $response,
            null,
            'Transaction failed - gateway error code: 20112',
            null,
            false
        );
    }

    public function testVoidWithTransactionId()
    {
        $this->request->setAuthorizationNumber('AUTH123');
        $data = $this->request->getData();

        $this->assertSame('AUTH123', $data['authorization_number']);
    }

    public function testVoidWithCard()
    {
        // Void requests don't include card data in the minimal structure
        $this->request->setCard([
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
        ]);
        $data = $this->request->getData();

        // Card data is not included in void requests
        $this->assertArrayNotHasKey('card_number', $data);
        $this->assertArrayNotHasKey('expire_month', $data);
        $this->assertArrayNotHasKey('expire_year', $data);
    }
}
