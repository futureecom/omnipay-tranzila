<?php

namespace Omnipay\Tranzila\Tests\Concerns;

use Omnipay\Common\Message\ResponseInterface;

trait TransactionStatus
{
    protected function assertTransaction(
        ResponseInterface $response,
        ?string $transactionReference = null,
        ?string $message = null,
        ?string $code = null,
        bool $isSuccessful = true,
        bool $isCancelled = false,
        ?string $token = null
    ): void {
        $this->assertEquals($isSuccessful, $response->isSuccessful());
        $this->assertEquals($isCancelled, $response->isCancelled());
        $this->assertEquals($transactionReference, $response->getTransactionReference());
        $this->assertEquals($message, $response->getMessage());
        $this->assertEquals($code, $response->getCode());

        if ($token !== null) {
            $this->assertEquals($token, $response->getToken());
        }
    }
}
