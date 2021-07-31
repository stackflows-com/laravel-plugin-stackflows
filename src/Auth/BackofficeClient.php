<?php

namespace Stackflows\StackflowsPlugin\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Stackflows\StackflowsPlugin\Exceptions\InvalidCredentials;

class BackofficeClient
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws InvalidCredentials
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login(string $email, string $password): string
    {
        try {
            $response = $this->client->post(
                '/api/token', // TODO
                [
                    'json' => [
                        'email' => $email,
                        'password' => $password,
                    ],
                ]
            );
        } catch (ClientException $e) {
            if (401 == $e->getCode()) {
                throw InvalidCredentials::emailOrPassword();
            }
        }

        return $this->decodeToken($response->getBody()->getContents());
    }

    private function decodeToken(string $json): string
    {
        $data = json_decode($json);

        return $data->access_token ?? '';
    }

    /**
     * @throws InvalidCredentials
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function logout(string $token): void
    {
        try {
            $this->client->post(
                '/api/token/invalidate',
                ['headers' => ['Authorization' => " Bearer {$token}"]]
            );
        } catch (ClientException $e) {
            if (401 == $e->getCode()) {
                throw InvalidCredentials::token();
            }
        }
    }
}
