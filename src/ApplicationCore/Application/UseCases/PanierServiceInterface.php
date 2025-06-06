<?php

namespace Giftbox\ApplicationCore\Application\UseCases;

use InvalidArgumentException;
use RuntimeException;

interface PanierServiceInterface
{
    /**
     * Ajoute une prestation dans la box (panier).
     *
     * @param string $boxId
     * @param string $prestationId
     * @return void
     *
     * @throws InvalidArgumentException si box ou prestation introuvable
     * @throws RuntimeException si box non modifiable
     */
    public function ajouterPrestation(string $boxId, string $prestationId): void;

    public function supprimerPrestation(string $boxId, string $prestationId): void;

}
