<?php


namespace App\Repository;


use App\Entity\Campaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class CampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campaign::class);
    }
    public function getAllWithJoins(): QueryBuilder
    {
        return $this->createQueryBuilder('campaign')
            ->addSelect(['user', 'subject', 'image'])
            ->leftJoin('campaign.owner', 'user')
            ->leftJoin('campaign.subject', 'subject')
            ->leftJoin('campaign.image', 'image');
    }
}