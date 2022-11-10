<?php

namespace Futureecom\OmnipayTranzila;

use Futureecom\OmnipayTranzila\Message\Requests\AuthorizeRequest;
use Futureecom\OmnipayTranzila\Message\Requests\CaptureRequest;
use Futureecom\OmnipayTranzila\Message\Requests\PurchaseRequest;
use Futureecom\OmnipayTranzila\Message\Requests\RefundRequest;
use Futureecom\OmnipayTranzila\Message\Requests\VoidRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RequestInterface;

/**
 * Class TranzilaGateway
 *
 * @noinspection PhpHierarchyChecksInspection
 */
class TranzilaGateway extends AbstractGateway
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'tranzila';
    }

    /**
     * @inheritDoc
     * @return array{supplier: string|null, terminal_password: string|null}
     */
    public function getDefaultParameters(): array
    {
        return [
            'supplier' => $this->getSupplier(),
            'terminal_password' => $this->getTerminalPassword(),
        ];
    }

    /**
     * @return $this
     */
    public function setSupplier(?string $value): self
    {
        return $this->setParameter('supplier', $value);
    }

    public function getSupplier(): ?string
    {
        return $this->getParameter('supplier');
    }

    /**
     * @return $this
     */
    public function setTerminalPassword(?string $value): self
    {
        return $this->setParameter('terminal_password', $value);
    }

    public function getTerminalPassword(): ?string
    {
        return $this->getParameter('terminal_password');
    }

    /**
     * @inheritDoc
     */
    public function authorize(array $options = []): AbstractRequest
    {
        return $this->createRequest(AuthorizeRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function capture(array $options = []): AbstractRequest
    {
        return $this->createRequest(CaptureRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function purchase(array $options = []): AbstractRequest
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function refund(array $options = []): AbstractRequest
    {
        return $this->createRequest(RefundRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function void(array $options = []): AbstractRequest
    {
        return $this->createRequest(VoidRequest::class, $options);
    }
}
