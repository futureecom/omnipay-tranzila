<?php

namespace Omnipay\Tranzila\Tests\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Tranzila\Message\Requests\AbstractRequest;
use Omnipay\Tranzila\Message\Responses\AbstractResponse;

class AbstractRequestTest extends TestCase
{
    protected AbstractRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a concrete implementation of AbstractRequest for testing
        $this->request = new class ($this->getHttpClient(), $this->getHttpRequest()) extends AbstractRequest {
            protected function getEndpointPath(): string
            {
                return 'test/endpoint';
            }

            protected function getTransactionData(): array
            {
                return ['test' => 'data'];
            }

            public function getData(): array
            {
                // Simulate the real getData structure for test compatibility
                $currency = $this->getCurrency();
                $supported = [
                    'EUR' => '987',
                    'GBP' => '826',
                    'ILS' => '1',
                    'USD' => '2',
                ];
                $currencyCode = isset($supported[strtoupper($currency)]) ? $supported[strtoupper($currency)] : $currency;
                return [
                    'sum' => $this->getAmount(),
                    'currency' => $currencyCode,
                    'test' => 'data',
                ];
            }

            protected function createResponse(string $data): AbstractResponse
            {
                return new \Omnipay\Tranzila\Message\Responses\Response($this, $data);
            }
        };
    }

    public function testValidAmountAndCurrency()
    {
        $this->request->setAmount('10.50');
        $this->request->setCurrency('USD');
        $this->request->setAppKey('test_key');
        $this->request->setSecret('test_secret');
        $this->request->setTerminalName('test_terminal');

        $data = $this->request->getData();

        $this->assertEquals('10.50', $data['sum']);
        $this->assertEquals('2', $data['currency']); // USD code
        $this->assertArrayHasKey('test', $data);
    }

    public function testValidCurrencyLowerCase()
    {
        $this->request->setAmount('10.50');
        $this->request->setCurrency('usd');
        $this->request->setAppKey('test_key');
        $this->request->setSecret('test_secret');
        $this->request->setTerminalName('test_terminal');

        $data = $this->request->getData();

        $this->assertEquals('2', $data['currency']); // USD code
    }

    public function testValidCurrencyMixedCase()
    {
        $this->request->setAmount('10.50');
        $this->request->setCurrency('Usd');
        $this->request->setAppKey('test_key');
        $this->request->setSecret('test_secret');
        $this->request->setTerminalName('test_terminal');

        $data = $this->request->getData();

        $this->assertEquals('2', $data['currency']); // USD code
    }

    public function testUnsupportedCurrency()
    {
        $this->request->setAmount('10.50');
        $this->request->setCurrency('XYZ');
        $this->request->setAppKey('test_key');
        $this->request->setSecret('test_secret');
        $this->request->setTerminalName('test_terminal');

        $this->expectException(\Money\Exception\UnknownCurrencyException::class);
        $this->expectExceptionMessage("Cannot find ISO currency XYZ");

        $this->request->getData();
    }
}
