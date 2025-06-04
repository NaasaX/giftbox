<?php
declare(strict_types=1);

namespace Giftbox\providers;

use Giftbox\ApplicationCore\Domain\Repository\UserRepositoryInterface;
use Giftbox\ApplicationCore\Domain\Entities\User;
// AuthnServiceInterface and InvalidArgumentException are no longer directly needed here

class SessionAuthProvider implements AuthProviderInterface
{
    private UserRepositoryInterface $userRepository;

    // Clé pour stocker l'ID utilisateur en session
    private const SESSION_USER_ID_KEY = 'auth_user_id';

    // Clé pour stocker le timestamp de dernière activité
    private const SESSION_LAST_ACTIVITY_KEY = 'auth_last_activity';

    // Durée d'inactivité maximale (en secondes) - 30 minutes par défaut
    private const SESSION_TIMEOUT = 1800;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;

        // Démarrer la session si elle n'est pas déjà démarrée
        $this->ensureSessionStarted();
    }

    public function setActiveUserId(string $userId): void
    {
        // Régénérer l'ID de session pour la sécurité
        session_regenerate_id(true);

        // Stocker les informations utilisateur en session
        $_SESSION[self::SESSION_USER_ID_KEY] = $userId; // Store the ID directly
        $_SESSION[self::SESSION_LAST_ACTIVITY_KEY] = time();

        // Optionally, fetch the user to store role, but it's better if AuthnService handles this
        // or if the role is checked on-demand via getSignedInUser() -> role
        // For simplicity, we'll just store the ID. Role can be fetched when needed.
    }

    public function clearActiveUser(): void
    {
        $this->clearSession();
    }
    
    public function getCurrentUserId(): ?string
    {
        if (!$this->isSessionValid()) {
            return null;
        }
        return $_SESSION[self::SESSION_USER_ID_KEY] ?? null;
    }


    /**
     * {@inheritDoc}
     */
    public function getSignedInUser(): ?User
    {
        // Vérifier si la session est valide
        if (!$this->isSessionValid()) {
            return null;
        }

        // Récupérer l'ID utilisateur depuis la session
        $userId = $_SESSION[self::SESSION_USER_ID_KEY] ?? null;
        
        if ($userId === null) {
            return null;
        }

        try {
            // Charger l'utilisateur depuis le repository
            $user = $this->userRepository->findById($userId);
            
            if ($user !== null) {
                // Mettre à jour le timestamp de dernière activité
                $this->updateLastActivity();
            }
            
            return $user;
            
        } catch (\Exception $e) {
            // En cas d'erreur, nettoyer la session
            $this->clearSession();
            return null;
        }
    }

    // The old signin method is removed as AuthnService will handle credential verification
    // and then call setActiveUserId.

    // The old signout method is replaced by clearActiveUser for consistency.

    /**
     * Vérifie si un utilisateur est actuellement authentifié
     * 
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->getSignedInUser() !== null;
    }

    /**
     * Obtient le rôle de l'utilisateur connecté
     * 
     * @return int|null Le rôle ou null si non connecté
     */
    public function getUserRole(): ?int
    {
        $user = $this->getSignedInUser();
        return $user ? $user->role : null; // Changed to direct property access
    }

    /**
     * Vérifie si l'utilisateur connecté a le rôle admin
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        $user = $this->getSignedInUser();
        return $user && $user->role === AuthnService::ROLE_ADMIN;
    }

    /**
     * Vérifie si l'utilisateur connecté est un utilisateur standard
     * 
     * @return bool
     */
    public function isUser(): bool
    {
        $user = $this->getSignedInUser();
        return $user && $user->role === AuthnService::ROLE_USER;
    }

    /**
     * S'assure que la session PHP est démarrée
     */
    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Crée une session pour l'utilisateur authentifié
     * 
     * @param User $user
     */
    private function createUserSession(User $user): void
    {
        // Régénérer l'ID de session pour la sécurité
        session_regenerate_id(true);
        
        // Stocker les informations utilisateur en session
        $_SESSION[self::SESSION_USER_ID_KEY] = $user->id; // Changed to direct property access
        $_SESSION[self::SESSION_LAST_ACTIVITY_KEY] = time();
        
        // Optionnel : stocker des informations supplémentaires
        // $_SESSION['auth_user_email'] = $user->email; // Email is not used anymore
        $_SESSION['auth_user_role'] = $user->role; // Changed to direct property access
    }

    /**
     * Nettoie les données d'authentification de la session
     */
    private function clearSession(): void
    {
        // Supprimer les clés d'authentification
        unset($_SESSION[self::SESSION_USER_ID_KEY]);
        unset($_SESSION[self::SESSION_LAST_ACTIVITY_KEY]);
        // unset($_SESSION['auth_user_email']); // Email is not used anymore
        unset($_SESSION['auth_user_role']);
        
        // Optionnel : détruire complètement la session
        // session_destroy();
    }

    /**
     * Vérifie si la session est valide (non expirée)
     * 
     * @return bool
     */
    private function isSessionValid(): bool
    {
        $lastActivity = $_SESSION[self::SESSION_LAST_ACTIVITY_KEY] ?? null;
        
        if ($lastActivity === null) {
            return false;
        }

        // Vérifier si la session n'a pas expiré
        if ((time() - $lastActivity) > self::SESSION_TIMEOUT) {
            $this->clearSession();
            return false;
        }

        return true;
    }

    /**
     * Met à jour le timestamp de dernière activité
     */
    private function updateLastActivity(): void
    {
        $_SESSION[self::SESSION_LAST_ACTIVITY_KEY] = time();
    }
}