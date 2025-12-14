<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Voter;

use App\Common\Application\Security\UserContextInterface;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template-extends Voter<string, string|null>
 */
class ClientsVoter extends Voter
{
    public const string GET_DETAILS = 'GET_CLIENT_DETAILS';

    public function __construct(
        private readonly UserContextInterface $userContext,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::GET_DETAILS
            && (is_string($subject) || $subject === null);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            self::GET_DETAILS => $this->canGetDetails($subject),
            default => throw new Exception('Invalid attribute.')
        };
    }

    private function canGetDetails(?string $clientId): bool
    {
        if ($this->userContext->isAdmin()) {
            return true;
        }

        return $clientId !== null && $clientId === $this->userContext->getUserIdentifier();
    }
}
