<?php

namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\ApplicationCore\Domain\Entities\Prestation;

class PanierService implements PanierServiceInterface
{
    public function ajouterPrestation(string $boxId, string $prestationId): void
    {
        $box = Box::find($boxId);
        $prestation = Prestation::find($prestationId);

        if (!$box || !$prestation) {
            throw new \InvalidArgumentException("Box ou prestation introuvable.");
        }

        if ($box->statut !== 1) {
            throw new \RuntimeException("La box n'est plus modifiable.");
        }

        $cookieName = "box_{$boxId}_prestations";
        $prestations = [];

        if (isset($_COOKIE[$cookieName])) {
            $decoded = json_decode($_COOKIE[$cookieName], true);
            if (is_array($decoded)) {
                $prestations = $decoded;
            }
        }

        if (isset($prestations[$prestationId])) {
            $prestations[$prestationId]++;
        } else {
            $prestations[$prestationId] = 1;
        }

        $cookieValue = json_encode($prestations);
        setcookie($cookieName, $cookieValue, time() + 86400, "/");
    }

    public function supprimerPrestation(string $boxId, string $prestationId): void
    {
        $cookieName = "box_{$boxId}_prestations";
        $prestations = [];

        if (isset($_COOKIE[$cookieName])) {
            $decoded = json_decode($_COOKIE[$cookieName], true);
            if (is_array($decoded)) {
                $prestations = $decoded;
            }
        }

        if (isset($prestations[$prestationId])) {
            unset($prestations[$prestationId]);

            if (empty($prestations)) {
                setcookie($cookieName, '', time() - 3600, "/");
            } else {
                $cookieValue = json_encode($prestations);
                setcookie($cookieName, $cookieValue, time() + 86400, "/");
            }
        } else {
            throw new \InvalidArgumentException("Prestation introuvable dans le panier");
        }
    }
}
