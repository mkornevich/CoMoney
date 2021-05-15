<?php


namespace App\Filter;


use App\Entity\Campaign;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class CampaignFilter extends AbstractFilter
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function afterAddFilters(QueryBuilder $builder, FormInterface $form): void
    {
        $builder->groupBy('campaign.id');
    }

    private function addTabFilter(QueryBuilder $builder, FormInterface $form): void
    {
        $tab = $form->get('tab')->getData();
        $user = $this->security->getUser();

        if (in_array($tab, ['my', 'sponsored'])) {
            $builder->setParameter('userId', $user->getId());
        }

        if ($tab == 'sponsored') {
            $builder->innerJoin('campaign.payments', 'payment')
                ->andWhere('payment.user = :userId');
        }

        if ($tab == 'my') {
            $builder->andWhere('campaign.owner = :userId');
        }
    }

    private function addSearchFilter(QueryBuilder $builder, FormInterface $form): void
    {
        $search = $form->get('search')->getData();
        if ($search != null) {
            $search = '%' . $search . '%';
            $builder->andWhere('campaign.name LIKE :search OR campaign.description LIKE :search')
                ->setParameter('search', $search);
        }
    }

    private function addUserFilter(QueryBuilder $builder, FormInterface $form): void
    {
        $userStr = $form->get('user')->getData();
        if ($userStr != null) {
            $userStr = '%' . $userStr . '%';
            $builder->andWhere('user.username LIKE :userStr OR user.fullName LIKE :userStr')
                ->setParameter('userStr', $userStr);
        }
    }

    private function addRatingFromFilter(QueryBuilder $builder, FormInterface $form): void
    {
        $ratingFrom = $form->get('ratingFrom')->getData();
        if ($ratingFrom != null) {
            $builder->andWhere('campaign.rating >= :ratingFrom')
                ->setParameter('ratingFrom', $ratingFrom);
        }
    }

    private function addSubjectFilter(QueryBuilder $builder, FormInterface $form): void
    {
        $subject = $form->get('subject')->getData();
        if ($subject != null) {
            $builder->andWhere('subject.id = :subject')->setParameter('subject', $subject->getId());
        }
    }

    private function addTagsFilter(QueryBuilder $builder, FormInterface $form): void
    {
        $tags = $form->get('tags')->getData();
        if (!$tags->isEmpty()) {
            $builder->setParameter('tagIds', $tags->map(fn($tag) => $tag->getId())->toArray());
            $builder
                ->leftJoin('campaign.tags', 'tag')
                ->andWhere('tag.id IN (:tagIds)');
        }
    }
}