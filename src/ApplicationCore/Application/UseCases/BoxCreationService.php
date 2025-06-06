<?php
namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\ApplicationCore\Domain\Repository\BoxRepositoryInterface;
use Illuminate\Support\Str;
use function Symfony\Component\Clock\now;

class BoxCreationService implements BoxCreationServiceInterface
{
    private BoxRepositoryInterface $boxRepository;

    public function __construct(BoxRepositoryInterface $boxRepository)
    {
        $this->boxRepository = $boxRepository;
    }

    public function createBox(array $data, string $userId): Box
    {
        // validation & création
        $nom = trim($data['nom'] ?? '');
        $description = trim($data['description'] ?? '');
        $cadeau = isset($data['cadeau']) ? 1 : 0;
        $message = trim($data['message'] ?? '');

        if (empty($nom) || empty($description)) {
            throw new \InvalidArgumentException("Nom et description sont obligatoires.");
        }

        $box = new Box([
            'libelle' => $nom,
            'description' => $description,
            'kdo' => $cadeau,
            'message_kdo' => $cadeau ? $message : '',
            'createur_id' => $userId,
        ]);

        $box->token = base64_encode(random_bytes(32));
        $box->id = (string) Str::uuid();
        $box->created_at = now();

        $this->boxRepository->save($box);

        return $box;
    }
}