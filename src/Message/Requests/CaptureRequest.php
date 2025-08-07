<?php

namespace Omnipay\Tranzila\Message\Requests;

use Omnipay\Tranzila\Message\Responses\AbstractResponse;
use Omnipay\Tranzila\Message\Responses\Response;

class CaptureRequest extends AbstractRequest
{
    public function getData(): array
    {
        $this->validate('app_key', 'secret', 'terminal_name', 'amount', 'currency', 'transaction_reference');

        $item = [
            'name' => $this->getDescription() ?: 'Capture',
            'type' => 'I',
            'unit_price' => (float) $this->getAmount(),
            'currency_code' => $this->getCurrency(),
            'units_number' => 1,
        ];
        $data = [
            'terminal_name' => $this->getTerminalName(),
            'txn_currency_code' => $this->getCurrency(),
            'txn_type' => 'force',
            'reference_txn_id' => (int) $this->getTransactionReference(),
            'authorization_number' => $this->getAuthorizationNumber() ?: '',
            'payment_plan' => 1,
            'items' => [$item],
            'response_language' => 'english',
        ];
        // Add card details if available (for capture, card details are optional)
        if ($this->getToken()) {
            // Place token in card_number, and set random expiry/cvv
            $data['card_number'] = $this->getToken();
            $data['expire_month'] = $this->getExpiryMonth();
            $data['expire_year'] = $this->getExpiryYear();
            $data['cvv'] = (string) random_int(100, 999);
        } elseif ($this->getCard()) {
            // Add card details if card is provided
            $data['expire_month'] = (int) $this->getCard()->getExpiryMonth();
            $data['expire_year'] = (int) $this->getCard()->getExpiryYear();
            $data['cvv'] = (string) $this->getCard()->getCvv();
            $data['card_number'] = (string) $this->getCard()->getNumber();
        }

        return $data;
    }

    protected function createResponse(string $data): AbstractResponse
    {
        return $this->response = new Response($this, $data);
    }
}
