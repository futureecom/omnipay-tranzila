<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Tranzila\Message\Responses\HandshakeResponse;
use PHPUnit\Framework\TestCase;

class HandshakeResponseTest extends TestCase
{
    public function testValidHandshakeResponse()
    {
        $data = 'thtk=abcdef123456';
        $response = new HandshakeResponse($this->getMockRequest(), $data);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('abcdef123456', $response->getHandshakeToken());
        $this->assertEquals('thtk=abcdef123456', $response->getFullHandshakeToken());
        $this->assertEquals('Handshake successful', $response->getMessage());
        $this->assertEquals('SUCCESS', $response->getCode());
        $respData = $response->getResponseData();
        $this->assertTrue($respData['successful']);
        $this->assertEquals('abcdef123456', $respData['handshake_token']);
        $this->assertEquals('thtk=abcdef123456', $respData['full_handshake_token']);
        $this->assertEquals('Handshake successful', $respData['message']);
        $this->assertEquals('SUCCESS', $respData['code']);
        $this->assertStringContainsString('thtk=', $respData['raw_response']);
    }

    public function testInvalidHandshakeResponse()
    {
        $data = 'invalid response';
        $response = new HandshakeResponse($this->getMockRequest(), $data);
        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getHandshakeToken());
        $this->assertNull($response->getFullHandshakeToken());
        $this->assertStringContainsString('Invalid handshake response', $response->getMessage());
        $this->assertEquals('INVALID_RESPONSE', $response->getCode());
    }

    protected function getMockRequest()
    {
        return $this->createMock(RequestInterface::class);
    }
}
