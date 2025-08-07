<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;
use Omnipay\Tranzila\Message\Requests\ReversalRequest;
use Omnipay\Tranzila\Message\Responses\Response;

class ReversalRequestTest extends TestCase
{
    protected ReversalRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = new ReversalRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'app_key' => 'test_app_key',
            'secret' => 'test_secret',
            'terminal_name' => 'test_terminal',
            'amount' => '10.00',
            'currency' => 'ILS',
            'transaction_reference' => '12345',
            'authorization_number' => 'AUTH123',
        ]);
    }

    public function testGetDataWithCard()
    {
        $card = new CreditCard([
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]);

        $this->request->setCard($card);

        $data = $this->request->getData();

        $this->assertSame('test_terminal', $data['terminal_name']);
        $this->assertSame('reversal', $data['txn_type']);
        $this->assertSame(12345, $data['reference_txn_id']);
        $this->assertSame('AUTH123', $data['authorization_number']);
        $this->assertSame('4111111111111111', $data['card_number']);
        $this->assertSame(12, $data['expire_month']);
        $this->assertSame(2025, $data['expire_year']);
        $this->assertSame('123', $data['cvv']);
        $this->assertSame('Reversal', $data['items'][0]['name']);
        $this->assertSame('I', $data['items'][0]['type']);
        $this->assertSame(10.0, $data['items'][0]['unit_price']);
        $this->assertSame(1, $data['items'][0]['units_number']);
    }

    public function testGetDataWithToken()
    {
        $this->request->setToken('U99e9abcd81c2ca4444');
        $this->request->setExpiryMonth(11);
        $this->request->setExpiryYear(2028);

        $data = $this->request->getData();

        $this->assertSame('test_terminal', $data['terminal_name']);
        $this->assertSame('reversal', $data['txn_type']);
        $this->assertSame(12345, $data['reference_txn_id']);
        $this->assertSame('AUTH123', $data['authorization_number']);
        $this->assertSame('U99e9abcd81c2ca4444', $data['card_number']);
        $this->assertSame(11, $data['expire_month']);
        $this->assertSame(2028, $data['expire_year']);
        $this->assertMatchesRegularExpression('/^\d{3}$/', $data['cvv']); // Random 3-digit CVV
        $this->assertSame('Reversal', $data['items'][0]['name']);
        $this->assertSame('I', $data['items'][0]['type']);
        $this->assertSame(10.0, $data['items'][0]['unit_price']);
        $this->assertSame(1, $data['items'][0]['units_number']);
    }

    public function testGetDataWithDescription()
    {
        $card = new CreditCard([
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]);

        $this->request->setCard($card);
        $this->request->setDescription('Custom Reversal Description');

        $data = $this->request->getData();

        $this->assertSame('Custom Reversal Description', $data['items'][0]['name']);
    }

    public function testValidationRequiresTransactionReference()
    {
        $this->request->setTransactionReference(null);

        $this->expectException(\Omnipay\Common\Exception\InvalidRequestException::class);
        $this->request->getData();
    }

    public function testValidationRequiresAuthorizationNumber()
    {
        $this->request->setAuthorizationNumber(null);

        $this->expectException(\Omnipay\Common\Exception\InvalidRequestException::class);
        $this->request->getData();
    }

    public function testValidationRequiresCardWhenNoToken()
    {
        // No card or token set
        $this->expectException(\Omnipay\Common\Exception\InvalidRequestException::class);
        $this->request->getData();
    }

    public function testSendData()
    {
        $card = new CreditCard([
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]);

        $this->request->setCard($card);

        $this->setMockHttpResponse('ReversalSuccess.txt');

        $response = $this->request->send();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->isSuccessful());
    }

    public function testReversalWithInvalidReference()
    {
        $card = new CreditCard([
            'number' => '4111111111111111',
            'expiryMonth' => '12',
            'expiryYear' => '2025',
            'cvv' => '123',
        ]);

        $this->request->setCard($card);

        $this->setMockHttpResponse('ReversalFailure.txt');

        $response = $this->request->send();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse($response->isSuccessful());
    }
}
