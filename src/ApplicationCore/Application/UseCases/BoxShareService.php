<?php

namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use DomainException;
use InvalidArgumentException;
use function Symfony\Component\Clock\now;

class BoxShareService implements BoxShareServiceInterface
{
    public function shareBox(string $boxId, string $userId, int $userRole): Box
    {
        $box = Box::find($boxId);
        if (!$box) {
            throw new InvalidArgumentException("Box introuvable.");
        }

        if ($userRole === null || $userRole < 1 || $box->createur_id !== $userId) {
            throw new DomainException("Vous n'avez pas les droits nécessaires pour partager cette box.");
        }

        // Génération token unique sécurisé
        $token = base64_encode(random_bytes(32));

        $box->token = $token;
        $box->statut = 3;
        $box->updated_at = now();
        $box->save();

        return $box;
    }
}
