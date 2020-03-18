<?php

namespace Tests\Message;

use Futureecom\OmnipayTranzila\Message\RefundRequest;
use Futureecom\OmnipayTranzila\Message\Response;
use Omnipay\Tests\TestCase;
use Tests\Concerns\TransactionStatus;

/**
 * Class RefundRequestTest
 */
class RefundRequestTest extends TestCase
{
    use TransactionStatus;

    /**
     * @var RefundRequest
     */
    private $request;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->request = new RefundRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
        $this->request->initialize([
            'supplier' => 'test',
        ]);
    }

    public function testSendMessage(): void
    {
        $response = $this->request->send();

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testPartialRefund(): void
    {
        $this->setMockHttpResponse('PartialRefund.txt');

        $response = $this->request->setAmount('50')
            ->setCurrency('ILS')
            ->setCredType('1')
            ->setCcNo('12312312')
            ->setTransactionReference('48-0000000')
            ->send();

        $this->assertTransaction(
            $response,
            '52-0000000',
            'Transaction approved',
            '000'
        );
    }

    public function testPartiallyRefundedTwice(): void
    {
        $this->setMockHttpResponse('PartiallyRefundedTwice.txt');

        $response = $this->request->setAmount('50')
            ->setCurrency('ILS')
            ->setCredType('1')
            ->setCcNo('12312312')
            ->setTransactionReference('48-0000000')
            ->send();

        $this->assertTransaction(
            $response,
            null,
            'Already Credited',
            '20020',
            false
        );
    }

    public function testRefund(): void
    {
        $this->setMockHttpResponse('Refund.txt');

        $response = $this->request->setAmount('100')
            ->setTransactionReference('0000000')
            ->setCurrency('ILS')
            ->setCredType('1')
            ->setCcNo('12312312')
            ->setIndex('51')
            ->send();

        $this->assertTransaction(
            $response,
            '54-0000000',
            'Transaction approved',
            '000'
        );
    }
}