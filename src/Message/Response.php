<?php

namespace Futureecom\OmnipayTranzila\Message;

use Futureecom\OmnipayTranzila\Status;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use stdClass;

/**
 * Class Response
 *
 * @property stdClass|null data
 */
class Response extends AbstractResponse
{
    /**
     * Response constructor.
     *
     * @param RequestInterface $request
     * @param string $data
     */
    public function __construct(RequestInterface $request, string $data)
    {
        parent::__construct($request, json_decode($data));
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

    /**
     * @inheritDoc
     */
    public function getTransactionReference()
    {
        return $this->data->ConfirmationCode ?? null;
    }
}
