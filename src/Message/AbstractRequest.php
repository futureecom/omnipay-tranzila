<?php

namespace Futureecom\OmnipayTranzila\Message;

use Futureecom\OmnipayTranzila\Enums\Currency;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Class AbstractRequest
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * @var string
     */
    protected const endpoint = 'https://secure5.tranzila.com/cgi-bin/tranzila71u.cgi';

    /**
     * @inheritDoc
     */
    public function sendData($data): ResponseInterface
    {
        $response = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $this->getHeaders(),
            $this->prepareBody($data)
        );

        return $this->createResponse($response->getBody()->getContents());
    }

    /**
     * @return string
     */
    public function getHttpMethod(): string
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return static::endpoint;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            'content-type' => 'application/x-www-form-urlencoded',
        ];
    }

    /**
     * @param array $data
     * @return string
     */
    protected function prepareBody(array $data)
    {
        return http_build_query($data, '', '&');
    }

    /**
     * @param string $content
     * @return ResponseInterface
     */
    protected function createResponse(string $content): ResponseInterface
    {
        return $this->response = new Response($this, $content);
    }

    /**
     * @inheritDoc
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        return array_merge($this->getDefaultParameters(), $this->getTransactionData());
    }

    /**
     * @return array
     * @throws InvalidRequestException
     */
    protected function getDefaultParameters(): array
    {
        return array_filter([
            // response format
            'response_return_format' => 'json',

            // basic transaction data
            'currency' => $this->getCurrencyCode(),
            'sum' => $this->getAmount(),

            // credit card data
            'ccno' => $this->getCcNo(),
            'cred_type' => $this->getCredType(),
            'expdate' => $this->getExpDate(),
            'mycvv' => $this->getMyCVV(),

            // transaction with installments
            'fpay' => $this->getFpay(),
            'npay' => $this->getNpay(),
            'spay' => $this->getSpay(),

            // others...
            'myid' => $this->getMyID(),
            'supplier' => $this->getSupplier(),
            'TranzilaPW' => $this->getTranzilaPW(),
        ]);
    }

    /**
     * @return string|null
     * @throws InvalidRequestException
     */
    public function getCurrencyCode(): ?string
    {
        if (!$currency = $this->getCurrency()) {
            return null;
        }

        if (!Currency::isValidKey($currency)) {
            throw new InvalidRequestException("Unsupported '{$currency}' currency.");
        }

        /** @var Currency $value */
        $value = Currency::$currency();

        return (string)$value->getValue();
    }

    /**
     * @return string|null
     */
    public function getCcNo(): ?string
    {
        return $this->getParameter('ccno');
    }

    /**
     * @return string|null
     */
    public function getCredType(): ?string
    {
        return $this->getParameter('cred_type');
    }

    /**
     * @return string|null
     */
    public function getExpDate(): ?string
    {
        return $this->getParameter('expdate');
    }

    /**
     * @return string|null
     */
    public function getMyCVV(): ?string
    {
        return $this->getParameter('mycvv');
    }

    /**
     * @return string|null
     */
    public function getFpay(): ?string
    {
        return $this->getParameter('fpay');
    }

    /**
     * @return string|null
     */
    public function getNpay(): ?string
    {
        return $this->getParameter('npay');
    }

    /**
     * @return string|null
     */
    public function getSpay(): ?string
    {
        return $this->getParameter('spay');
    }

    /**
     * @return string|null
     */
    public function getMyID(): ?string
    {
        return $this->getParameter('myid');
    }

    /**
     * @return string|null
     */
    public function getSupplier(): ?string
    {
        return $this->getParameter('supplier');
    }

    /**
     * @return string|null
     */
    public function getTranzilaPW(): ?string
    {
        return $this->getParameter('TranzilaPW');
    }

    /**
     * Return transaction data specified to given transaction.
     *
     * @return array
     */
    abstract protected function getTransactionData(): array;

    /**
     * @param string|null $value
     * @return AbstractRequest
     */
    public function setTranzilaPW(?string $value): self
    {
        return $this->setParameter('TranzilaPW', $value);
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setMyCVV(?string $value): self
    {
        return $this->setParameter('mycvv', $value);
    }

    /**
     * @param string|null $value
     * @return AbstractRequest
     */
    public function setMyID(?string $value): self
    {
        return $this->setParameter('myid', $value);
    }

    /**
     * @param string|null $value
     * @return AbstractRequest
     */
    public function setCredType(?string $value): self
    {
        return $this->setParameter('cred_type', $value);
    }

    /**
     * @param string|null $value
     * @return AbstractRequest
     */
    public function setCcNo(?string $value): self
    {
        return $this->setParameter('ccno', $value);
    }

    /**
     * @param string|null $value
     * @return AbstractRequest
     */
    public function setExpDate(?string $value): self
    {
        return $this->setParameter('expdate', $value);
    }

    /**
     * @param string|null $value
     * @return AbstractRequest
     */
    public function setSupplier(?string $value): self
    {
        return $this->setParameter('supplier', $value);
    }

    /**
     * @return string|null
     */
    public function getTranzilaTK(): ?string
    {
        return $this->getParameter('TranzilaTK');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setTranzilaTK(?string $value): self
    {
        return $this->setParameter('TranzilaTK', $value);
    }

    /**
     * @return string|null
     */
    public function getTranzilaPK(): ?string
    {
        return $this->getParameter('TranzilaPK');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setTranzilaPK(?string $value): self
    {
        return $this->setParameter('TranzilaPK', $value);
    }

    /**
     * @return string|null
     */
    public function getTranMode(): ?string
    {
        return $this->getParameter('tranmode');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setTranMode(string $value): self
    {
        if (!in_array($value, ['V', 'F', 'C'], true)) {
            $value = 'V';
        }

        return $this->setParameter('tranmode', $value);
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setFpay(?string $value): self
    {
        return $this->setParameter('fpay', $value);
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSpay(?string $value): self
    {
        return $this->setParameter('spay', $value);
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setNpay(?string $value): self
    {
        return $this->setParameter('npay', $value);
    }

    /**
     * @return string|null
     */
    public function getAuthNr(): ?string
    {
        return $this->getParameter('authnr');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setAuthNr(?string $value): self
    {
        return $this->setParameter('authnr', $value);
    }

    /**
     * @return string|null
     */
    public function getIndex(): ?string
    {
        return $this->getParameter('index');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setIndex(?string $value): self
    {
        return $this->setParameter('index', $value);
    }
}
