<?php


namespace App\Security\Voter;


use App\Entity\Campaign;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CampaignVoter extends Voter
{
    public const EDIT = 'edit';
    public const EDIT_OWNER = 'edit_owner';
    public const CREATE = 'create';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Campaign && in_array($attribute, [self::EDIT, self::EDIT_OWNER, self::CREATE]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($attribute == self::EDIT) {
            return $subject->getOwner() === $user;
        }

        if ($attribute == self::CREATE) {
            return true;
        }

        return false;
    }
}