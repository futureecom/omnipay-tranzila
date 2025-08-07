<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Tranzila\Message\Responses\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testSuccessfulResponse()
    {
        $data = json_encode([
            'error_code' => 0,
            'message' => 'Success',
            'transaction_result' => [
                'transaction_id' => '123',
                'processor_response_code' => '000',
                'auth_number' => 'AUTH123',
                'token' => 'TOKEN123',
            ],
        ]);
        $response = new Response($this->getMockRequest(), $data);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('123', $response->getTransactionReference());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertEquals('000', $response->getCode());
        $this->assertEquals('AUTH123', $response->getAuthorizationNumber());
        $this->assertEquals('TOKEN123', $response->getToken());
    }

    public function testFailedResponse()
    {
        $data = json_encode([
            'error_code' => 1,
            'message' => 'Failure',
            'transaction_result' => [
                'processor_response_code' => '001',
            ],
        ]);
        $response = new Response($this->getMockRequest(), $data);
        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getTransactionReference());
        $this->assertEquals('Transaction failed - gateway error code: 1, processor code: 001', $response->getMessage());
        $this->assertEquals('001', $response->getCode());
        $this->assertNull($response->getAuthorizationNumber());
        $this->assertNull($response->getToken());
    }

    protected function getMockRequest()
    {
        return $this->createMock(RequestInterface::class);
    }
}
