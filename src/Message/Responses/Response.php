<?php

namespace Futureecom\OmnipayTranzila\Message\Responses;

use Futureecom\OmnipayTranzila\Message\Requests\AbstractRequest;
use Futureecom\OmnipayTranzila\Status;
use JsonException;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use stdClass;

/**
 * Class Response.
 *
 * @property stdClass|null data
 */
class Response extends AbstractResponse
{
    /**
     * Response constructor.
     *
     * @throws JsonException
     */
    public function __construct(RequestInterface $request, ?string $data)
    {
        if ($data) {
            $data = json_decode($data, false, 512, JSON_THROW_ON_ERROR);
        }

        parent::__construct($request, $data);
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful(): bool
    {
        return $this->getCode() === '000';
    }

    /**
     * @inheritDoc
     */
    public function getCode(): ?string
    {
        return $this->data->Response ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->data->error_msg ?? Status::message($this->getCode());
    }

    public function getTranzilaTK(): ?string
    {
        return $this->data->TranzilaTK ?? null;
    }

    public function getTransactionReference(): ?string
    {
        $arr = array_filter([
            $this->data->index ?? null,
            $this->data->ConfirmationCode ?? null,
        ]);

        if (count($arr) < 2) {
            return null;
        }

        return implode(AbstractRequest::GLUE, $arr);
    }
}
