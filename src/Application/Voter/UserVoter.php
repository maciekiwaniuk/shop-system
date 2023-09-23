<?php

declare(strict_types=1);

namespace App\Application\Voter;

use App\Domain\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const REGISTER = 'REGISTER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::REGISTER]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::REGISTER => $this->canRegister(),
            default => throw new Exception('Invalid attribute.')
        };
    }

    private function canRegister(): bool
    {
        return true;
    }
}
