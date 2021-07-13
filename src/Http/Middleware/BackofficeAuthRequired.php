<?php

namespace Stackflows\StackflowsPlugin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stackflows\StackflowsPlugin\Auth\BackofficeAuth;
use Stackflows\StackflowsPlugin\Exceptions\TokenRequired;

class BackofficeAuthRequired
{
    protected BackofficeAuth $auth;

    protected string $redirectTo = 'login';

    public function __construct(BackofficeAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $this->auth->authenticate();
        } catch (TokenRequired $e) {
            return redirect($this->redirectTo);
        }

        return $next($request);
    }
}
