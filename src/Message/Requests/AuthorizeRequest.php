<?php

namespace Omnipay\Tranzila\Message\Requests;

use Omnipay\Tranzila\Message\Responses\AbstractResponse;
use Omnipay\Tranzila\Message\Responses\Response;

class AuthorizeRequest extends AbstractRequest
{
    public function getData(): array
    {
        $this->validate('app_key', 'secret', 'terminal_name', 'amount', 'currency');
        $this->validateTokenOrCard();

        $item = [
            'name' => $this->getDescription() ?: 'Authorization',
            'type' => 'I',
            'unit_price' => (float) $this->getAmount(),
            'currency_code' => $this->getCurrency(),
            'units_number' => 1,
        ];

        $data = [
            'terminal_name' => $this->getTerminalName(),
            'txn_type' => 'verify',
            'verify_mode' => $this->getVerifyMode() ?: 5,
            'items' => [ $item ],
        ];

        // If using a token, don't require card details
        if ($this->getToken()) {
            // Place token in card_number, and set random expiry/cvv
            $data['card_number'] = $this->getToken();
            $data['expire_month'] = $this->getExpiryMonth();
            $data['expire_year'] = $this->getExpiryYear();
            $data['cvv'] = (string) random_int(100, 999);
        } else {
            // Require card details when not using token
            $this->validate('card');
            $data['expire_month'] = (int) $this->getCard()->getExpiryMonth();
            $data['expire_year'] = (int) $this->getCard()->getExpiryYear();
            $data['cvv'] = (string) $this->getCard()->getCvv();
            $data['card_number'] = (string) $this->getCard()->getNumber();
        }

        return $data;
    }

    /**
     * Validate that either token or card details are provided, but not both.
     */
    protected function validateTokenOrCard()
    {
        $hasToken = !empty($this->getToken());
        $hasCard = $this->getCard() !== null;

        if (!$hasToken && !$hasCard) {
            throw new \Omnipay\Common\Exception\InvalidRequestException(
                'Either token or card details must be provided for authorization'
            );
        }

        if ($hasToken && $hasCard) {
            throw new \Omnipay\Common\Exception\InvalidRequestException(
                'Cannot provide both token and card details for authorization'
            );
        }
    }

    protected function createResponse(string $data): AbstractResponse
    {
        return $this->response = new Response($this, $data);
    }
}
