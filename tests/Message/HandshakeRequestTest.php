<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Tranzila\Message\Requests\HandshakeRequest;
use Omnipay\Tranzila\Message\Responses\HandshakeResponse;

class HandshakeRequestTest extends TestCase
{
    protected HandshakeRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new HandshakeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'terminal_name' => 'test_terminal',
            'terminal_password' => 'test_pw',
            'amount' => 5.5,
        ]);
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertSame('test_terminal', $data['supplier']);
        $this->assertSame('test_pw', $data['TranzilaPW']);
        $this->assertSame(5.5, $data['sum']);
    }

    public function testSendDataParsesResponse()
    {
        $this->setMockHttpResponse('HandshakeSuccess.txt');
        $response = $this->request->send();
        $this->assertInstanceOf(HandshakeResponse::class, $response);

        $this->assertSame('t3f88ef9db79d792b85c5dcdb3f52ba1e', $response->getHandshakeToken());
        $this->assertSame('thtk=t3f88ef9db79d792b85c5dcdb3f52ba1e', $response->getFullHandshakeToken());
        $this->assertTrue($response->isSuccessful());
        $this->assertSame('Handshake successful', $response->getMessage());
        $this->assertSame('SUCCESS', $response->getCode());
        $this->assertEquals(1, $response->hasValidHandshakeTokenFormat());
        $this->assertGreaterThan(0, $response->getHandshakeTokenLength());

        // Test getResponseData
        $data = $response->getResponseData();
        $this->assertTrue($data['successful']);
        $this->assertSame('t3f88ef9db79d792b85c5dcdb3f52ba1e', $data['handshake_token']);
        $this->assertSame('thtk=t3f88ef9db79d792b85c5dcdb3f52ba1e', $data['full_handshake_token']);
        $this->assertGreaterThan(0, $data['token_length']);
        $this->assertEquals(1, $data['has_valid_format']);
        $this->assertSame('Handshake successful', $data['message']);
        $this->assertSame('SUCCESS', $data['code']);
        $this->assertStringContainsString('thtk=', $data['raw_response']);
    }
}
