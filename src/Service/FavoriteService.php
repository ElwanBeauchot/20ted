<?php
namespace App\Service;

use App\Entity\Product;
use App\Entity\SecurityUser;
use App\Repository\ProductRepository;
use App\Repository\SecurityUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class FavoriteService
{
    public function __construct (private LoggerInterface $logger, private readonly EntityManagerInterface $entityManager, private Security $security, private ProductRepository $productRepository)
    {
    }

    public function updateFav($product): void
    {
        $em = $this->entityManager;
        $user = $this->security->getUser();
        $producted = $em->getRepository(Product::class)->find($product->getId());

        $producted->setFav(count($producted->getFavorite()));

        $em->persist($producted);
        $em->flush();
    }

    public function addFav (int $productId): bool
    {
        $em = $this->entityManager;
        $user = $this->security->getUser();
        $product = $em->getRepository(Product::class)->find($productId);

        if (!$product || !$user instanceof SecurityUser) {
            $this->logger->error('Failed to add');
            return false;
        }

        $product->addFavorite($user);

        $em->persist($product);
        $em->flush();
        $this->updateFav($product);

        return true;
    }

    public function removeFav (int $productId): bool
    {
        $em = $this->entityManager;
        $user = $this->security->getUser();
        $product = $em->getRepository(Product::class)->find($productId);

        if (!$product || !$user instanceof SecurityUser) {
            $this->logger->error('Failed to remove');
            return false;
        }

        $product->removeFavorite($user);

        $em->persist($product);
        $em->flush();
        $this->updateFav($product);
        return true;
    }

    public function checkFav (int $productId): bool
    {
        $em = $this->entityManager;
        $user = $this->security->getUser();
        $product = $em->getRepository(Product::class)->find($productId);

        if (!$product || !$user instanceof SecurityUser) {
            $this->logger->error('Failed to checked');
        }

        if ($product->getFavorite()->contains($user))
        {
            return true;
        }
        return false;
    }


}