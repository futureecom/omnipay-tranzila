<?php

namespace Futureecom\OmnipayTranzila\Message\Requests;

use Futureecom\OmnipayTranzila\Message\Responses\Response;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Class CaptureRequest
 */
class CaptureRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     * @return array{tranmode: string}
     */
    public function getTransactionData(): array
    {
        return [
            'tranmode' => 'F',
        ];
    }

    /**
     * @param mixed $data
     * @return Response&ResponseInterface
     * @throws InvalidRequestException
     */
    public function sendData($data): ResponseInterface
    {
        $this->validate('amount');

        return parent::sendData($data);
    }
}
