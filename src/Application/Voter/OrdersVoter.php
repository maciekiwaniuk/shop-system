<?php

declare(strict_types=1);

namespace App\Application\Voter;

use App\Domain\Entity\Order;
use App\Domain\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrdersVoter extends Voter
{
    public const GET_ALL = 'GET_ALL_ORDERS';
    public const SHOW = 'SHOW_ORDER';
    public const NEW = 'NEW_ORDER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::GET_ALL, self::SHOW, self::NEW])
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
            self::GET_ALL => $this->canGetAll($user),
            self::SHOW => $this->canShow($subject, $user),
            self::NEW => $this->canNew(),
            default => throw new Exception('Invalid attribute.')
        };
    }

    private function canGetAll(User $user): bool
    {
        return $user->isAdmin();
    }

    private function canShow(Order $order, User $user): bool
    {
        return $user->isAdmin()
            || $order->getUser() === $user;
    }

    private function canNew(): bool
    {
        return true;
    }
}
