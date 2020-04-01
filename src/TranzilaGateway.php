<?php

namespace Futureecom\OmnipayTranzila;

use Futureecom\OmnipayTranzila\Message\Requests\AuthorizeRequest;
use Futureecom\OmnipayTranzila\Message\Requests\CaptureRequest;
use Futureecom\OmnipayTranzila\Message\Requests\CompletePurchaseRequest;
use Futureecom\OmnipayTranzila\Message\Requests\PurchaseRequest;
use Futureecom\OmnipayTranzila\Message\Requests\RefundRequest;
use Futureecom\OmnipayTranzila\Message\Requests\VoidRequest;
use Omnipay\Common\AbstractGateway;
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
     */
    public function getDefaultParameters()
    {
        return [
            'supplier' => $this->getSupplier(),
            'passw' => $this->getPassW(),
        ];
    }

    /**
     * @return string|null
     */
    public function getSupplier(): ?string
    {
        return $this->getParameter('supplier');
    }

    /**
     * @return string|null
     */
    public function getPassW(): ?string
    {
        return $this->getParameter('passw');
    }

    /**
     * @inheritDoc
     */
    public function authorize(array $options = []): RequestInterface
    {
        return $this->createRequest(AuthorizeRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function capture(array $options = []): RequestInterface
    {
        return $this->createRequest(CaptureRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function completePurchase(array $options = []): RequestInterface
    {
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function purchase(array $options = []): RequestInterface
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function refund(array $options = []): RequestInterface
    {
        return $this->createRequest(RefundRequest::class, $options);
    }

    /**
     * @inheritDoc
     */
    public function void(array $options = array()): RequestInterface
    {
        return $this->createRequest(VoidRequest::class, $options);
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setSupplier(?string $value): self
    {
        return $this->setParameter('supplier', $value);
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setPassW(?string $value): self
    {
        return $this->setParameter('passw', $value);
    }
}
