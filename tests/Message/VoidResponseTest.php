<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Tranzila\Message\Responses\VoidResponse;
use PHPUnit\Framework\TestCase;

class VoidResponseTest extends TestCase
{
    public function testSuccessfulVoidResponse()
    {
        $data = json_encode([
            'error_code' => 0,
            'message' => 'Success',
        ]);
        $response = new VoidResponse($this->getMockRequest(), $data);

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertNull($response->getCode());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getAuthorizationNumber());
        $this->assertNull($response->getToken());
    }

    public function testFailedVoidResponse()
    {
        $data = json_encode([
            'error_code' => 1,
            'message' => 'Failure',
        ]);
        $response = new VoidResponse($this->getMockRequest(), $data);

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals('Transaction failed - gateway error code: 1', $response->getMessage());
        $this->assertNull($response->getCode());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getAuthorizationNumber());
        $this->assertNull($response->getToken());
    }

    public function testVoidResponseWithoutMessage()
    {
        $data = json_encode([
            'error_code' => 0,
        ]);
        $response = new VoidResponse($this->getMockRequest(), $data);

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('Transaction successful', $response->getMessage());
    }

    protected function getMockRequest()
    {
        return $this->createMock(RequestInterface::class);
    }
}
