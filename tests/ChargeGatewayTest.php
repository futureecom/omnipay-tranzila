<?php

namespace Tests;

use DateInterval;
use DateTime;
use Futureecom\OmnipayTranzila\Message\AuthorizeRequest;
use Futureecom\OmnipayTranzila\Message\CaptureRequest;
use Futureecom\OmnipayTranzila\Message\PurchaseRequest;
use Futureecom\OmnipayTranzila\Message\RefundRequest;
use Futureecom\OmnipayTranzila\TranzilaGateway;
use Omnipay\Tests\TestCase;

/**
 * Class ChargeGatewayTest
 *
 * @property TranzilaGateway gateway
 */
class ChargeGatewayTest extends TestCase
{
    public function testAuthorize(): void
    {
        $expDate = $this->expDate('+1 year');

        /** @var AuthorizeRequest $request */
        $request = $this->gateway->authorize([
            'ccno' => '4444333322221111',
            'cred_type' => '1',
            'currency' => 'ILS',
            'expdate' => $expDate,
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
            'expdate' => $expDate,
            'mycvv' => '333',
            'response_return_format' => 'json',
            'supplier' => 'test',
            'TranzilaPW' => 'TranzilaPW',
        ], $request->getData());
    }

    /**
     * @param string $string
     * @return string
     */
    protected function expDate(string $string): string
    {
        return (new DateTime())->add(DateInterval::createFromDateString($string))->format('my');
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
        $expDate = $this->expDate('+1 year');

        /** @var PurchaseRequest $request */
        $request = $this->gateway->purchase([
            'amount' => '10.00',
            'ccno' => '4444333322221111',
            'cred_type' => '1',
            'currency' => 'ILS',
            'expdate' => $expDate,
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
            'expdate' => $expDate,
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

