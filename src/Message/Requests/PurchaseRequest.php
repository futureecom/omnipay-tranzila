<?php

namespace Omnipay\Tranzila\Message\Requests;

use Omnipay\Tranzila\Message\Responses\AbstractResponse;
use Omnipay\Tranzila\Message\Responses\Response;

/**
 * Class PurchaseRequest.
 */
class PurchaseRequest extends AbstractRequest
{
    /**
     * @inheritDoc
     * @return array{tranmode: string}
     */

    /**
     * @inheritDoc
     * @return array{terminal_name: string, txn_type: string, expire_month: int, expire_year: int, cvv: string, card_number: string, items: array<array{name: string, type: string, unit_price: float, units_number: int}>, txn_currency_code?: string, remarks?: string}
     */
    public function getData(): array
    {
        $this->validate('app_key', 'secret', 'terminal_name', 'amount', 'currency');
        $this->validateTokenOrCard();

        $item = [
            'name' => $this->getDescription() ?: 'Purchase',
            'type' => 'I',
            'unit_price' => (float) $this->getAmount(),
            'currency_code' => $this->getCurrency(),
            'units_number' => 1,
        ];

        $data = [
            'terminal_name' => $this->getTerminalName(),
            'txn_type' => 'debit',
            'items' => [ $item ],
        ];

        // If using a token, don't require card details
        if ($this->getToken()) {
            // Place token in card_number, and set random expiry/cvv
            $data['card_number'] = $this->getToken();
            $data['expire_month'] = random_int(1, 12);
            $data['expire_year'] = (int) date('Y') + random_int(1, 5);
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
                'Either token or card details must be provided for purchase'
            );
        }

        if ($hasToken && $hasCard) {
            throw new \Omnipay\Common\Exception\InvalidRequestException(
                'Cannot provide both token and card details for purchase'
            );
        }
    }

    protected function createResponse(string $data): AbstractResponse
    {
        return $this->response = new Response($this, $data);
    }
}
