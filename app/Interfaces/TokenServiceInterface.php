<?php
namespace App\Interfaces;
use App\Models\User;

interface TokenServiceInterface
{
    public function generate(User $user): string;
    public function attempt(array $credentials): ?string;
    public function authenticate(): void;
    public function getTTL(): int;
    public function getUser(): ?User;
    public function invalidate(): void;
    public function validate(string $token): bool;
    public function refresh(string $token): string;
    public function getUserFromToken(string $token): ?User;
}
