<?php

namespace Futureecom\OmnipayTranzila\Message;

/**
 * Class RefundRequest
 */
class RefundRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     */
    public function getTransactionData(): array
    {
        return [
            'tranmode' => "C{$this->getIndex()}",
            'authnr' => $this->getAuthNr(),
        ];
    }
}
