<?php

namespace Tests\Message;

use Futureecom\OmnipayTranzila\Message\Requests\PurchaseRequest;
use Futureecom\OmnipayTranzila\Message\Responses\Response;
use Omnipay\Tests\TestCase;
use Tests\Concerns\TransactionStatus;

/**
 * Class PurchaseRequestTest.
 */
class PurchaseRequestTest extends TestCase
{
    use TransactionStatus;

    /**
     * @var PurchaseRequest
     */
    private $request;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->request = new PurchaseRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
        $this->request->initialize([
            'supplier' => 'test',
        ]);
    }

    public function testSendMessage(): void
    {
        self::assertInstanceOf(Response::class, $this->request->send());
    }

    public function testInvalidRequest(): void
    {
        $this->setMockHttpResponse('InvalidRequest.txt');

        $response = $this->request->setSupplier(null)->send();

        $this->assertTransaction(
            $response,
            null,
            'Invalid request.',
            '20000',
            false
        );
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

    public function testPurchase(): void
    {
        $this->setMockHttpResponse('Purchase.txt');

        $response = $this->request->setAmount('100')
            ->setCcNo('12312312')
            ->setExpDate('1234')
            ->setCredType('1')
            ->setMyCVV('1234')
            ->setCurrency('ILS')
            ->send();

        $this->assertTransaction(
            $response,
            '42-0000000',
            'Transaction approved',
            '000'
        );
    }

    public function testPurchaseUsingCardToken(): void
    {
        $this->setMockHttpResponse('PurchaseTokenTransaction.txt');

        $response = $this->request->setAmount('0.1')
            ->setCurrency('ILS')
            ->setTranzilaToken('U99e9abcd81c2ca4444')
            ->setExpDate('0924')
            ->send();

        $this->assertTransaction(
            $response,
            '42-0000000',
            'Transaction approved',
            '000',
            true,
            false,
            false,
            null,
            'Od3df2079abc0894111'
        );
    }
}
