<?php

namespace Giftbox\webui\actions\Post;

use DomainException;
use Giftbox\ApplicationCore\Application\UseCases\BoxValidationServiceInterface;
use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\ApplicationCore\Domain\Entities\Prestation;
use Giftbox\Webui\providers\CsrfTokenProvider;
use Giftbox\Webui\Providers\SessionAuthProvider;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpForbiddenException;

class PostValidateBoxAction
{
    private SessionAuthProvider $authProvider;
    private BoxValidationServiceInterface $validationService;

    public function __construct(SessionAuthProvider $authProvider, BoxValidationServiceInterface $validationService)
    {
        $this->authProvider = $authProvider;
        $this->validationService = $validationService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        try {
            $token = $data['csrf_token'] ?? '';
            CsrfTokenProvider::check($token);
        } catch (\Exception $e) {
            $response->getBody()->write("Erreur CSRF : " . $e->getMessage());
            return $response->withStatus(403);
        }

        $boxId = $_SESSION['current_box_id'] ?? null;
        if (!$boxId) {
            $response->getBody()->write("Aucune box en cours.");
            return $response->withStatus(400);
        }

        $userRole = $this->authProvider->getUserRole();
        $userId = $this->authProvider->getCurrentUserId();

        try {
            $this->validationService->validateBoxById($boxId, $userId, $userRole);
            // Validation OK, nettoyage session et message
            unset($_SESSION['current_box_id']);
            $_SESSION['flash_message'] = "Coffret validé avec succès !";
        } catch (DomainException $e) {
            $_SESSION['flash_message'] = $e->getMessage();
            return $response->withHeader('Location', '/box/' . $boxId)->withStatus(302);
        } catch (InvalidArgumentException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(404);
        } catch (HttpForbiddenException $e) {
            throw $e; // laisse Slim gérer la 403
        } catch (\Exception $e) {
            $response->getBody()->write("Erreur interne : " . $e->getMessage());
            return $response->withStatus(500);
        }

        return $response->withHeader('Location', '/box/' . $boxId)->withStatus(302);
    }
}