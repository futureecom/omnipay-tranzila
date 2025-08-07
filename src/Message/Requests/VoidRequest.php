<?php

namespace Omnipay\Tranzila\Message\Requests;

use Omnipay\Tranzila\Message\Responses\AbstractResponse;
use Omnipay\Tranzila\Message\Responses\VoidResponse;

class VoidRequest extends AbstractRequest
{
    public function getData(): array
    {
        $this->validate('app_key', 'secret', 'terminal_name', 'transaction_reference');

        $data = [
            'terminal_name' => $this->getTerminalName(),
            'txn_type' => 'cancel',
            'reference_txn_id' => (int) $this->getTransactionReference(),
            'authorization_number' => (string) ($this->getAuthorizationNumber() ?: '0000000'),
            'response_language' => 'english',
        ];

        return $data;
    }

    protected function createResponse(string $data): AbstractResponse
    {
        return $this->response = new VoidResponse($this, $data);
    }
}
