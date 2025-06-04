<?php
declare(strict_types=1);

namespace Giftbox\providers;

use Giftbox\ApplicationCore\Domain\Entities\User;

interface AuthProviderInterface
{
    public function getSignedInUser(): ?User;

    
    public function setActiveUserId(string $userId): void;

    
    public function clearActiveUser(): void;

    public function isAuthenticated(): bool;

    public function getCurrentUserId(): ?string;

    public function getUserRole(): ?int;

    public function isAdmin(): bool;

    public function isUser(): bool;
}