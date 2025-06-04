<?php
declare(strict_types=1);

namespace Giftbox\providers;

use Giftbox\ApplicationCore\Domain\Entities\User;

interface AuthProviderInterface
{
    public function getSignedInUser(): ?User;

    /**
     * Sets the specified user ID as the currently authenticated user.
     * This is typically called by AuthnService after successful credential verification.
     */
    public function setActiveUserId(string $userId): void;

    /**
     * Clears the authenticated user state.
     */
    public function clearActiveUser(): void; // Renamed from signout for clarity of responsibility

    public function isAuthenticated(): bool;

    public function getCurrentUserId(): ?string;

    public function getUserRole(): ?int;

    public function isAdmin(): bool;

    public function isUser(): bool;
}