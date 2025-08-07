<?php

namespace Omnipay\Tranzila\Message\Responses;

class VoidResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        // Parse JSON data if needed
        $data = is_string($this->data) ? json_decode($this->data, true) : $this->data;

        // For void responses, only check error_code (should be 0 for success)
        return isset($data['error_code']) && $data['error_code'] === 0;
    }

    public function getMessage()
    {
        // Parse JSON data if needed
        $data = is_string($this->data) ? json_decode($this->data, true) : $this->data;

        if ($this->isSuccessful()) {
            return $data['message'] ?? 'Transaction successful';
        }

        // For void failures, only show gateway error code
        if (isset($data['error_code'])) {
            return "Transaction failed - gateway error code: {$data['error_code']}";
        }

        return 'Transaction failed';
    }

    public function getCode()
    {
        // Void responses don't have processor response codes
        return null;
    }

    public function getTransactionReference()
    {
        // Void responses don't return transaction references
        return null;
    }

    public function getAuthorizationNumber()
    {
        // Void responses don't return authorization numbers
        return null;
    }

    public function getToken()
    {
        // Void responses don't return tokens
        return null;
    }
}
