<?php

namespace Giftbox\ApplicationCore\Application\UseCases;

interface GetBoxDetailsInterface
{
    /**
     * @param string $boxId
     * @param array|null $cookieData Données de prestations temporaires depuis les cookies
     * @return array Données prêtes à être envoyées à la vue
     */
    public function execute(string $boxId, ?array $cookieData): array;
}
