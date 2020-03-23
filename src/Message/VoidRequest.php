<?php

namespace Futureecom\OmnipayTranzila\Message;

/**
 * Class VoidRequest
 */
class VoidRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     */
    protected function getTransactionData(): array
    {
        return [
            'tranmode' => "D{$this->getIndex()}",
        ];
    }
}
