<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Bus\CommandBus\CommandBusInterface;
use App\Application\Bus\QueryBus\QueryBusInterface;
use App\Application\Command\CreateUser\CreateUserCommand as CreateUserCommandEvent;
use App\Application\DTO\User\CreateUserDTO;
use App\Application\Query\FindUserByEmail\FindUserByEmailQuery;
use App\Domain\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Infrastructure\Serializer\JsonSerializer;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1')]
class UserController extends AbstractController
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly QueryBusInterface $queryBus,
        protected readonly JWTTokenManagerInterface $JWTTokenManager,
        protected readonly JsonSerializer $serializer
    ) {
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns all orders',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'bool'),
                new OA\Property(property: 'message', type: 'string'),
                new OA\Property(
                    property: 'data',
                    properties: [new OA\Property(property: 'token', type: 'string')],
                    type: 'object'
                )
            ]
        )
    )]
    #[OA\RequestBody(content: new Model(type: CreateUserDTO::class))]
    #[Route('/register', methods: ['POST'])]
    public function register(#[ValueResolver('create_user_dto')] CreateUserDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto-> getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $commandResult = $this->commandBus->handle(new CreateUserCommandEvent($dto));

        $queryResult = $this->queryBus->handle(new FindUserByEmailQuery($dto->email));
        $user = $this->serializer->deserialize(json_encode($queryResult->data), User::class);

        $result = match (true) {
            $commandResult->success && $queryResult->success => [
                'success' => true,
                'message' => 'Successfully registered.',
                'data' => [
                    'token' => $this->JWTTokenManager->create($user)
                ]
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while registering.'
            ]
        };

        return $this->json($result, $commandResult->statusCode);
    }
}
