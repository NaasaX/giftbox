<?php

namespace Giftbox\webui\actions\Post;

use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Giftbox\ApplicationCore\Domain\Entities\Box;
use function Symfony\Component\Clock\now;

class PostSaveCustomBoxAction {
    public function __invoke(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();

        $nom = trim($data['nom'] ?? '');
        $description = trim($data['description'] ?? '');
        $cadeau = isset($data['cadeau']) ? 1 : 0;
        $message = trim($data['message'] ?? '');
        $createurId = '9c02505f-af68-4b51-a5b6-e52b1805eee1'; // ⚠️ à remplacer plus tard par un ID utilisateur dynamique

        if (empty($nom) || empty($description)) {
            $response->getBody()->write("Nom et description sont obligatoires.");
            return $response->withStatus(400);
        }

        // Création de la box avec génération automatique de l'id et du token via le modèle
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

        $box->save(); // Cela déclenche le boot et donc la génération du token


        // Stockage en session si besoin (ex : pour ajouter prestations ensuite)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['current_box_id'] = $box->id;

        // Redirection vers l’étape suivante
        return $response
            ->withHeader('Location', '/box/' . $box->id)
            ->withStatus(302);
    }
}
