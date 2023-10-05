<?php

declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\Bus\CommandBus\CommandBusInterface;
use App\Application\Bus\QueryBus\QueryBusInterface;
use App\Application\Command\ChangeOrderStatus\ChangeOrderStatusCommand;
use App\Application\Command\CreateOrder\CreateOrderCommand;
use App\Application\DTO\Order\ChangeOrderStatusDTO;
use App\Application\DTO\Order\CreateOrderDTO;
use App\Application\Query\FindOrderByUuid\FindOrderByUuidQuery;
use App\Application\Query\GetOrders\GetOrdersQuery;
use App\Application\Voter\OrdersVoter;
use App\Domain\Entity\Order;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[Route('/api/v1/orders', name: 'orders.')]
class OrdersController extends AbstractController
{
    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly QueryBusInterface $queryBus
    ) {
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns all orders',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: 'bool'),
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Order::class, groups: ['default']))
                )
            ]
        )
    )]
    #[Route('/get-all', methods: ['GET'])]
    #[IsGranted(OrdersVoter::GET_ALL)]
    public function getAll(): Response
    {
        $queryResult = $this->queryBus->handle(new GetOrdersQuery());

        $result = match (true) {
            $queryResult->success => [
                'success' => true,
                'data' => $queryResult->data
            ],
            default => [
                'success' => false
            ]
        };
        return $this->json($result, $queryResult->statusCode);
    }

    #[Route('/show/{uuid}', methods: ['GET'])]
    public function show(string $uuid): Response
    {
        $queryResult = $this->queryBus->handle(new FindOrderByUuidQuery($uuid));

        $result = match (true) {
            $queryResult->success => [
                'success' => true,
                'data' => $queryResult->data
            ],
            default => [
                'success' => false
            ]
        };
        return $this->json($result, $queryResult->statusCode);
    }

    // TODO:
    #[OA\RequestBody(content: new Model(type: CreateOrderDTO::class, groups: ['default']))]
    #[Route('/new', methods: ['POST'])]
    #[IsGranted(OrdersVoter::NEW)]
    public function new(#[ValueResolver('create_order_dto')] CreateOrderDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $commandResult = $this->commandBus->handle(new CreateOrderCommand($dto));

        $result = match (true) {
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully created order.'
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while creating order.'
            ]
        };
        return $this->json($result, $commandResult->statusCode);
    }

    #[Route('/change-status/{uuid}', methods: ['POST'])]
    #[IsGranted(OrdersVoter::UPDATE_STATUS)]
    public function changeStatus(
        #[ValueResolver('change_order_status_dto')] ChangeOrderStatusDTO $dto,
        string $uuid
    ): Response {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $commandResult = $this->commandBus->handle(new ChangeOrderStatusCommand($dto, $uuid));

        $result = match (true) {
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully updated status of order.'
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while updating status of order.'
            ]
        };
        return $this->json($result, $commandResult->statusCode);
    }
}
