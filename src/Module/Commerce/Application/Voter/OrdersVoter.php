<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Voter;

use App\Module\Commerce\Domain\Entity\Order;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrdersVoter extends Voter
{
    public const string GET_PAGINATED = 'GET_PAGINATED_ORDERS';
    public const string SHOW = 'SHOW_ORDER';
    public const string CREATE = 'CREATE_ORDER';
    public const string UPDATE_STATUS = 'UPDATE_STATUS_ORDER';

    private function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::GET_PAGINATED, self::SHOW, self::CREATE, self::UPDATE_STATUS])
            && ($subject instanceof Order || $subject === null);
    }

    /**
     * @param Order $subject
     */
    private function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
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

    private function canGetPaginated(UserInterface $user): bool
    {
        return $user->isAdmin();
    }

    private function canShow(Order $order, UserInterface $user): bool
    {
        return $user->isAdmin()
            || $order->getClient()->getId() === $user->getUserIdentifier();
    }

    private function canCreate(): bool
    {
        return true;
    }

    private function canUpdateStatus(UserInterface $user): bool
    {
        return $user->isAdmin();
    }
}
