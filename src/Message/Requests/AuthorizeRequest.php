<?php

namespace Futureecom\OmnipayTranzila\Message\Requests;

use Omnipay\Common\Message\ResponseInterface;

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

    /**
     * @param mixed $data
     * @return ResponseInterface
     */
    public function sendData($data): ResponseInterface
    {
        if ($this->hasParameters('ccno', 'expdate', 'mycvv')) {
            return parent::sendData($data);
        }

        return $this->createRedirectResponse();
    }
}
