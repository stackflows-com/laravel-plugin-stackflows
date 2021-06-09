<?php

namespace Stackflows\StackflowsPlugin\Http;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;

class ClientFactory
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function create(int $timeout = 5, int $retries = 5): ClientInterface
    {
        $middleware = Middleware::retry($this->createRetryHandler($retries));

        $stack = HandlerStack::create();
        $stack->push($middleware);

        return new Client(
            [
                'timeout' => $timeout,
                'handler' => $stack,
            ]
        );
    }

    private function createRetryHandler(int $maxRetries): callable
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            TransferException $e = null
        ) use ($maxRetries) {
            if ($retries >= $maxRetries) {
                $msg = sprintf("%s %s", $request->getMethod(), $request->getUri());
                if ($e) {
                    $msg .= $e->getMessage();
                } elseif ($response) {
                    $msg .= 'status code: ' . $response->getStatusCode();
                }

                throw TooManyErrors::tooManyHttpErrors($msg);
            }

            // Retry connection exceptions
            if ($e instanceof ConnectException) {
                $this->logger->warning(
                    sprintf(
                        'Retrying %s %s %s/%s, %s',
                        $request->getMethod(),
                        $request->getUri(),
                        $retries + 1,
                        $maxRetries,
                        $e->getMessage()
                    ),
                    [$request->getHeader('Host')[0]]
                );

                return true;
            }

            if ($response) {
                // Retry on server errors
                if ($response->getStatusCode() >= 500) {
                    $this->logger->warning(
                        sprintf(
                            'Retrying %s %s %s/%s, %s',
                            $request->getMethod(),
                            $request->getUri(),
                            $retries + 1,
                            $maxRetries,
                            'status code: ' . $response->getStatusCode()
                        ),
                        [$request->getHeader('Host')[0]]
                    );

                    return true;
                }
            }

            return false;
        };
    }
}
