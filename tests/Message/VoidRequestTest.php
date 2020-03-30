<?php

namespace Tests\Message;

use Futureecom\OmnipayTranzila\Message\Requests\VoidRequest;
use Futureecom\OmnipayTranzila\Message\Responses\Response;
use Omnipay\Tests\TestCase;
use Tests\Concerns\TransactionStatus;

/**
 * Class VoidRequestTest
 */
class VoidRequestTest extends TestCase
{
    use TransactionStatus;

    /**
     * @var VoidRequest
     */
    private $request;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->request = new VoidRequest(
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

    public function testVoid(): void
    {
        $this->setMockHttpResponse('Void.txt');

        $response = $this->request->setTransactionReference('78-0000000')->send();

        $this->assertTransaction(
            $response,
            null,
            'Transaction approved',
            '000'
        );
    }

    public function testFailsVoid(): void
    {
        $this->setMockHttpResponse('ApplicationError.txt');

        $response = $this->request->setTransactionReference('43-0000000')->send();

        $this->assertTransaction(
            $response,
            null,
            'Application error.',
            '200',
            false
        );
    }
}
