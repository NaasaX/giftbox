<?php
namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Application\Exceptions\CategorieNotFoundException;
use Giftbox\ApplicationCore\Application\Exceptions\PrestationNotFoundException;
use Giftbox\ApplicationCore\Application\Exceptions\CoffretNotFoundException;


class CatalogueService implements CatalogueServiceInterface
{
    protected $categorieRepository;
    protected $prestationRepository;
    protected $coffretRepository;

    public function __construct(
        $categorieRepository,
        $prestationRepository,
        $coffretRepository
    ) {
        $this->categorieRepository = $categorieRepository;
        $this->prestationRepository = $prestationRepository;
        $this->coffretRepository = $coffretRepository;
    }

    public function getCategories(): array
    {
        try {
            $categories = $this->categorieRepository->findAll();
            return $categories;
        } catch (\PDOException $e) {
            throw new CategorieNotFoundException("Erreur la récupération des catégories");
        }
    }

    public function getCategorieById(int $id): array
    {
        try {
            $categorie = $this->categorieRepository->findById($id);
            if (!$categorie) {
                throw new CategorieNotFoundException("Catégorie $id introuvable");
            }
            return $categorie;
        } catch (\PDOException $e) {
            throw new CategorieNotFoundException("Erreur de la récupération de la catégorie");
        }
    }

    public function getPrestationById(string $id): array
    {
        try {
            $prestation = $this->prestationRepository->findById($id);
            if (!$prestation) {
                throw new PrestationNotFoundException("Prestation $id introuvable");
            }
            return $prestation;
        } catch (\PDOException $e) {
            throw new PrestationNotFoundException("Erreur de la récupération de la prestation");
        }
    }

    public function getPrestationsbyCategorie(int $categ_id): array
    {
        try {
            $prestations = $this->prestationRepository->findByCategorie($categ_id);
            return $prestations;
        } catch (\PDOException $e) {
            throw new PrestationNotFoundException("Erreur de la récupération des prestations pour la catégorie $categ_id");
        }
    }

    public function getThemesCoffrets(): array
    {
        try {
            $themes = $this->coffretRepository->findAllThemes();
            return $themes;
        } catch (\PDOException $e) {
            throw new CoffretNotFoundException("Erreur de la récupération des thèmes de coffrets");
        }
    }

    public function getCoffretById(int $id): array
    {
        try {
            $coffret = $this->coffretRepository->findById($id);
            if (!$coffret) {
                throw new CoffretNotFoundException("Coffret $id introuvable");
            }
            return $coffret;
        } catch (\PDOException $e) {
            throw new CoffretNotFoundException("Erreur de la récupération du coffret");
        }
    }
}
