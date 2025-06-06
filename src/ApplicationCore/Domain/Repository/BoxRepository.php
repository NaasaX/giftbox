<?php
declare(strict_types=1);

namespace Giftbox\ApplicationCore\Domain\Repository;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\ApplicationCore\Domain\Entities\Categorie;
use Giftbox\ApplicationCore\Domain\Entities\Prestation;

class BoxRepository implements BoxRepositoryInterface
{
    public function findById(string $id): ?Box
    {
        return Box::find($id);
    }

    public function save(Box $box): void
    {
        $box->save();
    }

    public function getPrestationsTemporairesFromCookie(string $boxId): array
    {
        $prestationsTemporaires = [];

        $cookieName = "box_{$boxId}_prestations";

        if (isset($_COOKIE[$cookieName])) {
            $decoded = json_decode($_COOKIE[$cookieName], true);
            if (is_array($decoded)) {
                foreach ($decoded as $prestationId => $quantite) {
                    $prestation = Prestation::find($prestationId);
                    if ($prestation) {
                        $prestationsTemporaires[] = (object)[
                            'id' => $prestation->id,
                            'libelle' => $prestation->libelle,
                            'tarif' => $prestation->tarif,
                            'quantite' => $quantite,
                            'pivot' => (object)['quantite' => $quantite]
                        ];
                    }
                }
            }
        }

        return $prestationsTemporaires;
    }

    public function getMontantTotal(Box $box): float
    {
        $total = 0.0;

        if ($box->statut == 1) {
            $prestations = $this->getPrestationsTemporairesFromCookie($box->id);
            foreach ($prestations as $p) {
                $total += $p->tarif * $p->quantite;
            }
        } else {
            foreach ($box->prestations as $presta) {
                $total += $presta->tarif * $presta->pivot->quantite;
            }
        }

        return $total;
    }

    public function getCategoriesIfDraft(Box $box): array
    {
        if ($box->statut == 1) {
            return Categorie::with('prestations')->get()->all();
        }

        return [];
    }
}
