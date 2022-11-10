<?php

namespace Futureecom\OmnipayTranzila\Message\Requests;

/**
 * Class RefundRequest
 */
class RefundRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     * @return array{tranmode: string}
     */
    public function getTransactionData(): array
    {
        return [
            'tranmode' => "C{$this->getIndex()}",
        ];
    }
}
