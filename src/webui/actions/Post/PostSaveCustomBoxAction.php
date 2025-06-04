<?php

namespace Giftbox\webui\actions\Post;

use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Giftbox\ApplicationCore\Domain\Entities\Box;
use Giftbox\Webui\Providers\CsrfTokenProvider; // ✅ Ajout de l'import
use function Symfony\Component\Clock\now;

class PostSaveCustomBoxAction {
    public function __invoke(Request $request, Response $response, array $args): Response {

        $data = $request->getParsedBody();

        try {
            $token = $data['csrf_token'] ?? '';
            CsrfTokenProvider::check($token);
        } catch (\Exception $e) {
            $response->getBody()->write("Erreur CSRF : " . $e->getMessage());
            return $response->withStatus(403); // Interdit
        }

        $nom = trim($data['nom'] ?? '');
        $description = trim($data['description'] ?? '');
        $cadeau = isset($data['cadeau']) ? 1 : 0;
        $message = trim($data['message'] ?? '');
        $createurId = '9c02505f-af68-4b51-a5b6-e52b1805eee1'; // ⚠️ temporaire

        if (empty($nom) || empty($description)) {
            $response->getBody()->write("Nom et description sont obligatoires.");
            return $response->withStatus(400);
        }

        $box = new Box([
            'libelle' => $nom,
            'description' => $description,
            'kdo' => $cadeau,
            'message_kdo' => $cadeau ? $message : '',
            'createur_id' => $createurId
        ]);

        $box->token = base64_encode(random_bytes(32));
        $box->id = (string) Str::uuid();
        $box->created_at = now();

        $box->save();

        $_SESSION['current_box_id'] = $box->id;

        return $response
            ->withHeader('Location', '/box/' . $box->id)
            ->withStatus(302);
    }
}
