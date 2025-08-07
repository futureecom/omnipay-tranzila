<?php

namespace Omnipay\Tranzila\Tests;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\Tranzila\Gateway;

class GatewayTest extends GatewayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setAppKey('test_app_key');
        $this->gateway->setSecret('test_secret');
        $this->gateway->setTerminalName('test_terminal');
    }

    public function testAuthorize()
    {
        $request = $this->gateway->authorize([
            'amount' => '10.00',
            'currency' => 'USD',
            'card' => new CreditCard([
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'cvv' => '123',
            ]),
        ]);

        $this->assertInstanceOf('Omnipay\Tranzila\Message\Requests\AuthorizeRequest', $request);
        $this->assertEquals('10.00', $request->getAmount());
        $this->assertEquals('USD', $request->getCurrency());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase([
            'amount' => '10.00',
            'currency' => 'USD',
            'card' => new CreditCard([
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'cvv' => '123',
            ]),
        ]);

        $this->assertInstanceOf('Omnipay\Tranzila\Message\Requests\PurchaseRequest', $request);
        $this->assertEquals('10.00', $request->getAmount());
        $this->assertEquals('USD', $request->getCurrency());
    }

    public function testRefund()
    {
        $request = $this->gateway->refund([
            'amount' => '10.00',
            'currency' => 'USD',
            'transactionReference' => '12345',
        ]);

        $this->assertInstanceOf('Omnipay\Tranzila\Message\Requests\RefundRequest', $request);
        $this->assertEquals('10.00', $request->getAmount());
        $this->assertEquals('USD', $request->getCurrency());
        $this->assertEquals('12345', $request->getTransactionReference());
    }

    public function testVerify()
    {
        $request = $this->gateway->verify([
            'amount' => '10.00',
            'currency' => 'USD',
            'card' => new CreditCard([
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2025',
                'cvv' => '123',
            ]),
        ]);

        $this->assertInstanceOf('Omnipay\Tranzila\Message\Requests\VerifyRequest', $request);
        $this->assertEquals('10.00', $request->getAmount());
        $this->assertEquals('USD', $request->getCurrency());
    }

    public function testCapture()
    {
        $request = $this->gateway->capture([
            'amount' => '10.00',
            'currency' => 'USD',
            'transactionReference' => '12345',
        ]);

        $this->assertInstanceOf('Omnipay\Tranzila\Message\Requests\CaptureRequest', $request);
        $this->assertEquals('10.00', $request->getAmount());
        $this->assertEquals('USD', $request->getCurrency());
        $this->assertEquals('12345', $request->getTransactionReference());
    }

    public function testVoid()
    {
        $request = $this->gateway->void([
            'transactionReference' => '12345',
        ]);

        $this->assertInstanceOf('Omnipay\Tranzila\Message\Requests\VoidRequest', $request);
        $this->assertEquals('12345', $request->getTransactionReference());
    }
}
