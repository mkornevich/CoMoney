<?php


namespace App\Repository;


use App\Entity\Campaign;
use App\Entity\Payment;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function persistAndFlush(Payment $payment): void
    {
        $this->_em->persist($payment);
        $this->_em->flush();
    }

    public function getAllEagerQB(): QueryBuilder
    {
        return $this->createQueryBuilder('payment')
            ->addSelect(['campaign', 'user'])
            ->leftJoin('payment.campaign', 'campaign')
            ->leftJoin('payment.user', 'user')
            ->orderBy('payment.createdAt', 'DESC');
    }

    public function getAllEagerByUserQB(User $user): QueryBuilder
    {
        return $this->getAllEagerQB()
            ->where('user.id = :user_id')
            ->setParameter('user_id', $user->getId())
    ;
    }
}