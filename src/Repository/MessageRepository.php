<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findLastMessages($user): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m')
            ->join('m.products', 'p')
            ->join('p.users', 'u')
            ->where('m.buyer = :user OR u.id = :user')
            ->setParameter('user', $user->getId())
            ->orderBy('m.date', 'DESC');

        $messages = $qb->getQuery()->getResult();
        $lastMessages = [];
        foreach ($messages as $message) {
            $productId = $message->getProducts()->getId();
            $sellerId = $message->getProducts()->getUsers()->getId();
            $buyerId = $message->getBuyer()->getId();
            $key = $productId . '-' . $sellerId . '-' . $buyerId;
            if (!isset($lastMessages[$key])) {
                $lastMessages[$key] = $message;
            }
        }
        return array_values($lastMessages);
    }

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
