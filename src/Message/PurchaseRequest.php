<?php

namespace Futureecom\OmnipayTranzila\Message;

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
