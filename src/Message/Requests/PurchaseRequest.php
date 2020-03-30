<?php

namespace Futureecom\OmnipayTranzila\Message\Requests;

/**
 * Class PurchaseRequest
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     */
    public function getTransactionData(): array
    {
        return [
            'tranmode' => 'A',
        ];
    }
}
