<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Voter;

use App\Common\Application\Security\UserContextInterface;
use App\Module\Commerce\Domain\Entity\Order;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrdersVoter extends Voter
{
    public const string GET_PAGINATED = 'GET_PAGINATED_ORDERS';
    public const string SHOW = 'SHOW_ORDER';
    public const string CREATE = 'CREATE_ORDER';
    public const string UPDATE_STATUS = 'UPDATE_STATUS_ORDER';

    public function __construct(
        private readonly UserContextInterface $userContext,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::GET_PAGINATED, self::SHOW, self::CREATE, self::UPDATE_STATUS])
            && ($subject instanceof Order || $subject === null);
    }

    /**
     * @param Order $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::GET_PAGINATED => $this->canGetPaginated(),
            self::SHOW => $this->canShow($subject, $this->userContext),
            self::CREATE => $this->canCreate(),
            self::UPDATE_STATUS => $this->canUpdateStatus($this->userContext),
            default => throw new Exception('Invalid attribute.')
        };
    }

    private function canGetPaginated(): bool
    {
        return true;
    }

    private function canShow(Order $order, UserContextInterface $userContext): bool
    {
        return $userContext->isAdmin()
            || $order->getClient()->getId() === $userContext->getUserIdentifier();
    }

    private function canCreate(): bool
    {
        return true;
    }

    private function canUpdateStatus(UserContextInterface $userContext): bool
    {
        return $userContext->isAdmin();
    }
}
