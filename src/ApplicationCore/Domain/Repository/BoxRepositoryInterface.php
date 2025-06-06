<?php
declare(strict_types=1);

namespace Giftbox\ApplicationCore\Domain\Repository;

use Giftbox\ApplicationCore\Domain\Entities\Box;

interface BoxRepositoryInterface
{
    public function findById(string $id): ?Box;

    public function save(Box $box): void;

    public function getPrestationsTemporairesFromCookie(string $boxId): array;

    public function getMontantTotal(Box $box): float;

    public function getCategoriesIfDraft(Box $box): array;
}
