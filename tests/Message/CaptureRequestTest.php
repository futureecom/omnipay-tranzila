<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Tranzila\Message\Requests\CaptureRequest;
use Omnipay\Tranzila\Tests\Concerns\TransactionStatus;

class CaptureRequestTest extends TestCase
{
    use TransactionStatus;

    protected CaptureRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());
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

        $this->assertSame('test_terminal', $data['terminal_name']);
        $this->assertSame('force', $data['txn_type']);
        $this->assertSame(12345, $data['reference_txn_id']);
        $this->assertSame(10.0, $data['items'][0]['unit_price']);
        $this->assertSame('ILS', $data['items'][0]['currency_code']);
        $this->assertSame('Capture', $data['items'][0]['name']);
    }

    public function testSendData()
    {
        $this->setMockHttpResponse('CaptureSuccess.txt');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('215', $response->getTransactionReference());
        $this->assertSame('Success', $response->getMessage());
        $this->assertSame('000', $response->getCode());
    }

    public function testCaptureWithDescription()
    {
        $this->request->setDescription('Test Capture');
        $data = $this->request->getData();

        $this->assertSame('Test Capture', $data['items'][0]['name']);
    }

    public function testCaptureWithInvalidReference()
    {
        $this->setMockHttpResponse('CaptureFailure.txt');

        $response = $this->request->send();

        $this->assertTransaction(
            $response,
            null,
            'Transaction failed - gateway error code: 1, processor code: 001',
            '001',
            false
        );
    }

    public function testCaptureWithZeroAmount()
    {
        $this->setMockHttpResponse('AmountZero.txt');

        $response = $this->request
            ->setAmount('0.00')
            ->setCurrency('ILS')
            ->send();

        $this->assertTransaction(
            $response,
            null,
            'Transaction failed - gateway error code: 20014, processor code: 20014',
            '20014',
            false
        );
    }

    public function testCaptureWithDifferentCurrency()
    {
        $this->request->setCurrency('ILS');
        $data = $this->request->getData();

        // Currency is not part of the capture request structure anymore
        $this->assertSame('force', $data['txn_type']);
        $this->assertSame(12345, $data['reference_txn_id']);
    }
}
