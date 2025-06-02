<?php
namespace Giftbox\ApplicationCore\Application\UseCases;

interface BoxUsageServiceInterface
{
    /**
     * Génère un accès/token pour la box si possible.
     */
    public function generateAccessTokenForBox(int $boxId, int $userId): string;

    /**
     * Retourne les infos d'une box achetée par l'utilisateur.
     */
    public function getBoxInfo(int $boxId, int $userId): array;

    /**
     * Retourne les prestations disponibles via la box.
     */
    public function getPrestationsForBox(int $boxId, int $userId): array;

    /**
     * Marque la box comme utilisée (consommation).
     */
    public function consumeBox(int $boxId, int $userId): void;

    /**
     * Vérifie si la box peut être utilisée.
     */
    public function canUseBox(int $boxId, int $userId): bool;
}
