<?php

namespace Tests\Message;

use Futureecom\OmnipayTranzila\Message\Requests\CaptureRequest;
use Futureecom\OmnipayTranzila\Message\Responses\Response;
use Omnipay\Tests\TestCase;
use Tests\Concerns\TransactionStatus;

/**
 * Class CaptureRequestTest
 */
class CaptureRequestTest extends TestCase
{
    use TransactionStatus;

    /**
     * @var CaptureRequest
     */
    private $request;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->request = new CaptureRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
        $this->request->initialize([
            'supplier' => 'test',
        ]);
    }

    public function testGetData(): void
    {
        $this->assertEquals([
            'tranmode' => 'F',
            'response_return_format' => 'json',
            'supplier' => 'test',
        ], $this->request->getData());
    }

    public function testSendMessage(): void
    {
        $this->assertInstanceOf(Response::class, $this->request->setAmount('1')->send());
    }

    public function testCaptureWithoutCardData(): void
    {
        $this->setMockHttpResponse('IllegalCreditOperation.txt');

        $response = $this->request
            ->setAmount('11')
            ->setTransactionReference('40-0000000')
            ->send();

        $this->assertTransaction(
            $response,
            null,
            'Illegal Credit Operation 2',
            '20021',
            false
        );
    }

    public function testCapture(): void
    {
        $this->setMockHttpResponse('Capture.txt');

        $response = $this->request->setAmount('0.01')
            ->setTransactionReference('23-0053748')
            ->send();

        $this->assertTransaction(
            $response,
            '40-0000000',
            'Transaction approved',
            '000'
        );
    }

    public function testCaptureTransactionAuthorizedUsingToken(): void
    {
        $this->setMockHttpResponse('CaptureTokenTransaction.txt');

        $response = $this->request
            ->setTransactionReference('18-0099908')
            ->setAmount('0.01')
            ->send();

        $this->assertTransaction(
            $response,
            '68-11111111',
            'Transaction approved',
            '000'
        );
    }
}
