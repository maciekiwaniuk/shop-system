<?php

declare(strict_types=1);

namespace App\Module\Auth\Interface\Controller;

use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use App\Common\Application\Bus\SyncCommandBus\SyncCommandBusInterface;
use App\Module\Auth\Application\Command\CreateUser\CreateUserCommand;
use App\Module\Auth\Application\DTO\Validation\CreateUserDTO;
use App\Module\Auth\Application\Query\FindUserByEmail\FindUserByEmailQuery;
use App\Module\Auth\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
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
