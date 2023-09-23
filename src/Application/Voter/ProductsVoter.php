<?php

declare(strict_types=1);

namespace App\Application\Voter;

use App\Domain\Entity\Product;
use App\Domain\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsVoter extends Voter
{
    public const GET_ALL = 'GET_ALL_PRODUCTS';
    public const NEW = 'NEW_PRODUCT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::GET_ALL, self::NEW])
            && ($subject instanceof Product || $subject === null);
    }

    /**
     * @param Product $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::GET_ALL => $this->canGetAll(),
            self::NEW => $this->canNew($user),
            default => throw new Exception('Invalid attribute.')
        };
    }

    private function canGetAll(): bool
    {
        return true;
    }

    private function canNew(User $user): bool
    {
        return $user->isAdmin();
    }
}
