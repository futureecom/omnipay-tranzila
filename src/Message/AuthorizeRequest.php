<?php

namespace Futureecom\OmnipayTranzila\Message;

/**
 * Class AuthorizeRequest
 */
class AuthorizeRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     */
    public function getTransactionData(): array
    {
        return [
            'task' => 'Doverify',
            'tranmode' => 'V',
        ];
    }
}
