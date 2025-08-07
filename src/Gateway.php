<?php

namespace Omnipay\Tranzila;

use Omnipay\Common\AbstractGateway;
use Omnipay\Tranzila\Message\Requests\AuthorizeRequest;
use Omnipay\Tranzila\Message\Requests\CaptureRequest;
use Omnipay\Tranzila\Message\Requests\HandshakeRequest;
use Omnipay\Tranzila\Message\Requests\PurchaseRequest;
use Omnipay\Tranzila\Message\Requests\RefundRequest;
use Omnipay\Tranzila\Message\Requests\ReversalRequest;
use Omnipay\Tranzila\Message\Requests\VerifyRequest;
use Omnipay\Tranzila\Message\Requests\VoidRequest;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Tranzila';
    }

    public function getDefaultParameters()
    {
        return [
            'app_key' => '',
            'secret' => '',
            'terminal_name' => '',
            'terminal_password' => '',
            'testMode' => false,
        ];
    }

    public function getAppKey()
    {
        return $this->getParameter('app_key');
    }

    public function setAppKey($value)
    {
        return $this->setParameter('app_key', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getTerminalName()
    {
        return $this->getParameter('terminal_name');
    }

    public function setTerminalName($value)
    {
        return $this->setParameter('terminal_name', $value);
    }

    public function getTerminalPassword()
    {
        return $this->getParameter('terminal_password');
    }

    public function setTerminalPassword($value)
    {
        return $this->setParameter('terminal_password', $value);
    }

    public function authorize(array $options = [])
    {
        return $this->createRequest(AuthorizeRequest::class, $options);
    }

    public function verify(array $options = [])
    {
        return $this->createRequest(VerifyRequest::class, $options);
    }

    public function purchase(array $options = [])
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    public function refund(array $options = [])
    {
        return $this->createRequest(RefundRequest::class, $options);
    }

    public function capture(array $options = [])
    {
        return $this->createRequest(CaptureRequest::class, $options);
    }

    public function void(array $options = [])
    {
        return $this->createRequest(VoidRequest::class, $options);
    }

    public function reversal(array $options = [])
    {
        return $this->createRequest(ReversalRequest::class, $options);
    }

    public function handshake(array $options = [])
    {
        return $this->createRequest(HandshakeRequest::class, $options);
    }
}
