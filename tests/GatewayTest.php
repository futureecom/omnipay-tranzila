<?php

namespace Tests;

use Futureecom\OmnipayTranzila\Message\AuthorizeRequest;
use Futureecom\OmnipayTranzila\Message\CaptureRequest;
use Futureecom\OmnipayTranzila\Message\PurchaseRequest;
use Futureecom\OmnipayTranzila\Message\RefundRequest;
use Futureecom\OmnipayTranzila\TranzilaGateway;
use Omnipay\Tests\TestCase;

/**
 * Class GatewayTest
 *
 * @property TranzilaGateway gateway
 */
class GatewayTest extends TestCase
{
    public function testGatewayName(): void
    {
        $this->assertSame('tranzila', $this->gateway->getName());
    }

    public function testSetSupplier(): void
    {
        $supplier = $this->gateway->getSupplier();
        $this->assertNotSame($supplier, $this->gateway->setSupplier('test2'));
    }

    public function testAuthorize(): void
    {
        /** @var AuthorizeRequest $request */
        $request = $this->gateway->authorize([
            'ccno' => '4444333322221111',
            'cred_type' => '1',
            'currency' => 'ILS',
            'expdate' => '1234',
            'mycvv' => '333',
            'TranzilaPW' => 'TranzilaPW'
        ]);

        $this->assertInstanceOf(AuthorizeRequest::class, $request);

        $this->assertEquals([
            'task' => 'Doverify',
            'tranmode' => 'V',
            'ccno' => '4444333322221111',
            'cred_type' => '1',
            'currency' => '1',
            'expdate' => '1234',
            'mycvv' => '333',
            'response_return_format' => 'json',
            'supplier' => 'test',
            'TranzilaPW' => 'TranzilaPW',
        ], $request->getData());
    }

    public function testCapture(): void
    {
        /** @var CaptureRequest $request */
        $request = $this->gateway->capture([
            'authnr' => 'ConfirmationCode'
        ]);

        $this->assertInstanceOf(CaptureRequest::class, $request);

        $this->assertEquals([
            'task' => 'Doforce',
            'tranmode' => 'F',
            'authnr' => 'ConfirmationCode',
            'response_return_format' => 'json',
            'supplier' => 'test',
        ], $request->getData());
    }

    public function testPurchase(): void
    {
        /** @var PurchaseRequest $request */
        $request = $this->gateway->purchase([
            'amount' => '10.00',
            'ccno' => '4444333322221111',
            'cred_type' => '1',
            'currency' => 'ILS',
            'expdate' => '1234',
            'mycvv' => '333',
            'myid' => '12312312',
            'TranzilaPW' => 'TranzilaPW',
        ]);

        $this->assertInstanceOf(PurchaseRequest::class, $request);

        $this->assertEquals([
            'tranmode' => 'A',
            'ccno' => '4444333322221111',
            'cred_type' => '1',
            'currency' => '1',
            'expdate' => '1234',
            'mycvv' => '333',
            'myid' => '12312312',
            'response_return_format' => 'json',
            'sum' => '10.00',
            'supplier' => 'test',
            'TranzilaPW' => 'TranzilaPW',
        ], $request->getData());
    }

    public function testRefund(): void
    {
        /** @var RefundRequest $request */
        $request = $this->gateway->refund([
            'index' => 10,
            'authnr' => 'ConfirmationCode',
            'cred_type' => '1',
            'myid' => '12312312',
        ]);

        $this->assertInstanceOf(RefundRequest::class, $request);

        $this->assertEquals([
            'authnr' => 'ConfirmationCode',
            'tranmode' => 'C10',
            'cred_type' => '1',
            'myid' => '12312312',
            'response_return_format' => 'json',
            'supplier' => 'test',
        ], $request->getData());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->gateway = new TranzilaGateway(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
        $this->gateway->initialize([
            'supplier' => 'test',
        ]);
    }
}

