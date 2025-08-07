<?php

namespace Omnipay\Tranzila\Message\Requests;

use Omnipay\Tranzila\Message\Responses\AbstractResponse;
use Omnipay\Tranzila\Message\Responses\Response;

class ReversalRequest extends AbstractRequest
{
    public function getData(): array
    {
        $this->validate('app_key', 'secret', 'terminal_name', 'amount', 'currency', 'transactionReference', 'card');

        $data = [
            'terminal_name' => $this->getTerminalName(),
            'txn_type' => 'reversal',
            'reference_txn_id' => (int) $this->getTransactionReference(),
            'authorization_number' => (string) ($this->getAuthorizationNumber() ?: '0000000'),
            'expire_month' => (int) $this->getCard()->getExpiryMonth(),
            'expire_year' => (int) $this->getCard()->getExpiryYear(),
            'cvv' => (string) $this->getCard()->getCvv(),
            'card_number' => (string) $this->getCard()->getNumber(),
            'items' => [
                [
                    'name' => $this->getDescription() ?: 'Reversal',
                    'type' => 'I',
                    'unit_price' => (float) $this->getAmount(),
                    'units_number' => 1,
                ],
            ],
        ];

        return $data;
    }

    protected function createResponse(string $data): AbstractResponse
    {
        return $this->response = new Response($this, $data);
    }
}
