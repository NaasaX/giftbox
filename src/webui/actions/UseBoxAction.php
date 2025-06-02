<?php
namespace Giftbox\WebUI\Actions;

use Giftbox\ApplicationCore\Application\UseCases\BoxUsageServiceInterface;
use Giftbox\ApplicationCore\Application\Exceptions\BoxNotFoundException;
use Giftbox\ApplicationCore\Application\Exceptions\BoxUsageException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpForbiddenException;

class UseBoxAction
{
    protected $boxUsageService;

    public function __construct(BoxUsageServiceInterface $boxUsageService)
    {
        $this->boxUsageService = $boxUsageService;
    }

    public function __invoke($request, $response, $args)
    {
        $boxId = (int) $args['id'];
        $userId = $request->getAttribute('user_id');

        try {
            $token = $this->boxUsageService->generateAccessTokenForBox($boxId, $userId);
        } catch (BoxNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        } catch (BoxUsageException $e) {
            throw new HttpForbiddenException($request, $e->getMessage());
        }
    }
}
