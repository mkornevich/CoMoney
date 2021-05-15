<?php


namespace App\Filter;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use ReflectionClass;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractFilter
{

    public function applyFilterForm(FormInterface $form, QueryBuilder $builder): void
    {
        $class = new ReflectionClass($this);
        $this->beforeAddFilters($builder, $form);
        foreach ($class->getMethods() as $method) {
            if (str_starts_with($method->name, 'add') && str_ends_with($method->name, 'Filter')) {
                $method->setAccessible(true);
                $method->invoke($this, $builder, $form);
            }
        }
        $this->afterAddFilters($builder, $form);
    }

    protected function beforeAddFilters(QueryBuilder $builder, FormInterface $form): void
    {

    }

    protected function afterAddFilters(QueryBuilder $builder, FormInterface $form): void
    {

    }
}