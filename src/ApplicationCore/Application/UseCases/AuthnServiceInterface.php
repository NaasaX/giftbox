<?php
declare(strict_types=1);

namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Domain\Entities\User;

interface AuthnServiceInterface
{
    public function register(string $email, string $password): User;

    public function verifyCredentials(string $email, string $password): ?User;
}
