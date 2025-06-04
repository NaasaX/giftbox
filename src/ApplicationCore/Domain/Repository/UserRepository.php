<?php
declare(strict_types=1);

namespace Giftbox\ApplicationCore\Domain\Repository;

use Giftbox\ApplicationCore\Domain\Entities\User;
use Ramsey\Uuid\Uuid;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Récupère un utilisateur par son user_id.
     */
    public function findByUserId(string $userId): ?User
    {
        return User::where('user_id', $userId)->first();
    }

    /**
     * Récupère un utilisateur par son email.
     */
    public function findByEmail(string $email): ?User
    {
        // Corrected to use 'user_id' as the column name for email lookup
        return User::where('user_id', $email)->first();
    }

    /**
     * Récupère un utilisateur par son ID.
     * Modifié pour accepter string au lieu de int (pour les ID base64)
     */
    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    /**
     * Crée un nouvel utilisateur avec ID généré automatiquement
     */
    public function create(array $userData): User
    {
        $user = new User();
        
        $user->id = $this->generateUuid();
        $user->user_id = $userData['user_id'] ?? $userData['email'];
        $user->password = $userData['password'];
        $user->role = $userData['role'] ?? 1;
        
        // Ajouter nom et prenom si disponibles
        if (isset($userData['nom'])) {
            $user->nom = $userData['nom'];
        }
        if (isset($userData['prenom'])) {
            $user->prenom = $userData['prenom'];
        }
        
        $user->save();
        
        return $user;
    }

    /**
     * Sauvegarde (crée ou met à jour) un utilisateur.
     */
    public function save(User $user): bool
    {
        // Si l'utilisateur n'a pas d'ID, en générer un
        if (empty($user->id)) {
            $user->id = $this->generateUuid();
        
        
        return $user->save();
    }
}

    /**
     * Supprime un utilisateur.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Vérifie si un email/user_id existe déjà
     */
    public function emailExists(string $email): bool
    {
        return User::where('user_id', $email)->exists();
    }

    
    private function generateUuid(): string
{
    return Uuid::uuid4()->toString();
}

}