<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }
    public function findAllNotification($user)
    {
        return $this->createQueryBuilder('n')
            ->where('n.seller = :user OR n.buyer = :user')
            ->andWhere('n.sender != :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
