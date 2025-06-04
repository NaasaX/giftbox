<?php
declare(strict_types=1);

namespace Giftbox\providers;

use Giftbox\ApplicationCore\Application\UseCases\AuthnServiceInterface;
use Giftbox\ApplicationCore\Domain\Repository\UserRepositoryInterface;
use Giftbox\ApplicationCore\Domain\Entities\User;
use InvalidArgumentException; // Already used, but good practice to import

class SessionAuthProvider implements \Giftbox\providers\AuthProviderInterface
{
    private AuthnServiceInterface $authnService;
    private UserRepositoryInterface $userRepository;
    
    // Clé pour stocker l'ID utilisateur en session
    private const SESSION_USER_ID_KEY = 'auth_user_id';
    
    // Clé pour stocker le timestamp de dernière activité
    private const SESSION_LAST_ACTIVITY_KEY = 'auth_last_activity';
    
    // Durée d'inactivité maximale (en secondes) - 30 minutes par défaut
    private const SESSION_TIMEOUT = 1800;

    public function __construct(
        AuthnServiceInterface $authnService,
        UserRepositoryInterface $userRepository
    ) {
        $this->authnService = $authnService;
        $this->userRepository = $userRepository;
        
        // Démarrer la session si elle n'est pas déjà démarrée
        $this->ensureSessionStarted();
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
            $user = $this->userRepository->findById((int)$userId);
            
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

    /**
     * {@inheritDoc}
     */
    public function signin(string $email, string $password): bool
    {
        // Validation des paramètres
        if (empty($email) || empty($password)) {
            throw new InvalidArgumentException("L'email et le mot de passe sont requis");
        }

        try {
            // Utiliser le service d'authentification pour vérifier les credentials
            $user = $this->authnService->verifyCredentials($email, $password);
            
            if ($user === null) {
                return false;
            }

            // Authentification réussie, créer la session
            $this->createUserSession($user);
            
            return true;
            
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire (à implémenter selon votre système de log)
            error_log("Erreur lors de l'authentification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Déconnecte l'utilisateur en nettoyant la session
     */
    public function signout(): void
    {
        $this->clearSession();
    }

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
        return $user ? $user->getRole() : null;
    }

    /**
     * Vérifie si l'utilisateur connecté a le rôle admin
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        $user = $this->getSignedInUser();
        return $user && $user->isAdmin();
    }

    /**
     * Vérifie si l'utilisateur connecté est un utilisateur standard
     * 
     * @return bool
     */
    public function isUser(): bool
    {
        $user = $this->getSignedInUser();
        return $user && $user->isUser();
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
        $_SESSION[self::SESSION_USER_ID_KEY] = $user->getId();
        $_SESSION[self::SESSION_LAST_ACTIVITY_KEY] = time();
        
        // Optionnel : stocker des informations supplémentaires
        $_SESSION['auth_user_email'] = $user->getEmail();
        $_SESSION['auth_user_role'] = $user->getRole();
    }

    /**
     * Nettoie les données d'authentification de la session
     */
    private function clearSession(): void
    {
        // Supprimer les clés d'authentification
        unset($_SESSION[self::SESSION_USER_ID_KEY]);
        unset($_SESSION[self::SESSION_LAST_ACTIVITY_KEY]);
        unset($_SESSION['auth_user_email']);
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