<?php

namespace Futureecom\OmnipayTranzila\Message;

/**
 * Class CaptureRequest
 */
class CaptureRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     */
    public function getTransactionData(): array
    {
        return [
            'authnr' => $this->getAuthNr(),
            'task' => 'Doforce',
            'tranmode' => 'F',
        ];
    }
}
