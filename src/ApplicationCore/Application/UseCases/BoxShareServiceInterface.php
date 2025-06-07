<?php

namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Domain\Entities\Box;

interface BoxShareServiceInterface
{
    /**
     * Génère un token, met à jour la box (token + statut 3) et sauvegarde.
     *
     * @param string $boxId
     * @param string $userId
     * @param int $userRole
     * @return Box
     */
    public function shareBox(string $boxId, string $userId, int $userRole): Box;
}
