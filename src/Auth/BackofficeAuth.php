<?php

namespace Stackflows\StackflowsPlugin\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Stackflows\StackflowsPlugin\Configuration;
use Stackflows\StackflowsPlugin\Exceptions\InvalidCredentials;
use Stackflows\StackflowsPlugin\Exceptions\TokenRequired;

class BackofficeAuth
{
    private BackofficeClient $client;
    private TokenProviderInterface $provider;
    private Configuration $conf;

    public function __construct(BackofficeClient $client, Configuration $conf, TokenProviderInterface $provider)
    {
        $this->client = $client;
        $this->conf = $conf;
        $this->provider = $provider;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @throws GuzzleException
     * @throws \Exception
     */
    public function attempt(string $email, string $password): bool
    {
        try {
            $token = $this->client->login($email, $password);
        } catch (InvalidCredentials $e) {
            return false;
        }

        $this->setToken($token);

        return true;
    }

    /**
     * Log the user out of the backoffice application.
     *
     * @throws InvalidCredentials
     * @throws GuzzleException
     */
    public function logout(): void
    {
        $this->client->logout($this->conf->getToken());
        $this->provider->delete();
    }

    /**
     * Try to set an authentication token.
     *
     * @throws TokenRequired
     */
    public function authenticate(): void
    {
        if (! empty($this->conf->getToken())) {
            return;
        }

        try {
            $token = $this->provider->get();
        } catch (FileNotFoundException $e) {
            throw new TokenRequired();
        }

        $this->conf->setToken($token);
    }

    /**
     * Determine whether the token is set.
     */
    public function check(): bool
    {
        if (! empty($this->conf->getToken())) {
            return true;
        }

        try {
            $this->authenticate();
        } catch (TokenRequired $e) {
            return false;
        }

        return true;
    }

    /**
     * Set the authentication token.
     *
     * @throws \Exception
     */
    public function setToken(string $token): void
    {
        $this->conf->setToken($token);
        $this->provider->set($token);
    }
}
