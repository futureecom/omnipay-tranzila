<?php

namespace Omnipay\Tranzila\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\ResponseInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Request;

class TestCase extends BaseTestCase
{
    protected function getMockHttpClient($responses = [])
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        return new Client(['handler' => $handler]);
    }

    protected function getMockHttpResponse($filename)
    {
        $path = __DIR__ . '/Mock/' . $filename;
        $content = file_get_contents($path);

        // Split the content into headers and body
        list($headers, $body) = explode("\n\n", $content, 2);

        // Parse status line and headers
        $headerLines = explode("\n", $headers);
        $statusLine = array_shift($headerLines);
        $statusCode = (int) substr($statusLine, 9, 3);

        $headers = [];
        foreach ($headerLines as $line) {
            if (strpos($line, ':') !== false) {
                list($name, $value) = explode(':', $line, 2);
                $headers[trim($name)] = trim($value);
            }
        }

        // Ensure the body is properly formatted JSON
        $body = trim($body);
        if (json_decode($body) === null) {
            throw new \InvalidArgumentException("Invalid JSON in mock response: $filename");
        }

        return new Response($statusCode, $headers, $body);
    }

    protected function getMockRequest()
    {
        return Request::create('http://localhost', 'POST');
    }

    protected function assertRequest(RequestInterface $request, $expectedData)
    {
        $data = $request->getData();
        foreach ($expectedData as $key => $value) {
            $this->assertEquals($value, $data[$key]);
        }
    }

    protected function assertResponse(ResponseInterface $response, $expectedData)
    {
        foreach ($expectedData as $key => $value) {
            $method = 'get' . ucfirst($key);
            $this->assertEquals($value, $response->$method());
        }
    }
}
