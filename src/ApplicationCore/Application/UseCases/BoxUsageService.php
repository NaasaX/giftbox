<?php
namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Application\Exceptions\BoxNotFoundException;
use Giftbox\ApplicationCore\Application\Exceptions\BoxUsageException;


class BoxUsageService implements BoxUsageServiceInterface
{
    protected $boxRepository;
    protected $prestationRepository;

    public function __construct($boxRepository, $prestationRepository)
    {
        $this->boxRepository = $boxRepository;
        $this->prestationRepository = $prestationRepository;
    }

    public function generateAccessTokenForBox(int $boxId, int $userId): string
    {
        $box = $this->boxRepository->findByIdAndUser($boxId, $userId);
        if (!$box) throw new BoxNotFoundException("Box non trouvée ou non accessible.");
        if ($box['etat'] !== 'validée' || $box['utilisee']) {
            throw new BoxUsageException("Box déjà utilisée ou non valide.");
        }
        if ($box['token'] && !$box['utilisee']) {
            return $box['token'];
        }
        $token = bin2hex(random_bytes(16));
        $this->boxRepository->saveToken($boxId, $token);
        return $token;
    }

    public function getBoxInfo(int $boxId, int $userId): array
    {
        $box = $this->boxRepository->findByIdAndUser($boxId, $userId);
        if (!$box) throw new BoxNotFoundException("Box non trouvée.");
        return $box;
    }

    public function getPrestationsForBox(int $boxId, int $userId): array
    {
        $box = $this->boxRepository->findByIdAndUser($boxId, $userId);
        if (!$box) throw new BoxNotFoundException("Box non trouvée.");
        return $this->prestationRepository->findByBoxId($boxId);
    }

    public function consumeBox(int $boxId, int $userId): void
    {
        $box = $this->boxRepository->findByIdAndUser($boxId, $userId);
        if (!$box) throw new BoxNotFoundException("Box non trouvée.");
        if ($box['utilisee']) {
            throw new BoxUsageException("Box déjà utilisée.");
        }
        $this->boxRepository->markAsUsed($boxId);
    }

    public function canUseBox(int $boxId, int $userId): bool
    {
        $box = $this->boxRepository->findByIdAndUser($boxId, $userId);
        return $box && !$box['utilisee'] && $box['etat'] === 'validée';
    }
}