<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Voter;

use App\Module\Commerce\Domain\Entity\Product;
use App\Module\Auth\Domain\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsVoter extends Voter
{
    public const string GET_PAGINATED = 'GET_PAGINATED_PRODUCTS';
    public const string CREATE = 'CREATE_PRODUCT';
    public const string SHOW = 'SHOW_PRODUCT';
    public const string UPDATE = 'UPDATE_PRODUCT';
    public const string DELETE = 'DELETE_PRODUCT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::GET_PAGINATED, self::CREATE, self::SHOW, self::UPDATE, self::DELETE])
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
            self::GET_PAGINATED => $this->canGetPaginated(),
            self::CREATE => $this->canCreate($user),
            self::SHOW => $this->canShow(),
            self::UPDATE => $this->canUpdate($user),
            self::DELETE => $this->canDelete($user),
            default => throw new Exception('Invalid attribute.')
        };
    }

    private function canGetPaginated(): bool
    {
        return true;
    }

    private function canCreate(User $user): bool
    {
        return $user->isAdmin();
    }

    private function canShow(): bool
    {
        return true;
    }

    private function canUpdate(User $user): bool
    {
        return $user->isAdmin();
    }

    private function canDelete(User $user): bool
    {
        return $user->isAdmin();
    }
}
