<?php

namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use InvalidArgumentException;

interface BoxByTokenServiceInterface
{
    /**
     * Récupère une box via son token de partage.
     *
     * @param string $token
     * @return Box
     * @throws InvalidArgumentException si la box n'existe pas ou token invalide
     */
    public function getBoxByToken(string $token): Box;
}

