<?php

namespace Omnipay\Tranzila\Message\Responses;

use JsonException;
use Omnipay\Common\Message\RequestInterface;

/**
 * Class Response.
 */
class Response extends AbstractResponse
{
    /**
     * @var ResponseInterface
     */
    protected $httpResponse;

    /**
     * Response constructor.
     *
     * @throws JsonException
     */
    public function __construct(RequestInterface $request, ?string $data)
    {
        parent::__construct($request, json_decode($data, true, 512, JSON_THROW_ON_ERROR));
    }
}
