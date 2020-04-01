<?php

namespace Tests\Message;

use Futureecom\OmnipayTranzila\Message\Requests\AbstractRequest;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Tests\TestCase;

/**
 * Class RequestTest
 */
class RequestTest extends TestCase
{
    /**
     * @var AbstractRequest
     */
    private $request;

    public function testSetParameters(): void
    {
        $this->request->setSupplier('test')
            ->setCurrency('USD')
            ->setMyCVV('1234')
            ->setCcNo('1234567890')
            ->setCredType('2')
            ->setAmount('1000')
            ->setExpDate('4567')
            ->setMyID('3489')
            ->setCreditPass('qwerty')
            ->setOrderId('HJd8lxLdVLSzNihY');

        $this->assertEquals([
            'supplier' => 'test',
            'currency' => 'USD',
            'mycvv' => '1234',
            'ccno' => '1234567890',
            'cred_type' => '2',
            'amount' => '1000',
            'expdate' => '4567',
            'myid' => '3489',
            'CreditPass' => 'qwerty',
            'orderId' => 'HJd8lxLdVLSzNihY',
        ], $this->request->getParameters());

        $this->request->setTransactionReference('555-80000001');
        $this->assertSame('555', $this->request->getIndex());
        $this->assertSame('80000001', $this->request->getAuthNr());
    }

    public function testSetTransactionReferenceWithInvalidFormat(): void
    {
        $this->request->setTransactionReference('555-555-100');
        $this->assertNull($this->request->getIndex());
        $this->assertNull($this->request->getAuthNr());
        $this->assertNull($this->request->getTransactionReference());

        $this->request->setTransactionReference('50000');
        $this->assertNull($this->request->getIndex());
        $this->assertNull($this->request->getAuthNr());
        $this->assertNull($this->request->getTransactionReference());
    }

    public function testGetInvalidCurrencyCode(): void
    {
        $this->request->setCurrency('PLN');
        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionMessage('Unsupported \'PLN\' currency.');
        $this->request->getCurrencyCode();
    }

    public function testGetTransactionReference(): void
    {
        $this->assertNull($this->request->getTransactionReference());
        $this->request->setIndex('20')->setAuthNr('50000');
        $this->assertSame('20-50000', $this->request->getTransactionReference());
    }

    public function testSumParameter(): void
    {
        $this->request->setSum('1000');
        $this->assertSame($this->request->getAmount(), $this->request->getSum());
    }

    public function testGetCardNumberFromCardObject(): void
    {
        $this->request->setCard(new CreditCard([
            'number' => '1234567890',
        ]));

        $this->assertSame('1234567890', $this->request->getCcNo());
    }

    public function testGetExpDateFromCardObject(): void
    {
        $this->request->setCard(new CreditCard([
            'expiryMonth' => '10',
            'expiryYear' => '2030',
        ]));

        $this->assertSame('1030', $this->request->getExpDate());
    }

    public function testGetCvvFromCardObject(): void
    {
        $this->request->setCard(new CreditCard([
            'cvv' => '333',
        ]));

        $this->assertSame('333', $this->request->getMyCVV());
    }

    public function testSetTranzilaParameters(): void
    {
        $this->request->setTranzilaTK('TK')
            ->setTranzilaPK('PK')
            ->setTranzilaPW('PW');

        $this->assertEquals([
            'TranzilaTK' => 'TK',
            'TranzilaPK' => 'PK',
            'TranzilaPW' => 'PW',
        ], $this->request->getParameters());

        $this->assertSame('TK', $this->request->getTranzilaTK());
        $this->assertSame('PK', $this->request->getTranzilaPK());
        $this->assertSame('PW', $this->request->getTranzilaPW());
    }

    public function testSetPayParameters(): void
    {
        $this->request->setFpay('1000')
            ->setNpay('200')
            ->setSpay('400');

        $this->assertEquals([
            'fpay' => '1000',
            'npay' => '200',
            'spay' => '400',
        ], $this->request->getParameters());

        $this->assertSame('1000', $this->request->getFpay());
        $this->assertSame('200', $this->request->getNpay());
        $this->assertSame('400', $this->request->getSpay());
    }

    public function testSetRedirectParameters(): void
    {
        $this->request
            ->setSum('8')
            ->setPDesc('Product description.')
            ->setContact('John Doe')
            ->setCompany('FutureEcom')
            ->setEmail('contact@example.com')
            ->setPhone('+00-1234-1234-1234')
            ->setFax('+00-1234-1234-1236')
            ->setAddress('123 Main Street')
            ->setCity('New York')
            ->setRemarks('glass')
            ->setTranzilaToken('ZUC9EVLk9fFVQx7c')
            ->setCurrency('ILS')
            ->setMyID('123456')
            ->setCredType('1')
            ->setOldPrice('10');

        $this->assertEquals([
            'amount' => '8',
            'pdesc' => 'Product description.',
            'contact' => 'John Doe',
            'company' => 'FutureEcom',
            'email' => 'contact@example.com',
            'phone' => '+00-1234-1234-1234',
            'fax' => '+00-1234-1234-1236',
            'address' => '123 Main Street',
            'city' => 'New York',
            'remarks' => 'glass',
            'TranzilaTK' => 'ZUC9EVLk9fFVQx7c',
            'currency' => 'ILS',
            'myid' => '123456',
            'cred_type' => '1',
            'oldprice' => '10',
        ], $this->request->getParameters());
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->request = $this->makeRequest();
    }

    /**
     * @return AbstractRequest
     */
    private function makeRequest(): AbstractRequest
    {
        return new class($this->getHttpClient(), $this->getHttpRequest()) extends AbstractRequest {
            /**
             * @inheritDoc
             */
            protected function getTransactionData(): array
            {
                return [];
            }
        };
    }
}
