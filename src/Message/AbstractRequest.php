<?php

namespace Futureecom\OmnipayTranzila\Message;

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
    public const GLUE = '-';

    /**
     * @var string
     */
    protected const ENDPOINT = 'https://secure5.tranzila.com/cgi-bin/tranzila71u.cgi';

    /**
     * @var array
     */
    protected static $supportedCurrencies = [
        'EUR' => 987,
        'GBP' => 826,
        'ILS' => 1,
        'USD' => 2,
    ];

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
        return static::ENDPOINT;
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

        if ($code = static::$supportedCurrencies[$currency] ?? false) {
            return $code;
        }

        throw new InvalidRequestException("Unsupported '{$currency}' currency.");
    }

    /**
     * @return string|null
     */
    public function getCcNo(): ?string
    {
        if (($card = $this->getCard()) && ($number = $card->getNumber())) {
            return $number;
        }

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
        if (($card = $this->getCard()) && ($expDate = $card->getExpiryDate('my'))) {
            return $expDate;
        }

        return $this->getParameter('expdate');
    }

    /**
     * @return string|null
     */
    public function getMyCVV(): ?string
    {
        if (($card = $this->getCard()) && ($cvv = $card->getCvv())) {
            return $cvv;
        }

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

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSum(?string $value): self
    {
        return $this->setAmount($value);
    }

    /**
     * @return string|null
     * @throws InvalidRequestException
     */
    public function getSum(): ?string
    {
        return $this->getAmount();
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionReference($value): self
    {
        if (is_string($value)) {
            $arr = explode(static::GLUE, $value);

            if (count($arr) === 2) {
                $this->setIndex($arr[0])->setAuthNr($arr[1]);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        $arr = array_filter([$this->getIndex(), $this->getAuthNr()]);

        if (count($arr) < 2) {
            return null;
        }

        return implode(static::GLUE, $arr);
    }
}
