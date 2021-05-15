<?php


namespace App\Form\DataTransformer;


use App\Entity\User;
use App\Repository\CampaignRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserToStringTransformer implements DataTransformerInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function transform($value): string
    {
        if ($value === null) {
            return '';
        }
        return $value->getUsername();
    }

    public function reverseTransform($value): User|null
    {
        $user = $this->userRepository->findOneByUsername($value);
        if ($user === null) {
            throw new TransformationFailedException("User with this username not found.");
        }
        return $user;
    }
}