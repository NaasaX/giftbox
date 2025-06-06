<?php
namespace Giftbox\ApplicationCore\Application\UseCases;

use Exception;
use Giftbox\ApplicationCore\Domain\Entities\Box;
use InvalidArgumentException;

interface BoxCreationServiceInterface
{
    /**
     * Crée une nouvelle box avec les données fournies et l'ID de l'utilisateur créateur.
     *
     * @param array $data Données pour créer la box (nom, description, cadeau, message)
     * @param string $userId ID de l'utilisateur créateur
     * @return Box La box créée
     *
     * @throws InvalidArgumentException Si les données sont invalides
     * @throws Exception Pour d'autres erreurs
     */
    public function createBox(array $data, string $userId): Box;
}
