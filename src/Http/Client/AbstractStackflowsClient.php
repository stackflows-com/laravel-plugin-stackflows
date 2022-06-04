<?php

namespace Stackflows\Http\Client;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractStackflowsClient
{
    protected Client $client;

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

    protected function decodeResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    protected function fetchResponseMeta(ResponseInterface $response): array
    {
        $responseArray = $this->decodeResponse($response);
        if (! isset($responseArray['meta'])) {
            throw new \Exception('Response has no meta');
        }

        return $responseArray['meta'];
    }

    protected function fetchResponseData(ResponseInterface $response): array
    {
        $responseArray = $this->decodeResponse($response);
        if (! isset($responseArray['data'])) {
            throw new \Exception('Response has no data');
        }

        return $responseArray['data'];
    }
}
