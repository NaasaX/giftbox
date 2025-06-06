<?php
declare(strict_types=1);

namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Domain\Repository\BoxRepositoryInterface;

class GetBoxDetailsService implements GetBoxDetailsInterface
{
    public function __construct(
        private BoxRepositoryInterface $boxRepository
    ) {}

    public function execute(string $boxId, ?array $cookieData): array
    {
        $box = $this->boxRepository->findById($boxId);
        if (!$box) {
            throw new \RuntimeException("Box non trouvée");
        }

        $prestationsTemporaires = $this->boxRepository->getPrestationsTemporairesFromCookie($boxId);

        $montantTotal = $this->boxRepository->getMontantTotal($box);

        $box->montant = $montantTotal;

        $categories = $this->boxRepository->getCategoriesIfDraft($box);

        return [
            'box' => $box,
            'prestations_temporaires' => $prestationsTemporaires,
            'categories' => $categories,
        ];
    }
}
