<?php

namespace App\Service;

use App\Entity\Message;
use Doctrine\Persistence\ManagerRegistry;

class MessageService
{
    public function showDiscussion($productId, $user1Id, $user2Id)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m')
            ->join('m.products', 'p')
            ->join('p.users', 'u')
            ->where('p.id = :productId')
            ->andWhere('(m.buyer = :user1 AND u.id = :user2) OR (m.buyer = :user2 AND u.id = :user1)')
            ->setParameter('productId', $productId)
            ->setParameter('user1', $user1Id)
            ->setParameter('user2', $user2Id)
            ->orderBy('m.date', 'ASC');

        return $qb->getQuery()->getResult();
    }


}