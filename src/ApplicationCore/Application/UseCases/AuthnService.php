<?php

namespace Giftbox\ApplicationCore\Application\UseCases;
use Giftbox\ApplicationCore\Domain\Entities\User;
use Giftbox\ApplicationCore\Domain\Repository\UserRepositoryInterface;

class AuthnService implements AuthnServiceInterface
{
    private UserRepositoryInterface $userRepository;
    private \Giftbox\providers\AuthProviderInterface $authProvider;

    // Rôles définis selon les spécifications
    public const ROLE_USER = 1;
    public const ROLE_ADMIN = 100;

    public function __construct(UserRepositoryInterface $userRepository, \Giftbox\providers\AuthProviderInterface $authProvider)
    {
        $this->userRepository = $userRepository;
        $this->authProvider = $authProvider;
    }

    public function register(string $email, string $password): User // Signature matches interface
    {
        // Validation des données d'entrée
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Invalid email format.");
        }
        $this->validatePassword($password);

        // Vérifier si l'utilisateur existe déjà using the email as user_id for lookup
        $existingUser = $this->userRepository->findByEmail($email); // findByEmail now queries user_id column
        if ($existingUser !== null) {
            throw new \RuntimeException("A user with this email already exists.");
        }

        // Hacher le mot de passe
        $hashedPassword = $this->hashPassword($password);

        // Créer l'utilisateur avec le rôle par défaut (utilisateur enregistré)
        $user = new User();
        $user->user_id = $email; // Set user_id with the email value
        $user->password = $hashedPassword;
        // nom and prenom are not set as per user feedback
        $user->role = self::ROLE_USER;

        // Sauvegarder l'utilisateur
        $this->userRepository->save($user);
        return $user;
    }
    
    // This registerUser method seems redundant now or was for a different purpose.
    // If it's not used, it could be removed to avoid confusion.
    // For now, I'll leave it but ensure the primary 'register' method is correct.
    public function registerUser(string $userId, string $password): User
    {
        // Validation des données d'entrée
        // TODO: Add validation for userId if necessary, e.g., length, format
        $this->validatePassword($password);

        // Vérifier si l'utilisateur existe déjà
        $existingUser = $this->userRepository->findByUserId($userId);
        if ($existingUser !== null) {
            throw new \RuntimeException("Un utilisateur avec cet ID utilisateur existe déjà");
        }

        // Hacher le mot de passe
        $hashedPassword = $this->hashPassword($password);

        // Créer l'utilisateur avec le rôle par défaut (utilisateur enregistré)
        $user = new User();
        $user->id = base64_encode(random_bytes(16));
        $user->user_id = $userId;
        $user->password = $hashedPassword; // Eloquent handles setPassword if it's a mutator
        $user->role = self::ROLE_USER; // Eloquent handles setRole if it's a mutator
        // $user->setCreatedAt(new \DateTime()); // Eloquent handles timestamps if enabled

        // Sauvegarder l'utilisateur
        $this->userRepository->save($user);
        return $user;
    }

    public function verifyCredentials(string $email, string $password): ?User
    {
        // Validation basique
        if (empty($email) || empty($password)) { // Corrected $userId to $email
            return null;
        }

        // Récupérer l'utilisateur par email
        $user = $this->userRepository->findByEmail($email);
        if ($user === null) {
            return null;
        }

        // Vérifier le mot de passe
        if ($this->verifyPassword($password, $user->password)) {
        
            if (!isset($user->id)) {
                throw new \LogicException("User object is missing an ID property.");
            }
            $this->authProvider->setActiveUserId((string)$user->id);
            return $user;
        }

        return null;
    }

    public function signOut(): void
    {
        $this->authProvider->clearActiveUser(); // Changed to use the new method name
    }

    public function isSignedIn(): bool
    {
        return $this->authProvider->isAuthenticated(); // Changed to use the new method name
    }

    public function getCurrentUserId(): ?string
    {
        return $this->authProvider->getCurrentUserId();
    }


    /**
     * Valide le mot de passe
     *
     * @param string $password
     * @throws \InvalidArgumentException
     */
    private function validatePassword(string $password): void
    {
        if (empty($password)) {
            throw new \InvalidArgumentException("Password cannot be empty.");
        }

        if (strlen($password) < 6) {
            throw new \InvalidArgumentException("Password must be at least 6 characters long.");
        }

        if (strlen($password) > 255) {
            throw new \InvalidArgumentException("Password is too long (max 255 characters).");
        }
    }

    /**
     * Hache un mot de passe
     * 
     * @param string $password
     * @return string
     */
    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 12, 
        ]);
    }

    /**
     * Vérifie un mot de passe contre son hash
     * 
     * @param string $password Le mot de passe en clair
     * @param string $hash Le hash stocké
     * @return bool
     */
    private function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

  }

