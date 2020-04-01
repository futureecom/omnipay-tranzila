<?php

namespace Futureecom\OmnipayTranzila\Message\Requests;

use Omnipay\Common\Message\ResponseInterface;

/**
 * Class CompletePurchaseRequest
 */
class CompletePurchaseRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     */
    protected function getTransactionData(): array
    {
        return [];
    }

    /**
     * @param  array  $data
     *
     * @return ResponseInterface
     */
    public function sendData($data): ResponseInterface
    {
        return $this->createResponse(json_encode($data));
    }
}
