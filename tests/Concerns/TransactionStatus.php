<?php

namespace Tests\Concerns;

use Omnipay\Common\Message\ResponseInterface;
use PHPUnit\Framework\Assert;

/**
 * Trait TransactionStatus
 */
trait TransactionStatus
{
    /**
     * @param ResponseInterface $response
     * @param string $reference
     * @param string $message
     * @param string $code
     * @param bool $isSuccess
     * @param bool $isRedirect
     * @param bool $isCancelled
     */
    protected function assertTransaction(
        ResponseInterface $response,
        ?string $reference,
        ?string $message,
        ?string $code,
        bool $isSuccess = true,
        bool $isRedirect = false,
        bool $isCancelled = false
    )
    {
        Assert::assertSame($reference, $response->getTransactionReference());
        Assert::assertSame($message, $response->getMessage());
        Assert::assertSame($code, $response->getCode());
        Assert::assertSame($isSuccess, $response->isSuccessful());
        Assert::assertSame($isRedirect, $response->isRedirect());
        Assert::assertSame($isCancelled, $response->isCancelled());
    }
}
