<?php

namespace Giftbox\ApplicationCore\Application\UseCases;

interface CatalogueServiceInterface
{
    public function getCategories(): array;
    public function getCategorieById(int $id): array;
    public function getPrestationById(string $id): array;
    public function getPrestationsbyCategorie(int $categ_id): array;
    public function getThemesCoffrets(): array;
    public function getCoffretById(int $id): array;
}
