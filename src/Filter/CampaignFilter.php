<?php


namespace App\Filter;


use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CampaignFilter extends AbstractFilter
{
    private function addSearchFilter(QueryBuilder $builder, FormInterface $form): void
    {

    }

    private function addUserFilter(QueryBuilder $builder, FormInterface $form): void
    {

    }

    private function addRatingFromFilter(QueryBuilder $builder, FormInterface $form): void
    {

    }

    private function addSubjectFilter(QueryBuilder $builder, FormInterface $form): void
    {
        $subject = $form->get('subject')->getData();
        if ($subject != null) {
            $builder->andWhere($builder->expr()->eq('subject.id', $subject->getId()));
        }

    }

    private function addTagsFilter(QueryBuilder $builder, FormInterface $form): void
    {
        $tags = $form->get('tags')->getData();
        if (!$tags->isEmpty()) {
            $builder->andWhere($builder->expr()->in('tag.id', $tags->map(fn($tag) => $tag->getId())->toArray()));
        }
    }
}