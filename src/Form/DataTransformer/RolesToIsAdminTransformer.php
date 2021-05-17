<?php


namespace App\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RolesToIsAdminTransformer implements DataTransformerInterface
{
    public function transform($value): bool
    {
        return in_array('ROLE_ADMIN', $value);
    }

    public function reverseTransform($value): array
    {
        $roles = ['ROLE_USER'];
        if ($value) {
            $roles[] = 'ROLE_ADMIN';
        }
        return $roles;
    }
}