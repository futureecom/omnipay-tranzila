<?php

namespace Futureecom\OmnipayTranzila\Message\Requests;

use Futureecom\OmnipayTranzila\Message\Responses\RedirectResponse;
use Futureecom\OmnipayTranzila\Message\Responses\Response;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Class AbstractRequest
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * @var string
     */
    protected const ENDPOINT = 'https://secure5.tranzila.com/cgi-bin/tranzila71u.cgi';

    /**
     * @var string
     */
    public const GLUE = '-';

    /**
     * @var array
     */
    protected static $supportedCurrencies = [
        'EUR' => '987',
        'GBP' => '826',
        'ILS' => '1',
        'USD' => '2',
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
    protected function prepareBody(array $data): string
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
     * @param string $value
     * @return $this
     */
    public function setCurrency($value): self
    {
        if ($value !== null) {
            $value = strtoupper($value);
        }

        if ($currency = array_search($value, static::$supportedCurrencies, true)) {
            $value = $currency;
        }

        return $this->setParameter('currency', $value);
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->getParameter('address');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setAddress(?string $value): self
    {
        return $this->setParameter('address', $value);
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->getParameter('city');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCity(?string $value): self
    {
        return $this->setParameter('city', $value);
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->getParameter('company');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCompany(?string $value): self
    {
        return $this->setParameter('company', $value);
    }

    /**
     * @return string|null
     */
    public function getContact(): ?string
    {
        return $this->getParameter('contact');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setContact(?string $value): self
    {
        return $this->setParameter('contact', $value);
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getParameter('email');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setEmail(?string $value): self
    {
        return $this->setParameter('email', $value);
    }

    /**
     * @return string|null
     */
    public function getFax(): ?string
    {
        return $this->getParameter('fax');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setFax(?string $value): self
    {
        return $this->setParameter('fax', $value);
    }

    /**
     * @return string|null
     */
    public function getOldPrice(): ?string
    {
        return $this->getParameter('oldprice');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setOldPrice(?string $value): self
    {
        return $this->setParameter('oldprice', $value);
    }

    /**
     * @return string|null
     */
    public function getPDesc(): ?string
    {
        return $this->getParameter('pdesc');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPDesc(?string $value): self
    {
        return $this->setParameter('pdesc', $value);
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->getParameter('phone');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPhone(?string $value): self
    {
        return $this->setParameter('phone', $value);
    }

    /**
     * @return string|null
     */
    public function getRemarks(): ?string
    {
        return $this->getParameter('remarks');
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setRemarks(?string $value): self
    {
        return $this->setParameter('remarks', $value);
    }

    /**
     * @return string|null
     */
    public function getTranzilaToken(): ?string
    {
        return $this->getTranzilaTK();
    }

    public function setTranzilaPW(?string $value): self
    {
        return $this->setParameter('TranzilaPW', $value);
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
    public function setTranzilaToken(?string $value): self
    {
        return $this->setTranzilaTK($value);
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

            // account data
            'supplier' => $this->getSupplier(),
            'TranzilaPW' => $this->getTranzilaPW() ?: null,

            // basic transaction data
            'currency' => $this->getCurrencyCode(),
            'orderId' => $this->getOrderId(),
            'sum' => $this->getAmount(),

            // credit card data
            'ccno' => $this->getCcNo(),
            'cred_type' => $this->getCredType(),
            'expdate' => $this->getExpDate(),
            'mycvv' => $this->getMyCVV(),

            //card token
            'TranzilaTK' => $this->getTranzilaTK(),

            // transaction with installments
            'fpay' => $this->getFpay(),
            'npay' => $this->getNpay(),
            'spay' => $this->getSpay(),

            // others...
            'index' => $this->getIndex(),
            'authnr' => $this->getAuthNr(),
            'CreditPass' => $this->getCreditPass(),
            'myid' => $this->getMyID(),
        ]);
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
    public function getTerminalPassword(): ?string
    {
        return $this->getTranzilaPW();
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
    public function getOrderId(): ?string
    {
        return $this->getParameter('orderId');
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
    public function getIndex(): ?string
    {
        return $this->getParameter('index');
    }

    /**
     * @return string|null
     */
    public function getAuthNr(): ?string
    {
        return $this->getParameter('authnr');
    }

    /**
     * @return string|null
     */
    public function getCreditPass(): ?string
    {
        return $this->getParameter('CreditPass');
    }

    /**
     * @return string|null
     */
    public function getMyID(): ?string
    {
        return $this->getParameter('myid');
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
    public function setTerminalPassword(?string $value): self
    {
        return $this->setTranzilaPW($value);
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
     * @param string|null $value
     * @return $this
     */
    public function setAuthNr(?string $value): self
    {
        return $this->setParameter('authnr', $value);
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

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCreditPass(?string $value): self
    {
        return $this->setParameter('CreditPass', $value);
    }

    /**
     * @param string ...$keys
     * @return bool
     * @noinspection PhpDocSignatureInspection
     */
    public function hasParameters(string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (!$this->getParameter($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string|null $value
     * @return self
     */
    public function setOrderId(?string $value): self
    {
        return $this->setParameter('orderId', $value);
    }

    /**
     * @return RedirectResponseInterface
     */
    protected function createRedirectResponse(): RedirectResponseInterface
    {
        return $this->response = new RedirectResponse($this);
    }

    /**
     * @return $this
     */
    public function getTranzilaPW(): ?string
    {
        return $this->getParameter('TranzilaPW');
    }
}
