<?php

declare(strict_types=1);

namespace App\Module\Auth\Interface\Controller;

use App\Module\Auth\Application\Command\CreateUser\CreateUserCommand;
use App\Module\Auth\Application\DTO\CreateUserDTO;
use App\Module\Auth\Application\Query\FindUserByEmail\FindUserByEmailQuery;
use App\Module\Auth\Domain\Entity\User;
use App\Common\Application\Bus\SyncCommandBus\SyncCommandBusInterface;
use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly SyncCommandBusInterface $syncCommandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly JWTTokenManagerInterface $JWTTokenManager,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/test', methods: ['POST'])]
    public function test(): Response
    {
        return $this->json(['test'], 200);
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Register user',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'bool'),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(
                    property: 'data',
                    properties: [new OA\Property(property: 'token', type: 'string')],
                    type: 'object',
                ),
            ],
        ),
    )]
    #[OA\RequestBody(content: new Model(type: CreateUserDTO::class))]
    #[Route('/register', methods: [Request::METHOD_POST])]
    public function register(#[ValueResolver('create_user_dto')] CreateUserDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $commandResult = $this->syncCommandBus->handle(new CreateUserCommand($dto));
        $queryResult = $this->queryBus->handle(new FindUserByEmailQuery($dto->email));
        if ($queryResult->data !== null) {
            $user = $this->entityManager->getReference(User::class, $queryResult->data['id']);
        }

        $result = match (true) {
            $commandResult->success && $queryResult->success && isset($user) => [
                'success' => true,
                'message' => 'Successfully registered.',
                'data' => [
                    'token' => $this->JWTTokenManager->create($user),
                ],
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while registering.',
            ]
        };
        return $this->json($result, $commandResult->statusCode);
    }
}
