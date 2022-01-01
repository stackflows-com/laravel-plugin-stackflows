<?php

namespace Stackflows\Http\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractStackflowsClient
{
    private Client $client;

    public function __construct($token, $baseUri)
    {
        if ($this->getBaseUriSuffix()) {
            $baseUri .= $this->getBaseUriSuffix();
        }

        $this->client = new Client([
            'base_uri' => $baseUri,
            'headers' => [
                'Authorization' => $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'timeout' => 5.0,
        ]);
    }

    protected function getBaseUriSuffix(): ?string
    {
        return null;
    }

    protected function makeGetRequest(string $uri, array $params = [])
    {
        try {
            $response = $this->client->get($uri, $params);
        } catch (RequestException $exception) {
            return $this->createErrorResponse($exception->getResponse());
        }

        return $this->parseResponse($response);
    }

    protected function makePostRequest(string $uri, array $params = [])
    {
        try {
            $response = $this->client->post($uri, $params);
        } catch (ClientException $exception) {
            return $this->createErrorResponse($exception->getResponse());
        }

        return $this->parseResponse($response);
    }

    protected function createErrorResponse($response): array
    {
        $response = $this->parseResponse($response);

        $message = $response['error'] ?? null;
        if (isset($response['errorCode'])) {
            $message = ErrorMap::map($response['errorCode'], $message);
        }

        return ['error' => $message];
    }

    protected function parseResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
