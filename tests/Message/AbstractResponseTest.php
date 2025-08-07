<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Tranzila\Message\Responses\AbstractResponse;
use PHPUnit\Framework\TestCase;

class AbstractResponseTest extends TestCase
{
    public function testSuccessfulResponse()
    {
        $data = [
            'error_code' => 0,
            'message' => 'Success',
            'transaction_result' => [
                'transaction_id' => 'TXN123',
                'processor_response_code' => '000',
                'auth_number' => 'AUTH999',
                'token' => 'TOKEN999',
            ],
        ];
        $response = $this->makeResponse($data);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('TXN123', $response->getTransactionReference());
        $this->assertEquals('Success', $response->getMessage());
        $this->assertEquals('000', $response->getCode());
        $this->assertEquals('AUTH999', $response->getAuthorizationNumber());
        $this->assertEquals('TOKEN999', $response->getToken());
    }

    public function testFailedResponse()
    {
        $data = [
            'error_code' => 1,
            'message' => 'Failure',
            'transaction_result' => [
                'processor_response_code' => '001',
            ],
        ];
        $response = $this->makeResponse($data);
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

    protected function makeResponse($data)
    {
        return new class ($this->getMockRequest(), $data) extends AbstractResponse {
            // No extra methods needed for base tests
        };
    }
}
