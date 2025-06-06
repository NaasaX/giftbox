<?php
namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\ApplicationCore\Domain\Entities\Prestation;
use InvalidArgumentException;
use DomainException;

class BoxValidationService implements BoxValidationServiceInterface
{
    public function validateBoxById(string $boxId, string $userId, int $userRole): Box
    {
        $box = Box::find($boxId);
        if (!$box) {
            throw new InvalidArgumentException("Box introuvable.");
        }

        if ($userRole === null || $userRole < 1 || $box->createur_id !== $userId) {
            throw new DomainException("Vous n'avez pas les droits nécessaires pour valider cette box.");
        }

        $prestationCount = $box->prestations()->count();
        if ($prestationCount < 2) {
            // Lecture des prestations depuis cookie
            $cookieName = "box_{$boxId}_prestations";
            $prestations = [];

            if (isset($_COOKIE[$cookieName])) {
                $decoded = json_decode($_COOKIE[$cookieName], true);
                if (is_array($decoded)) {
                    $prestations = $decoded;
                }
            }

            $totalQuantite = array_sum($prestations);
            if ($totalQuantite < 2) {
                throw new DomainException("Vous devez ajouter au moins deux prestations pour valider ce coffret.");
            }

            foreach ($prestations as $prestationId => $quantite) {
                $presta = Prestation::find($prestationId);
                if ($presta && $quantite > 0) {
                    $box->prestations()->attach($prestationId, ['quantite' => $quantite]);
                }
            }
        }

        $box->statut = 2;
        $box->save();

        // Supprimer le cookie
        setcookie("box_{$boxId}_prestations", '', time() - 3600, "/");

        // Nettoyage session se fera dans l'action, pas ici

        return $box;
    }
}
