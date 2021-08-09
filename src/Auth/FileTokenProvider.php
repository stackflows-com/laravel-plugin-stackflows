<?php

namespace Stackflows\StackflowsPlugin\Auth;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class FileTokenProvider implements TokenProviderInterface
{
    private string $tokenPath;
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->tokenPath = storage_path('auth/token.txt');
    }

    /**
     * @throws Exception
     */
    public function set(string $token): void
    {
        $this->prepareDirectory();
        $this->filesystem->put($this->tokenPath, $token);
    }

    public function get(): ?string
    {
        try {
            $token = $this->filesystem->get($this->tokenPath);
        } catch (FileNotFoundException $e) {
            return null;
        }

        return $token;
    }

    public function exists(): bool
    {
        return $this->filesystem->exists($this->tokenPath);
    }

    public function delete(): bool
    {
        if (! $this->exists()) {
            return false;
        }

        $this->filesystem->delete($this->tokenPath);

        return true;
    }

    /**
     * Check directory structure and permissions.
     *
     * @throws Exception
     */
    protected function prepareDirectory(): self
    {
        $dir = dirname($this->tokenPath);
        if ($this->filesystem->exists($dir) && ! is_writable($dir)) {
            throw new Exception('Auth storage directory is not writable');
        }

        if (! $this->filesystem->exists($dir)) {
            $this->filesystem->makeDirectory($dir);
        }

        if (! $this->filesystem->exists($dir)) {
            throw new Exception('Auth storage directory could not be created');
        }

        return $this;
    }
}
