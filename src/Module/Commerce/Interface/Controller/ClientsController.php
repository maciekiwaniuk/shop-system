<?php

declare(strict_types=1);

namespace App\Module\Commerce\Interface\Controller;

use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use App\Module\Commerce\Application\Query\FindClientById\FindClientByIdQuery;
use App\Module\Commerce\Application\Voter\ClientsVoter;
use App\Module\Commerce\Domain\Repository\ClientRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/clients')]
class ClientsController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly ClientRepositoryInterface $clientRepository,
    ) {
    }

    #[Route('/details/{id}', methods: [Request::METHOD_GET])]
    #[IsGranted(ClientsVoter::GET_DETAILS, subject: 'id')]
    public function getDetails(string $id): Response
    {
        $queryResult = $this->queryBus->handle(new FindClientByIdQuery($id));
        if ($queryResult->data !== null) {
            $client = $this->clientRepository->getReference($queryResult->data['id']);
        }

        $result = match (true) {
            $queryResult->success && isset($client) => [
                'success' => true,
                'data' => [
                    'email' => $client->getEmail(),
                    'name' => $client->getName(),
                    'surname' => $client->getSurname(),
                ],
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while fetching details about client.',
            ]
        };
        return $this->json($result, $queryResult->statusCode);
    }
}
