<?php

namespace Omnipay\Tranzila\Message\Requests;

use Omnipay\Tranzila\Message\Responses\AbstractResponse;
use Omnipay\Tranzila\Message\Responses\Response;

/**
 * Class RefundRequest.
 */
class RefundRequest extends AbstractRequest
{
    public function getData(): array
    {
        $this->validate('app_key', 'secret', 'terminal_name', 'amount', 'currency');
        $this->validateRefundRequirements();

        $data = [
            'terminal_name' => $this->getTerminalName(),
            'txn_type' => 'credit',
            'reference_txn_id' => (int) $this->getTransactionReference(),
            'items' => [
                [
                    'name' => $this->getDescription() ?: 'Refund',
                    'type' => 'I',
                    'unit_price' => (float) $this->getAmount(),
                    'currency_code' => $this->getCurrency(),
                    'units_number' => 1,
                ],
            ],
        ];

        // Option 1: Authorization number + transaction reference
        if ($this->getAuthorizationNumber()) {
            $data['authorization_number'] = $this->getAuthorizationNumber();
        }
        // Option 2: Token as card number + expiry + cvv
        elseif ($this->getToken()) {
            // Place token in card_number, and set expiry/cvv
            $data['card_number'] = $this->getToken();
            $data['expire_month'] = (int) $this->getExpiryMonth();
            $data['expire_year'] = (int) $this->getExpiryYear();
            $data['cvv'] = (string) random_int(100, 999);
        }
        // Option 3: Regular credit card number + expiry + cvv
        elseif ($this->getCard()) {
            $data['card_number'] = (string) $this->getCard()->getNumber();
            $data['expire_month'] = (int) $this->getCard()->getExpiryMonth();
            $data['expire_year'] = (int) $this->getCard()->getExpiryYear();
            $data['cvv'] = (string) $this->getCard()->getCvv();
        }

        return $data;
    }

    /**
     * Validate that one of the required refund methods is provided:
     * 1. Authorization number + transaction reference
     * 2. Token (as card number) + expiry + cvv
     * 3. Regular credit card number + expiry + cvv
     */
    protected function validateRefundRequirements()
    {
        $hasAuthNumber = !empty($this->getAuthorizationNumber());
        $hasTransactionReference = !empty($this->getTransactionReference());
        $hasToken = !empty($this->getToken());
        $hasCard = $this->getCard() !== null;

        // Option 1: Authorization number + transaction reference
        if ($hasAuthNumber && $hasTransactionReference) {
            return; // Valid - authorization number + transaction reference
        }

        // Option 2: Token + expiry
        if ($hasToken) {
            // When using token, we need expiry information
            if (!$this->getExpiryMonth() || !$this->getExpiryYear()) {
                throw new \Omnipay\Common\Exception\InvalidRequestException(
                    'Token provided but expiry month/year is missing'
                );
            }
            return; // Valid - token will be used as card number with expiry
        }

        // Option 3: Full card details
        if ($hasCard) {
            return; // Valid - full card details provided
        }

        // Check for incomplete authorization number scenario
        if ($hasAuthNumber && !$hasTransactionReference) {
            throw new \Omnipay\Common\Exception\InvalidRequestException(
                'Authorization number provided but transaction reference is missing'
            );
        }

        // None of the valid options provided
        throw new \Omnipay\Common\Exception\InvalidRequestException(
            'Refund requires one of: (1) authorization number + transaction reference, (2) token as card number + expiry + cvv, or (3) regular credit card number + expiry + cvv'
        );
    }

    protected function createResponse(string $data): AbstractResponse
    {
        return $this->response = new Response($this, $data);
    }
}
