<?php
namespace Giftbox\webui\actions\Post;


use Giftbox\ApplicationCore\Application\UseCases\BoxCreationServiceInterface;
use Giftbox\Webui\Providers\SessionAuthProvider;
use Giftbox\ApplicationCore\Domain\Repository\UserRepository;
use Giftbox\Webui\providers\CsrfTokenProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostSaveCustomBoxAction
{
    protected BoxCreationServiceInterface $boxCreationService;

    public function __construct(BoxCreationServiceInterface $boxCreationService)
    {
        $this->boxCreationService = $boxCreationService;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        $userRepository = new UserRepository();
        $session = new SessionAuthProvider($userRepository);
        $userId = $session->getCurrentUserId();

        try {
            $token = $data['csrf_token'] ?? '';
            CsrfTokenProvider::check($token);

            $box = $this->boxCreationService->createBox($data, $userId);

            $_SESSION['current_box_id'] = $box->id;

            return $response
                ->withHeader('Location', '/box/' . $box->id)
                ->withStatus(302);

        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write("Erreur de validation : " . $e->getMessage());
            return $response->withStatus(400);

        } catch (\Exception $e) {
            $response->getBody()->write("Erreur : " . $e->getMessage());
            return $response->withStatus(500);
        }
    }
}
