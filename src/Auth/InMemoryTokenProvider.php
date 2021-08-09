<?php

namespace Stackflows\StackflowsPlugin\Auth;

use Exception;
use Illuminate\Filesystem\Filesystem;

class InMemoryTokenProvider implements TokenProviderInterface
{
    private ?string $token = null;

    /**
     * @throws Exception
     */
    public function set(string $token): void
    {
        $this->token = $token;
    }

    public function get(): ?string
    {
        return $this->token;
    }

    public function exists(): bool
    {
        return $this->token != null;
    }

    public function delete(): bool
    {
        $this->token = null;
        return true;
    }
}
