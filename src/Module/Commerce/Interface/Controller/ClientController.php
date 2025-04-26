<?php

declare(strict_types=1);

namespace App\Module\Commerce\Interface\Controller;

use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use App\Module\Commerce\Application\Query\FindClientByEmail\FindClientByEmailQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/client')]
class ClientController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('/exists', methods: [Request::METHOD_GET])]
    public function exists(Request $request): Response
    {
        $email = $request->query->get('email');
        if (!$email) {
            return $this->json([
                'success' => false,
                'message' => 'Email parameter is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        $queryResult = $this->queryBus->handle(new FindClientByEmailQuery($email));

        return $this->json([
            'success' => true,
            'data' => [
                'exists' => $queryResult->success && $queryResult->data['id'] !== null,
                'id' => $queryResult->data['id'] ?? null
            ]
        ]);
    }
}
