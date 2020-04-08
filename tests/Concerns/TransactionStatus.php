<?php

namespace Tests\Concerns;

use Omnipay\Common\Message\RedirectResponseInterface;
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
     * @param string|null $redirectUrl
     */
    protected function assertTransaction(
        ResponseInterface $response,
        ?string $reference,
        ?string $message,
        ?string $code,
        bool $isSuccess = true,
        bool $isRedirect = false,
        bool $isCancelled = false,
        ?string $redirectUrl = null
    )
    {
        Assert::assertEquals([
            'cancelled' => $isCancelled,
            'code' => $code,
            'message' => $message,
            'redirect' => $isRedirect,
            'redirect_url' => $redirectUrl,
            'success' => $isSuccess,
            'transaction_reference' => $reference,
        ], [
            'cancelled' => $response->isCancelled(),
            'code' => $response->getCode(),
            'message' => $response->getMessage(),
            'redirect' => $response->isRedirect(),
            'redirect_url' => $this->getRedirectUrlFromResponse($response),
            'success' => $response->isSuccessful(),
            'transaction_reference' => $response->getTransactionReference(),
        ]);
    }

    /**
     * @param ResponseInterface $response
     * @return string|null
     */
    private function getRedirectUrlFromResponse(ResponseInterface $response): ?string
    {
        return $response instanceof RedirectResponseInterface ? $response->getRedirectUrl() : null;
    }
}
