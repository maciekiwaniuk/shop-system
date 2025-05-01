<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Voter;

use App\Common\Application\Security\UserContextInterface;
use App\Module\Commerce\Domain\Entity\Product;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductsVoter extends Voter
{
    public const string GET_PAGINATED = 'GET_PAGINATED_PRODUCTS';
    public const string CREATE = 'CREATE_PRODUCT';
    public const string SHOW = 'SHOW_PRODUCT';
    public const string UPDATE = 'UPDATE_PRODUCT';
    public const string DELETE = 'DELETE_PRODUCT';
    public const string SEARCH = 'SEARCH_PRODUCTS';

    public function __construct(
        private readonly UserContextInterface $userContext,
    ) {
    }

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
        return match ($attribute) {
            self::GET_PAGINATED => $this->canGetPaginated(),
            self::CREATE => $this->canCreate($this->userContext),
            self::SHOW => $this->canShow(),
            self::UPDATE => $this->canUpdate($this->userContext),
            self::DELETE => $this->canDelete($this->userContext),
            self::SEARCH => $this->canSearch(),
            default => throw new Exception('Invalid attribute.')
        };
    }

    private function canGetPaginated(): bool
    {
        return true;
    }

    private function canCreate(UserContextInterface $userContext): bool
    {
        return $userContext->isAdmin();
    }

    private function canShow(): bool
    {
        return true;
    }

    private function canUpdate(UserContextInterface $userContext): bool
    {
        return $userContext->isAdmin();
    }

    private function canDelete(UserContextInterface $userContext): bool
    {
        return $userContext->isAdmin();
    }

    private function canSearch(): bool
    {
        return true;
    }
}
