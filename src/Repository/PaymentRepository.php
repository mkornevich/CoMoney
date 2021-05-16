<?php


namespace App\Repository;


use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}