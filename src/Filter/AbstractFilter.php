<?php


namespace App\Filter;


use Doctrine\ORM\QueryBuilder;
use ReflectionClass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFilter
{
    public function applyFilters(QueryBuilder $builder, FormInterface $form): void
    {
        $class = new ReflectionClass($this);
        foreach ($class->getMethods() as $method) {
            if (str_starts_with($method->name, 'add') && str_ends_with($method->name, 'Filter')) {
                $method->setAccessible(true);
                $method->invoke($this, $builder, $form);
            }
        }
    }
}