<?php

namespace App\Services;

use App\Interfaces\TokenServiceInterface;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenService implements TokenServiceInterface
{
    public function generate(User $user): string {
        return JWTAuth::fromUser($user);
    }

    public function attempt(array $credentials): ?string {
        if (! $token = JWTAuth::attempt($credentials)) {
            return null;
        }
        return $token;
    }

    public function authenticate(): void {
        JWTAuth::parseToken()->authenticate();
    }

    public function getTTL(): int {
        return JWTAuth::factory()->getTTL() * 60;
    }

    public function getUser(): ?User {
        return JWTAuth::user();
    }

    public function invalidate(): void {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function refresh(): string {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    public function getRefreshTTL(): int {
        return JWTAuth::factory()->getRefreshTTL() * 60;
    }




    public function validate(string $token): bool {
        return JWTAuth::setToken($token)->check();
    }

    // public function refresh(string $token): string {
    //     return JWTAuth::setToken($token)->refresh();
    // }

    public function getUserFromToken(string $token): ?User {
        return JWTAuth::setToken($token)->authenticate();
    }
}
