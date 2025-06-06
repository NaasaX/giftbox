<?php

namespace Giftbox\ApplicationCore\Application\UseCases;

use DomainException;
use Giftbox\ApplicationCore\Domain\Entities\Box;
use InvalidArgumentException;

interface BoxValidationServiceInterface
{
    /**
     * Valide une box donnée par son ID pour un utilisateur donné.
     *
     * @param string $boxId
     * @param string $userId
     * @param int $userRole
     * @throws InvalidArgumentException si box introuvable ou invalide
     * @throws DomainException si l'utilisateur n'a pas les droits nécessaires
     * @return Box La box validée
     */
    public function validateBoxById(string $boxId, string $userId, int $userRole): Box;
}
