<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Voter;

use App\Module\Commerce\Domain\Entity\Order;
use App\Module\Auth\Domain\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrdersVoter extends Voter
{
    public const GET_PAGINATED = 'GET_PAGINATED_ORDERS';
    public const SHOW = 'SHOW_ORDER';
    public const CREATE = 'CREATE_ORDER';
    public const UPDATE_STATUS = 'UPDATE_STATUS_ORDER';

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
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::GET_PAGINATED => $this->canGetPaginated($user),
            self::SHOW => $this->canShow($subject, $user),
            self::CREATE => $this->canCreate(),
            self::UPDATE_STATUS => $this->canUpdateStatus($user),
            default => throw new Exception('Invalid attribute.')
        };
    }

    private function canGetPaginated(User $user): bool
    {
        return $user->isAdmin();
    }

    private function canShow(Order $order, User $user): bool
    {
        return $user->isAdmin()
            || $order->getUser() === $user;
    }

    private function canCreate(): bool
    {
        return true;
    }

    private function canUpdateStatus(User $user): bool
    {
        return $user->isAdmin();
    }
}
