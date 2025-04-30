<?php

declare(strict_types=1);

namespace App\Module\Commerce\Interface\Controller;

use App\Common\Application\Bus\AsyncCommandBus\AsyncCommandBusInterface;
use App\Common\Application\Bus\QueryBus\QueryBusInterface;
use App\Common\Application\Bus\SyncCommandBus\SyncCommandBusInterface;
use App\Common\Application\DTO\PaginationUuidDTO;
use App\Module\Commerce\Application\Command\ChangeOrderStatus\ChangeOrderStatusCommand;
use App\Module\Commerce\Application\Command\CreateOrder\CreateOrderCommand;
use App\Module\Commerce\Application\DTO\Validation\ChangeOrderStatusDTO;
use App\Module\Commerce\Application\DTO\Validation\CreateOrderDTO;
use App\Module\Commerce\Application\Query\FindOrderByUuid\FindOrderByUuidQuery;
use App\Module\Commerce\Application\Query\GetPaginatedOrders\GetPaginatedOrdersQuery;
use App\Module\Commerce\Application\Voter\OrdersVoter;
use App\Module\Commerce\Domain\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1/orders')]
class OrdersController extends AbstractController
{
    public function __construct(
        private readonly SyncCommandBusInterface $syncCommandBus,
        private readonly AsyncCommandBusInterface $asyncCommandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly EntityManagerInterface $commerceEntityManager,
    ) {
    }

    #[Route('/get-paginated', methods: [Request::METHOD_GET])]
    #[IsGranted(OrdersVoter::GET_PAGINATED)]
    public function getPaginated(#[ValueResolver('get_paginated_orders')] PaginationUuidDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $queryResult = $this->queryBus->handle(new GetPaginatedOrdersQuery($dto->cursor, $dto->limit));

        $result = match (true) {
            $queryResult->success => [
                'success' => true,
                'data' => $queryResult->data,
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while getting paginated orders.',
            ]
        };
        return $this->json($result, $queryResult->statusCode);
    }

    #[Route('/show/{uuid}', methods: [Request::METHOD_GET])]
    public function show(string $uuid): Response
    {
        $queryResult = $this->queryBus->handle(new FindOrderByUuidQuery($uuid));

        $order = $this->commerceEntityManager->getReference(Order::class, $queryResult->data['id']);
        if (!$this->isGranted(OrdersVoter::SHOW, $order)) {
            throw $this->createAccessDeniedException();
        }

        $result = match (true) {
            $queryResult->success => [
                'success' => true,
                'data' => $queryResult->data,
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while showing order.',
            ]
        };
        return $this->json($result, $queryResult->statusCode);
    }

    #[Route('/create', methods: [Request::METHOD_POST])]
    #[IsGranted(OrdersVoter::CREATE)]
    public function create(#[ValueResolver('create_order_dto')] CreateOrderDTO $dto): Response
    {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $commandResult = $this->syncCommandBus->handle(new CreateOrderCommand($dto));

        $result = match (true) {
            $commandResult->success => [
                'success' => true,
                'message' => 'Successfully created order.',
            ],
            default => [
                'success' => false,
                'message' => 'Something went wrong while creating order.',
            ]
        };
        return $this->json($result, $commandResult->statusCode);
    }

    #[Route('/change-status/{uuid}', methods: [Request::METHOD_POST])]
    #[IsGranted(OrdersVoter::UPDATE_STATUS)]
    public function changeStatus(
        #[ValueResolver('change_order_status_dto')] ChangeOrderStatusDTO $dto,
        string $uuid,
    ): Response {
        if ($dto->hasErrors()) {
            return $this->json([
                'success' => false,
                'errors' => $dto->getErrors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->asyncCommandBus->handle(new ChangeOrderStatusCommand($dto, $uuid));

        return $this->json([
            'success' => true,
            'message' => 'Successfully queued update status of order.',
        ], Response::HTTP_ACCEPTED);
    }
}
