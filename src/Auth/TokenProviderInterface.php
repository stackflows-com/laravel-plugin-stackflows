<?php

namespace Stackflows\StackflowsPlugin\Auth;

interface TokenProviderInterface
{
    /**
     * Set the authentication token.
     */
    public function set(string $token): void;

    /**
     * Get the value of the authentication token.
     */
    public function get(): string;

    /**
     * Determine whether the token is set.
     */
    public function exists(): bool;

    /**
     * Delete the authentication token.
     */
    public function delete(): bool;
}
