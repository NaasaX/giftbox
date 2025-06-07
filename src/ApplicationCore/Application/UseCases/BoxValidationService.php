<?php
namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\ApplicationCore\Domain\Entities\Prestation;
use InvalidArgumentException;
use DomainException;
use function Symfony\Component\Clock\now;

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
        $prixTotal = 0;

        if ($prestationCount < 2) {
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
                    $prixTotal += $presta->tarif * $quantite;
                }
            }
        } else {
            foreach ($box->prestations as $presta) {
                $quantite = $presta->pivot->quantite ?? 1;
                $prixTotal += $presta->tarif * $quantite;
            }
        }

        $box->montant = $prixTotal;
        $box->updated_at = now();
        $box->statut = 2;
        $box->save();

        unset($_SESSION['flash_message']);

        setcookie("box_{$boxId}_prestations", '', time() - 3600, "/");

        return $box;
    }
}
