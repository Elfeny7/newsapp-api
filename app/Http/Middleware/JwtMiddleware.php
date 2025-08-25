<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Support\ApiResponse;
use App\Interfaces\TokenServiceInterface;

class JwtMiddleware
{
    private TokenServiceInterface $tokenServiceInterface;

    public function __construct(TokenServiceInterface $tokenServiceInterface){
        $this->tokenServiceInterface = $tokenServiceInterface;
    }

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->tokenServiceInterface->authenticate();
        } catch (\Exception $e) {
            return ApiResponse::throw($e, "Unauthorized", 401);
        }
        return $next($request);
    }
}
