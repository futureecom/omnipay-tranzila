<?php

namespace Futureecom\OmnipayTranzila\Message\Responses;

use Futureecom\OmnipayTranzila\Message\Requests\AbstractRequest;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Class RedirectResponse
 *
 * @property AbstractRequest request
 */
class RedirectResponse extends Response implements RedirectResponseInterface
{
    /**
     * RedirectResponse constructor.
     *
     * @param AbstractRequest $request
     */
    public function __construct(AbstractRequest $request)
    {
        parent::__construct($request, null);
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * @inheritDoc
     * @throws InvalidRequestException
     */
    public function getRedirectUrl(): string
    {
        return "https://direct.tranzila.com/{$this->request->getSupplier()}/iframe.php?" . $this->getUrlParams();
    }

    /**
     * @return string
     * @throws InvalidRequestException
     */
    private function getUrlParams(): string
    {
        $params = array_filter([
            'address' => $this->request->getAddress(),
            'city' => $this->request->getCity(),
            'company' => $this->request->getCompany(),
            'contact' => $this->request->getContact(),
            'cred_type' => $this->request->getCredType(),
            'currency' => $this->request->getCurrencyCode(),
            'email' => $this->request->getEmail(),
            'fax' => $this->request->getFax(),
            'myid' => $this->request->getMyID(),
            'oldprice' => $this->request->getOldPrice(),
            'pdesc' => $this->request->getPDesc(),
            'phone' => $this->request->getPhone(),
            'remarks' => $this->request->getRemarks(),
            'sum' => $this->request->getSum(),
            'TranzilaToken' => $this->request->getTranzilaToken(),
        ]);

        return http_build_query($params, '', '&');
    }
}
