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
            'task' => 'Doforce',
            'tranmode' => 'F',
        ];
    }
}
