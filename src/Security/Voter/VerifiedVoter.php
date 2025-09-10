<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class VerifiedVoter extends Voter
{
    public const IS_VERIFIED = 'IS_VERIFIED';
    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::IS_VERIFIED;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->isVerified(); // true si email confirmé
    }
}