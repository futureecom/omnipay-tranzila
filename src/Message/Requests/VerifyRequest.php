<?php

namespace Omnipay\Tranzila\Message\Requests;

use Omnipay\Tranzila\Message\Responses\AbstractResponse;
use Omnipay\Tranzila\Message\Responses\Response;

class VerifyRequest extends AbstractRequest
{
    public function getData(): array
    {
        $this->validate('app_key', 'secret', 'terminal_name', 'amount', 'currency', 'card');

        $timestamp = (string) round(microtime(true) * 1000);
        $nonce = bin2hex(random_bytes(32));
        $accessKey = hash_hmac('sha256', $this->getAppKey(), $this->getSecret() . $timestamp . $nonce);

        $card = $this->getCard();

        $data = [
            'terminal_name' => $this->getTerminalName(),
            'txn_type' => 'verify',
            'verify_mode' => 2,
            'payment_plan' => 1,
            'expire_month' => $card->getExpiryMonth(),
            'expire_year' => $card->getExpiryYear(),
            'cvv' => $card->getCvv(),
            'card_number' => $card->getNumber(),
            'items' => [
                [
                    'name' => $this->getDescription() ?: 'Verify',
                    'type' => 'I',
                    'unit_price' => (float) $this->getAmount(),
                    'currency_code' => $this->getCurrencyCode(),
                    'units_number' => 1,
                ],
            ],
            'response_language' => 'english',
        ];

        return $data;
    }

    protected function createResponse(string $data): AbstractResponse
    {
        return $this->response = new Response($this, $data);
    }
}
