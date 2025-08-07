<?php

namespace Omnipay\Tranzila\Message\Responses;

use Omnipay\Common\Message\RequestInterface;

class HandshakeResponse extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        // Store the raw response string
        parent::__construct($request, $data);
    }

    public function isSuccessful()
    {
        // If we have a token, consider it successful
        return $this->getHandshakeToken() !== null;
    }

    public function getMessage()
    {
        if ($this->isSuccessful()) {
            return 'Handshake successful';
        }

        if (empty($this->data)) {
            return 'Empty response from Tranzila';
        }

        return 'Invalid handshake response: ' . $this->data;
    }

    public function getCode()
    {
        if ($this->isSuccessful()) {
            return 'SUCCESS';
        }

        if (empty($this->data)) {
            return 'EMPTY_RESPONSE';
        }

        return 'INVALID_RESPONSE';
    }

    /**
     * Get the handshake token from the response (e.g., t3f88ef9db79d792b85c5dcdb3f52ba1e).
     */
    public function getHandshakeToken()
    {
        $data = trim($this->data);
        if (preg_match('/thtk=([a-zA-Z0-9]+)/', $data, $matches)) {
            $token = trim($matches[1]);
            // Accept any alphanumeric token
            if (preg_match('/^[a-zA-Z0-9]+$/', $token)) {
                return $token;
            }
        }
        return null;
    }

    /**
     * Get the full handshake token string including the prefix.
     */
    public function getFullHandshakeToken()
    {
        $token = $this->getHandshakeToken();
        return $token ? 'thtk=' . $token : null;
    }

    /**
     * Check if the response contains a valid handshake token format.
     */
    public function hasValidHandshakeTokenFormat()
    {
        return preg_match('/^thtk=[a-zA-Z0-9]+$/', trim($this->data));
    }

    /**
     * Get the handshake token length (useful for validation).
     */
    public function getHandshakeTokenLength()
    {
        $token = $this->getHandshakeToken();
        return $token ? strlen($token) : 0;
    }

    /**
     * Return the raw response string.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get a structured array representation of the response.
     */
    public function getResponseData()
    {
        return [
            'successful' => $this->isSuccessful(),
            'handshake_token' => $this->getHandshakeToken(),
            'full_handshake_token' => $this->getFullHandshakeToken(),
            'token_length' => $this->getHandshakeTokenLength(),
            'has_valid_format' => $this->hasValidHandshakeTokenFormat(),
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'raw_response' => $this->data,
        ];
    }
}
