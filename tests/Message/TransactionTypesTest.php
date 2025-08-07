<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;
use Omnipay\Tranzila\Message\Requests\AuthorizeRequest;
use Omnipay\Tranzila\Message\Requests\CaptureRequest;
use Omnipay\Tranzila\Message\Requests\PurchaseRequest;
use Omnipay\Tranzila\Message\Requests\RefundRequest;
use Omnipay\Tranzila\Message\Requests\VoidRequest;
use Omnipay\Tranzila\Tests\Concerns\TransactionStatus;

class TransactionTypesTest extends TestCase
{
    use TransactionStatus;

    protected $baseParams = [
        'app_key' => 'test_app_key',
        'secret' => 'test_secret',
        'terminal_name' => 'test_terminal',
        'amount' => '10.00',
        'currency' => 'ILS',
        // 'card' will be set in setUp()
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseParams['card'] = new CreditCard([
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]);
    }

    public function testDebitTransaction()
    {
        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize($this->baseParams);
        $this->setMockHttpResponse('DebitSuccess.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('debit', $response->getData()['transaction_result']['txn_type']);
    }

    public function testCreditTransaction()
    {
        $request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $params = $this->baseParams;
        $params['transaction_reference'] = '12345';
        $request->initialize($params);
        $this->setMockHttpResponse('CreditSuccess.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('credit', $response->getData()['transaction_result']['txn_type']);
    }

    public function testVerifyMode2Transaction()
    {
        $request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $params = $this->baseParams;
        $params['verify_mode'] = 2;
        $request->initialize($params);
        $this->setMockHttpResponse('VerifyMode2Success.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('verify', $response->getData()['transaction_result']['txn_type']);
        $this->assertEquals(2, $response->getData()['transaction_result']['verify_mode']);
    }

    public function testVerifyMode5Transaction()
    {
        $request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $params = $this->baseParams;
        $params['verify_mode'] = 5;
        $request->initialize($params);
        $this->setMockHttpResponse('VerifyMode5Success.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('verify', $response->getData()['transaction_result']['txn_type']);
        $this->assertEquals(5, $response->getData()['transaction_result']['verify_mode']);
    }

    public function testVerifyMode6Transaction()
    {
        $request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $params = $this->baseParams;
        $params['verify_mode'] = 6;
        $request->initialize($params);
        $this->setMockHttpResponse('VerifyMode6Success.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('verify', $response->getData()['transaction_result']['txn_type']);
        $this->assertEquals(6, $response->getData()['transaction_result']['verify_mode']);
    }

    public function testForceTransaction()
    {
        $request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());
        $params = $this->baseParams;
        $params['transaction_reference'] = '12345';
        $request->initialize($params);
        $this->setMockHttpResponse('ForceSuccess.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('force', $response->getData()['transaction_result']['txn_type']);
    }

    public function testStoTransaction()
    {
        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $params = $this->baseParams;
        $params['txn_type'] = 'sto';
        $request->initialize($params);
        $this->setMockHttpResponse('StoSuccess.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('sto', $response->getData()['transaction_result']['txn_type']);
    }

    public function testCancelTransaction()
    {
        $request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());
        $params = $this->baseParams;
        $params['transaction_reference'] = '12345';
        $request->initialize($params);
        $this->setMockHttpResponse('CancelSuccess.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        // Void responses don't contain transaction_result, just check success
    }

    public function testReversalTransaction()
    {
        $request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $params = $this->baseParams;
        $params['txn_type'] = 'reversal';
        $params['reference_txn_id'] = '12345';
        $params['authorization_number'] = '0000000';
        $request->initialize($params);
        $this->setMockHttpResponse('ReversalSuccess.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('reversal', $response->getData()['transaction_result']['txn_type']);
    }

    public function testJ2Transaction()
    {
        $request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $params = $this->baseParams;
        $params['verify_mode'] = 2;
        $request->initialize($params);
        $this->setMockHttpResponse('J2Success.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('verify', $response->getData()['transaction_result']['txn_type']);
        $this->assertEquals(2, $response->getData()['transaction_result']['verify_mode']);
    }

    public function testJ5Transaction()
    {
        $request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $params = $this->baseParams;
        $params['verify_mode'] = 5;
        $request->initialize($params);
        $this->setMockHttpResponse('J5Success.txt');
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('verify', $response->getData()['transaction_result']['txn_type']);
        $this->assertEquals(5, $response->getData()['transaction_result']['verify_mode']);
    }
}
