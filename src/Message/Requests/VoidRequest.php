<?php

namespace Futureecom\OmnipayTranzila\Message\Requests;

use Futureecom\OmnipayTranzila\Message\Responses\Response;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Class VoidRequest.
 */
class VoidRequest extends AbstractRequest
{
    /**
     * @return Response&ResponseInterface
     * @throws InvalidRequestException
     */
    public function sendData($data): ResponseInterface
    {
        $this->validate('amount', 'authnr', 'index');

        return parent::sendData($data);
    }

    /**
     * @inheritDoc
     * @return array{tranmode: string}
     */
    protected function getTransactionData(): array
    {
        return [
            'tranmode' => "D{$this->getIndex()}",
        ];
    }
}
