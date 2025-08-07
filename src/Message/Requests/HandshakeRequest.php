<?php

namespace Omnipay\Tranzila\Message\Requests;

use Omnipay\Tranzila\Message\Responses\AbstractResponse;
use Omnipay\Tranzila\Message\Responses\HandshakeResponse;

class HandshakeRequest extends AbstractRequest
{
    public function getData(): array
    {
        $this->validate('terminal_name', 'terminal_password', 'amount');
        return [
            'supplier' => $this->getTerminalName(),
            'TranzilaPW' => $this->getTerminalPassword(),
            'sum' => $this->getAmount(),
        ];
    }

    public function sendData($data): AbstractResponse
    {
        $url = $this->getEndpoint() . '?' . http_build_query($data);
        $httpResponse = $this->httpClient->request('GET', $url);
        return $this->createResponse($httpResponse->getBody()->getContents());
    }

    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }

    public function getEndpoint(): string
    {
        return static::BASE_URL . '/handshake/create';
    }

    protected function createResponse(string $data): AbstractResponse
    {
        return $this->response = new HandshakeResponse($this, $data);
    }
}
