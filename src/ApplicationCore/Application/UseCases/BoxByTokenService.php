<?php

namespace Giftbox\ApplicationCore\Application\UseCases;

use Giftbox\ApplicationCore\Domain\Entities\Box;
use InvalidArgumentException;
class BoxByTokenService implements BoxByTokenServiceInterface
{
    public function getBoxByToken(string $token): Box
    {
        $box = Box::where('token', $token)->first();

        if (!$box) {
            throw new InvalidArgumentException("Box introuvable ou token invalide.");
        }

        return $box;
    }
}
