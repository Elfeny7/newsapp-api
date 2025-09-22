<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Interfaces\TokenServiceInterface;

class JwtMiddleware
{
    private TokenServiceInterface $tokenServiceInterface;

    public function __construct(TokenServiceInterface $tokenServiceInterface){
        $this->tokenServiceInterface = $tokenServiceInterface;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $this->tokenServiceInterface->authenticate();
        return $next($request);
    }
}
