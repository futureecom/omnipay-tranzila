<?php

namespace Omnipay\Tranzila\Message\Requests;

use JsonException;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Tranzila\Message\Responses\AbstractResponse;

/**
 * Class AbstractRequest.
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * @var string
     */
    protected const BASE_URL = 'https://api.tranzila.com/v1';

    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @inheritDoc
     */
    public function __construct(ClientInterface $httpClient, $httpRequest)
    {
        $this->httpClient = $httpClient;
        parent::__construct($httpClient, $httpRequest);
    }

    /**
     * @inheritDoc
     */
    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @inheritDoc
     */
    public function sendData($data): AbstractResponse
    {
        $timestamp = time();
        $nonce = bin2hex(random_bytes(32));
        $accessKey = hash_hmac('sha256', $this->getAppKey(), $this->getSecret() . $timestamp . $nonce);

        $headers = [
            'X-tranzila-api-app-key' => $this->getAppKey(),
            'X-tranzila-api-request-time' => (string) $timestamp,
            'X-tranzila-api-nonce' => $nonce,
            'X-tranzila-api-access-token' => $accessKey,
            'Content-Type' => 'application/json',
        ];

        try {
            $httpResponse = $this->httpClient->request(
                $this->getHttpMethod(),
                $this->getEndpoint(),
                $headers,
                $this->prepareBody($data)
            );
        } catch (JsonException $e) {
            throw new InvalidRequestException("Invalid fields sent - request data could not be encoded as JSON");
        }
        try {
            return $this->createResponse($httpResponse->getBody()->getContents());
        } catch (JsonException $e) {
            throw new InvalidResponseException(sprintf("Invalid response sent. HTTP Status Code: %s; HTTP Reason: %s", $httpResponse->getStatusCode(), $httpResponse->getReasonPhrase()));
        }
    }

    public function getHttpMethod(): string
    {
        return 'POST';
    }

    /**
     * @inheritDoc
     */
    public function getEndpoint(): string
    {
        return static::BASE_URL . '/transaction/credit_card/create';
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return [
            'content-type' => 'application/json',
        ];
    }

    /**
     * @inheritDoc
     * @throws InvalidRequestException
     */
    abstract public function getData(): array;

    public function getTerminalName(): ?string
    {
        return $this->getParameter('terminal_name');
    }

    public function getOrderId(): ?string
    {
        return $this->getParameter('order_id');
    }

    public function getCcNo(): ?string
    {
        if (($card = $this->getCard()) && ($number = $card->getNumber())) {
            return $number;
        }

        return $this->getParameter('cc_no');
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

        return $this->getParameter('exp_date');
    }

    public function getMyCVV(): ?string
    {
        if (($card = $this->getCard()) && ($cvv = $card->getCvv())) {
            return $cvv;
        }

        return $this->getParameter('my_cvv');
    }

    public function setTerminalPassword(?string $value): self
    {
        return $this->setParameter('terminal_password', $value);
    }

    /**
     * @return $this
     */
    public function setMyCVV(?string $value): self
    {
        return $this->setParameter('my_cvv', $value);
    }

    public function setMyID(?string $value): self
    {
        return $this->setParameter('my_id', $value);
    }

    public function setCredType(?string $value): self
    {
        return $this->setParameter('cred_type', $value);
    }

    public function setCcNo(?string $value): self
    {
        return $this->setParameter('cc_no', $value);
    }

    public function setExpDate(?string $value): self
    {
        return $this->setParameter('exp_date', $value);
    }

    public function setTerminalName(?string $value): self
    {
        return $this->setParameter('terminal_name', $value);
    }

    public function getAuthorizationNumber(): ?string
    {
        return $this->getParameter('authorization_number');
    }

    public function setAuthorizationNumber(?string $value): self
    {
        return $this->setParameter('authorization_number', $value);
    }

    public function getToken(): ?string
    {
        return $this->getParameter('token');
    }

    public function setToken($value): self
    {
        return $this->setParameter('token', $value);
    }

    public function getVerifyMode(): ?int
    {
        return $this->getParameter('verify_mode');
    }

    public function setVerifyMode(?int $value): self
    {
        return $this->setParameter('verify_mode', $value);
    }

    public function getDescription(): ?string
    {
        return $this->getParameter('description');
    }

    public function setDescription($value): self
    {
        return $this->setParameter('description', $value);
    }

    public function getTxnCurrencyCode(): ?string
    {
        return $this->getParameter('txn_currency_code');
    }

    public function setTxnCurrencyCode(?string $value): self
    {
        return $this->setParameter('txn_currency_code', $value);
    }

    public function getTxnType(): ?string
    {
        return $this->getParameter('txn_type');
    }

    public function setTxnType(?string $value): self
    {
        return $this->setParameter('txn_type', $value);
    }

    public function getExpiryMonth(): ?int
    {
        return $this->getParameter('expiry_month');
    }

    public function setExpiryMonth(?int $value): self
    {
        return $this->setParameter('expiry_month', $value);
    }

    public function getExpiryYear(): ?int
    {
        return $this->getParameter('expiry_year');
    }

    public function setExpiryYear(?int $value): self
    {
        return $this->setParameter('expiry_year', $value);
    }

    public function getCvv(): ?string
    {
        return $this->getParameter('cvv');
    }

    public function setCvv(?string $value): self
    {
        return $this->setParameter('cvv', $value);
    }

    public function getCardHolderId(): ?int
    {
        return $this->getParameter('card_holder_id');
    }

    public function setCardHolderId(?int $value): self
    {
        return $this->setParameter('card_holder_id', $value);
    }

    public function getCardNumber(): ?string
    {
        return $this->getParameter('card_number');
    }

    public function setCardNumber(?string $value): self
    {
        return $this->setParameter('card_number', $value);
    }

    public function getPaymentPlan(): ?int
    {
        return $this->getParameter('payment_plan');
    }

    public function setPaymentPlan(?int $value): self
    {
        return $this->setParameter('payment_plan', $value);
    }

    public function getInstallmentsNumber(): ?int
    {
        return $this->getParameter('installments_number');
    }

    public function setInstallmentsNumber(?int $value): self
    {
        return $this->setParameter('installments_number', $value);
    }

    public function getFirstInstallmentAmount(): ?float
    {
        return $this->getParameter('first_installment_amount');
    }

    public function setFirstInstallmentAmount(?float $value): self
    {
        return $this->setParameter('first_installment_amount', $value);
    }

    public function getOtherInstallmentsAmount(): ?float
    {
        return $this->getParameter('other_installments_amount');
    }

    public function setOtherInstallmentsAmount(?float $value): self
    {
        return $this->setParameter('other_installments_amount', $value);
    }

    public function getReferenceTxnId(): ?int
    {
        return $this->getParameter('reference_txn_id');
    }

    public function setReferenceTxnId(?int $value): self
    {
        return $this->setParameter('reference_txn_id', $value);
    }

    public function getClientExternalId(): ?string
    {
        return $this->getParameter('client_external_id');
    }

    public function setClientExternalId(?string $value): self
    {
        return $this->setParameter('client_external_id', $value);
    }

    public function getClientName(): ?string
    {
        return $this->getParameter('client_name');
    }

    public function setClientName(?string $value): self
    {
        return $this->setParameter('client_name', $value);
    }

    public function getClientContactPerson(): ?string
    {
        return $this->getParameter('client_contact_person');
    }

    public function setClientContactPerson(?string $value): self
    {
        return $this->setParameter('client_contact_person', $value);
    }

    public function getClientId(): ?int
    {
        return $this->getParameter('client_id');
    }

    public function setClientId(?int $value): self
    {
        return $this->setParameter('client_id', $value);
    }

    public function getClientEmail(): ?string
    {
        return $this->getParameter('client_email');
    }

    public function setClientEmail(?string $value): self
    {
        return $this->setParameter('client_email', $value);
    }

    public function getClientPhoneCountryCode(): ?string
    {
        return $this->getParameter('client_phone_country_code');
    }

    public function setClientPhoneCountryCode(?string $value): self
    {
        return $this->setParameter('client_phone_country_code', $value);
    }

    public function getClientPhoneAreaCode(): ?string
    {
        return $this->getParameter('client_phone_area_code');
    }

    public function setClientPhoneAreaCode(?string $value): self
    {
        return $this->setParameter('client_phone_area_code', $value);
    }

    public function getClientPhoneNumber(): ?string
    {
        return $this->getParameter('client_phone_number');
    }

    public function setClientPhoneNumber(?string $value): self
    {
        return $this->setParameter('client_phone_number', $value);
    }

    public function getClientAddressLine1(): ?string
    {
        return $this->getParameter('client_address_line_1');
    }

    public function setClientAddressLine1(?string $value): self
    {
        return $this->setParameter('client_address_line_1', $value);
    }

    public function getClientAddressLine2(): ?string
    {
        return $this->getParameter('client_address_line_2');
    }

    public function setClientAddressLine2(?string $value): self
    {
        return $this->setParameter('client_address_line_2', $value);
    }

    public function getClientCity(): ?string
    {
        return $this->getParameter('client_city');
    }

    public function setClientCity(?string $value): self
    {
        return $this->setParameter('client_city', $value);
    }

    public function getClientCountryCode(): ?string
    {
        return $this->getParameter('client_country_code');
    }

    public function setClientCountryCode(?string $value): self
    {
        return $this->setParameter('client_country_code', $value);
    }

    public function getClientZip(): ?string
    {
        return $this->getParameter('client_zip');
    }

    public function setClientZip(?string $value): self
    {
        return $this->setParameter('client_zip', $value);
    }

    public function getItems(): ?array
    {
        return $this->getParameter('items');
    }

    public function setItems($value): self
    {
        return $this->setParameter('items', $value);
    }

    public function getUserDefinedFields(): ?array
    {
        return $this->getParameter('user_defined_fields');
    }

    public function setUserDefinedFields(?array $value): self
    {
        return $this->setParameter('user_defined_fields', $value);
    }

    public function getResponseLanguage(): ?string
    {
        return $this->getParameter('response_language');
    }

    public function setResponseLanguage(?string $value): self
    {
        return $this->setParameter('response_language', $value);
    }

    public function getCreatedByUser(): ?string
    {
        return $this->getParameter('created_by_user');
    }

    public function setCreatedByUser(?string $value): self
    {
        return $this->setParameter('created_by_user', $value);
    }

    public function getCreatedBySystem(): ?string
    {
        return $this->getParameter('created_by_system');
    }

    public function setCreatedBySystem(?string $value): self
    {
        return $this->setParameter('created_by_system', $value);
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
    public function setSum(?string $value): self
    {
        return $this->setParameter('sum', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTransactionReference($value): self
    {
        return $this->setParameter('transaction_reference', $value);
    }

    public function getTransactionReference(): ?string
    {
        return $this->getParameter('transaction_reference');
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
        return $this->setParameter('order_id', $value);
    }

    public function getTerminalPassword(): ?string
    {
        return $this->getParameter('terminal_password');
    }

    public function getAppKey(): ?string
    {
        return $this->getParameter('app_key');
    }

    public function setAppKey(?string $value): self
    {
        return $this->setParameter('app_key', $value);
    }

    public function getSecret(): ?string
    {
        return $this->getParameter('secret');
    }

    public function setSecret(?string $value): self
    {
        return $this->setParameter('secret', $value);
    }

    /**
     * @inheritDoc
     */
    public function getCard(): ?CreditCard
    {
        return $this->getParameter('card');
    }

    /**
     * @inheritDoc
     */
    public function setCard($value): self
    {
        return $this->setParameter('card', $value);
    }

    protected function getTransactionData(): array
    {
        return $this->getData();
    }

    /**
     * @param array<string, mixed> $data
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    protected function prepareBody($data): string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \Omnipay\Common\Exception\InvalidRequestException(
                'Failed to encode request data as JSON: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Create a response object from the raw response data.
     *
     * @param string $data The raw response data
     * @return AbstractResponse A concrete response instance
     */
    abstract protected function createResponse(string $data): AbstractResponse;

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultParameters(): array
    {
        return [
            'terminal_name' => $this->getTerminalName(),
            'terminal_password' => $this->getTerminalPassword(),
            'app_key' => $this->getAppKey(),
            // 'secret' removed for security
        ];
    }
}
