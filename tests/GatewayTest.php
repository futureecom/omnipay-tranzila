<?php

namespace Tests;

use Futureecom\OmnipayTranzila\Message\Requests\AuthorizeRequest;
use Futureecom\OmnipayTranzila\Message\Requests\CaptureRequest;
use Futureecom\OmnipayTranzila\Message\Requests\PurchaseRequest;
use Futureecom\OmnipayTranzila\Message\Requests\RefundRequest;
use Futureecom\OmnipayTranzila\Message\Requests\VoidRequest;
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
            'authnr' => '00000000'
        ]);

        $this->assertInstanceOf(CaptureRequest::class, $request);

        $this->assertEquals([
            'task' => 'Doforce',
            'tranmode' => 'F',
            'response_return_format' => 'json',
            'supplier' => 'test',
            'authnr' => '00000000'
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
            'authnr' => '00000000',
            'cred_type' => '1',
            'myid' => '12312312',
        ]);

        $this->assertInstanceOf(RefundRequest::class, $request);

        $this->assertEquals([
            'authnr' => '00000000',
            'tranmode' => 'C10',
            'cred_type' => '1',
            'myid' => '12312312',
            'response_return_format' => 'json',
            'supplier' => 'test',
        ], $request->getData());
    }

    public function testVoid(): void
    {
        $request = $this->gateway->void([
            'index' => '78',
            'authnr' => '00000000',
        ]);

        $this->assertInstanceOf(VoidRequest::class, $request);

        $this->assertEquals([
            'response_return_format' => 'json',
            'authnr' => '00000000',
            'supplier' => 'test',
            'tranmode' => 'D78',
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

