<?php

namespace Futureecom\OmnipayTranzila\Message\Requests;

use Futureecom\OmnipayTranzila\Message\Responses\RedirectResponse;
use Futureecom\OmnipayTranzila\Message\Responses\Response;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Class AbstractRequest.
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * @var string
     */
    final public const GLUE = '-';

    /**
     * @var string
     */
    protected const ENDPOINT = 'https://secure5.tranzila.com/cgi-bin/tranzila71u.cgi';

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

    public function getHttpMethod(): string
    {
        return 'POST';
    }

    public function getEndpoint(): string
    {
        return static::ENDPOINT;
    }

    public function getHeaders(): array
    {
        return [
            'content-type' => 'application/x-www-form-urlencoded',
        ];
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

    public function getAddress(): ?string
    {
        return $this->getParameter('address');
    }

    /**
     * @return $this
     */
    public function setAddress(?string $value): self
    {
        return $this->setParameter('address', $value);
    }

    public function getCity(): ?string
    {
        return $this->getParameter('city');
    }

    /**
     * @return $this
     */
    public function setCity(?string $value): self
    {
        return $this->setParameter('city', $value);
    }

    public function getCompany(): ?string
    {
        return $this->getParameter('company');
    }

    /**
     * @return $this
     */
    public function setCompany(?string $value): self
    {
        return $this->setParameter('company', $value);
    }

    public function getContact(): ?string
    {
        return $this->getParameter('contact');
    }

    /**
     * @return $this
     */
    public function setContact(?string $value): self
    {
        return $this->setParameter('contact', $value);
    }

    public function getEmail(): ?string
    {
        return $this->getParameter('email');
    }

    /**
     * @return $this
     */
    public function setEmail(?string $value): self
    {
        return $this->setParameter('email', $value);
    }

    public function getFax(): ?string
    {
        return $this->getParameter('fax');
    }

    /**
     * @return $this
     */
    public function setFax(?string $value): self
    {
        return $this->setParameter('fax', $value);
    }

    public function getOldPrice(): ?string
    {
        return $this->getParameter('oldprice');
    }

    /**
     * @return $this
     */
    public function setOldPrice(?string $value): self
    {
        return $this->setParameter('oldprice', $value);
    }

    public function getPDesc(): ?string
    {
        return $this->getParameter('pdesc');
    }

    /**
     * @return $this
     */
    public function setPDesc(?string $value): self
    {
        return $this->setParameter('pdesc', $value);
    }

    public function getPhone(): ?string
    {
        return $this->getParameter('phone');
    }

    /**
     * @return $this
     */
    public function setPhone(?string $value): self
    {
        return $this->setParameter('phone', $value);
    }

    public function getRemarks(): ?string
    {
        return $this->getParameter('remarks');
    }

    /**
     * @return $this
     */
    public function setRemarks(?string $value): self
    {
        return $this->setParameter('remarks', $value);
    }

    public function getTranzilaToken(): ?string
    {
        return $this->getTranzilaTK();
    }

    public function setTranzilaPW(?string $value): self
    {
        return $this->setParameter('TranzilaPW', $value);
    }

    public function getTranzilaTK(): ?string
    {
        return $this->getParameter('TranzilaTK');
    }

    /**
     * @return $this
     */
    public function setTranzilaToken(?string $value): self
    {
        return $this->setTranzilaTK($value);
    }

    /**
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

    public function getSupplier(): ?string
    {
        return $this->getParameter('supplier');
    }

    public function getTerminalPassword(): ?string
    {
        return $this->getTranzilaPW();
    }

    /**
     * @throws InvalidRequestException
     */
    public function getCurrencyCode(): ?string
    {
        $currency = $this->getCurrency();
        if ($currency === '' || $currency === null) {
            return null;
        }

        if ($code = static::$supportedCurrencies[$currency] ?? false) {
            return $code;
        }

        throw new InvalidRequestException("Unsupported '$currency' currency.");
    }

    public function getOrderId(): ?string
    {
        return $this->getParameter('orderId');
    }

    public function getCcNo(): ?string
    {
        if (($card = $this->getCard()) && ($number = $card->getNumber())) {
            return $number;
        }

        return $this->getParameter('ccno');
    }

    public function getCredType(): ?string
    {
        return $this->getParameter('cred_type');
    }

    public function getExpDate(): ?string
    {
        if (($card = $this->getCard()) && ($expDate = $card->getExpiryDate('my'))) {
            return $expDate;
        }

        return $this->getParameter('expdate');
    }

    public function getMyCVV(): ?string
    {
        if (($card = $this->getCard()) && ($cvv = $card->getCvv())) {
            return $cvv;
        }

        return $this->getParameter('mycvv');
    }

    public function getFpay(): ?string
    {
        return $this->getParameter('fpay');
    }

    public function getNpay(): ?string
    {
        return $this->getParameter('npay');
    }

    public function getSpay(): ?string
    {
        return $this->getParameter('spay');
    }

    public function getIndex(): ?string
    {
        return $this->getParameter('index');
    }

    public function getAuthNr(): ?string
    {
        return $this->getParameter('authnr');
    }

    public function getCreditPass(): ?string
    {
        return $this->getParameter('CreditPass');
    }

    public function getMyID(): ?string
    {
        return $this->getParameter('myid');
    }

    public function setTerminalPassword(?string $value): self
    {
        return $this->setTranzilaPW($value);
    }

    /**
     * @return $this
     */
    public function setMyCVV(?string $value): self
    {
        return $this->setParameter('mycvv', $value);
    }

    public function setMyID(?string $value): self
    {
        return $this->setParameter('myid', $value);
    }

    public function setCredType(?string $value): self
    {
        return $this->setParameter('cred_type', $value);
    }

    public function setCcNo(?string $value): self
    {
        return $this->setParameter('ccno', $value);
    }

    public function setExpDate(?string $value): self
    {
        return $this->setParameter('expdate', $value);
    }

    public function setSupplier(?string $value): self
    {
        return $this->setParameter('supplier', $value);
    }

    public function getTranzilaPK(): ?string
    {
        return $this->getParameter('TranzilaPK');
    }

    /**
     * @return $this
     */
    public function setTranzilaPK(?string $value): self
    {
        return $this->setParameter('TranzilaPK', $value);
    }

    /**
     * @return $this
     */
    public function setFpay(?string $value): self
    {
        return $this->setParameter('fpay', $value);
    }

    /**
     * @return $this
     */
    public function setSpay(?string $value): self
    {
        return $this->setParameter('spay', $value);
    }

    /**
     * @return $this
     */
    public function setNpay(?string $value): self
    {
        return $this->setParameter('npay', $value);
    }

    /**
     * @return $this
     */
    public function setSum(?string $value): self
    {
        return $this->setAmount($value);
    }

    /**
     * @throws InvalidRequestException
     */
    public function getSum(): string
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
     * @return $this
     */
    public function setAuthNr(?string $value): self
    {
        return $this->setParameter('authnr', $value);
    }

    /**
     * @return $this
     */
    public function setIndex(?string $value): self
    {
        return $this->setParameter('index', $value);
    }

    public function getTransactionReference(): ?string
    {
        $arr = array_filter([$this->getIndex(), $this->getAuthNr()]);

        if (count($arr) < 2) {
            return null;
        }

        return implode(static::GLUE, $arr);
    }

    /**
     * @return $this
     */
    public function setCreditPass(?string $value): self
    {
        return $this->setParameter('CreditPass', $value);
    }

    public function hasParameters(string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (!$this->getParameter($key)) {
                return false;
            }
        }

        return true;
    }

    public function setOrderId(?string $value): self
    {
        return $this->setParameter('orderId', $value);
    }

    public function getTranzilaPW(): ?string
    {
        return $this->getParameter('TranzilaPW');
    }

    protected function prepareBody(array $data): string
    {
        return http_build_query($data, '', '&');
    }

    protected function createResponse(string $content): Response
    {
        return $this->response = new Response($this, $content);
    }

    /**
     * @throws InvalidRequestException
     * @return array{response_return_format: string, supplier?: string, TranzilaPW?: string, currency?: string, orderId?: string, sum?: string, ccno?: string, cred_type?: string, expdate?: string, mycvv?: string, TranzilaTK?: string, fpay?: string, npay?: string, spay?: string, index?: string, authnr?: string, CreditPass?: string, myid?: string}
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
     * Return transaction data specified to given transaction.
     */
    abstract protected function getTransactionData(): array;

    protected function createRedirectResponse(): RedirectResponse
    {
        return $this->response = new RedirectResponse($this);
    }
}
