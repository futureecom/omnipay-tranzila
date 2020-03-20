<?php

namespace Tests\Message;

use Futureecom\OmnipayTranzila\Message\AuthorizeRequest;
use Futureecom\OmnipayTranzila\Message\Response;
use Omnipay\Tests\TestCase;
use Tests\Concerns\TransactionStatus;

class AuthorizeRequestTest extends TestCase
{
    use TransactionStatus;

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
        $this->assertInstanceOf(Response::class, $this->request->send());
    }

    public function testZeroAmountResponse(): void
    {
        $this->setMockHttpResponse('AmountZero.txt');

        $response = $this->request->send();

        $this->assertTransaction(
            $response,
            null,
            'Amount Zero',
            '20014',
            false
        );
    }

    public function testCardNumberNotFoundOrFoundTwice(): void
    {
        $this->setMockHttpResponse('CardNumberNotFoundOrFoundTwice.txt');

        $response = $this->request->setAmount('100')->send();

        $this->assertTransaction(
            $response,
            '27-0000000',
            'Card number not found or found twice',
            '061',
            false
        );
    }

    public function testCardExpired(): void
    {
        $this->setMockHttpResponse('Expired.txt');

        $response = $this->request->setAmount('100')
            ->setCcNo('4444333322221111')
            ->send();

        $this->assertTransaction(
            $response,
            '28-0000000',
            'Expired.',
            '036',
            false
        );
    }

    public function testRefusal(): void
    {
        $this->setMockHttpResponse('Refusal.txt');

        $response = $this->request->setAmount('100')
            ->setCcNo('4444333322221111')
            ->setExpDate('1225')
            ->send();

        $this->assertTransaction(
            $response,
            '29-0000000',
            'Refusal.',
            '004',
            false
        );
    }

    public function testAuthorize(): void
    {
        $this->setMockHttpResponse('Authorize.txt');

        $response = $this->request->setAmount('100')
            ->setCcNo('4444333322221111')
            ->setExpDate('1225')
            ->setCredType('1')
            ->setMyCVV('1234')
            ->setCurrency('ILS')
            ->send();

        $this->assertTransaction(
            $response,
            '60-0000000',
            'Transaction approved',
            '000'
        );
    }
}
