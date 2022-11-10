<?php

namespace Futureecom\OmnipayTranzila\Message\Requests;

/**
 * Class PurchaseRequest
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     * @return array{tranmode: string}
     */
    public function getTransactionData(): array
    {
        return [
            'tranmode' => 'A',
        ];
    }
}
