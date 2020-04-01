<?php

namespace Tests\Message;

use Futureecom\OmnipayTranzila\Message\Requests\CompletePurchaseRequest;
use Futureecom\OmnipayTranzila\Message\Responses\Response;
use Omnipay\Tests\TestCase;
use Tests\Concerns\TransactionStatus;

/**
 * Class CompletePurchaseRequestTest
 */
class CompletePurchaseRequestTest extends TestCase
{
    use TransactionStatus;

    /**
     * @var CompletePurchaseRequest
     */
    private $request;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->request = new CompletePurchaseRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
        $this->request->initialize([
            'supplier' => 'test',
        ]);
    }

    public function testSendMessage(): void
    {
        $this->assertInstanceOf(Response::class, $this->request->send());
    }

    public function testGetData(): void
    {
        $this->assertEquals([
            'response_return_format' => 'json',
            'supplier' => 'test',
        ], $this->request->getData());
    }
}