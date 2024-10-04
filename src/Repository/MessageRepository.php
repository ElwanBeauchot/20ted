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

}
