<?php

namespace Omnipay\Tranzila\Message\Responses;

use Omnipay\Common\Message\AbstractResponse as OmnipayAbstractResponse;

abstract class AbstractResponse extends OmnipayAbstractResponse
{
    public function isSuccessful()
    {
        // Check for error_code (should be 0 for success)
        $hasNoError = isset($this->data['error_code']) && $this->data['error_code'] === 0;

        // Check for processor_response_code (should be "000" for success)
        $hasSuccessfulProcessorCode = isset($this->data['transaction_result']['processor_response_code'])
                                     && $this->data['transaction_result']['processor_response_code'] === '000';

        // Both conditions must be met for success
        return $hasNoError && $hasSuccessfulProcessorCode;
    }

    public function getTransactionReference()
    {
        return isset($this->data['transaction_result']['transaction_id']) ? (string) $this->data['transaction_result']['transaction_id'] : null;
    }

    public function getMessage()
    {
        if ($this->isSuccessful()) {
            return $this->data['message'] ?? 'Transaction successful';
        }

        // Build comprehensive error message with both codes
        $errorParts = [];

        // Add gateway error code if present
        if (isset($this->data['error_code'])) {
            $errorParts[] = "gateway error code: {$this->data['error_code']}";
        }

        // Add processor response code if present
        $processorCode = $this->getCode();
        if ($processorCode) {
            $errorParts[] = "processor code: {$processorCode}";
        }

        if (!empty($errorParts)) {
            return "Transaction failed - " . implode(', ', $errorParts);
        }

        return 'Transaction failed';
    }

    public function getCode()
    {
        return $this->data['transaction_result']['processor_response_code'] ?? null;
    }

    public function getAuthorizationNumber()
    {
        return $this->data['transaction_result']['auth_number'] ?? null;
    }

    public function getToken()
    {
        return $this->data['transaction_result']['token'] ?? null;
    }
}
