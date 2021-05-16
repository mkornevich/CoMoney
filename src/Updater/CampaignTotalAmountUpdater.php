<?php


namespace App\Updater;


use App\Entity\Campaign;
use Doctrine\ORM\EntityManagerInterface;

class CampaignTotalAmountUpdater
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function update(Campaign $campaign): void
    {
        $query = $this->entityManager->createQuery('
            UPDATE App\Entity\Campaign c 
            SET c.totalAmount = (SELECT SUM(p.amount) FROM App\Entity\Payment p WHERE p.campaign = :campaign_id) 
            WHERE c.id = :campaign_id
        ');
        $query->execute(['campaign_id' => $campaign->getId()]);
    }
}