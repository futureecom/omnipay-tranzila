<?php

namespace Tests\Message;

use Futureecom\OmnipayTranzila\Message\AuthorizeRequest;
use Futureecom\OmnipayTranzila\Message\Response;
use Omnipay\Tests\TestCase;

class AuthorizeRequestTest extends TestCase
{
    /**
     * @var AuthorizeRequest
     */
    private $request;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->request = new AuthorizeRequest(
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
            'task' => 'Doverify',
            'tranmode' => 'V',
            'response_return_format' => 'json',
            'supplier' => 'test',
        ], $this->request->getData());
    }

    public function testSendMessage(): void
    {
        $response = $this->request->send();

        $this->assertInstanceOf(Response::class, $response);
    }
}
