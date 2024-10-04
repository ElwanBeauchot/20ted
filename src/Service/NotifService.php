<?php
namespace App\Service;

use App\Entity\Notification;
use App\Entity\Offer;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\SecurityUser;
use App\Repository\NotificationRepository;
use App\Repository\ProductRepository;
use App\Repository\SecurityUserRepository;
use Doctrine\Entity;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class NotifService
{
    public function __construct (private Security $security, private readonly EntityManagerInterface $entityManager, NotificationRepository $notificationRepository)
    {
    }

    public function notifOffer(Offer $offer): void
    {
        $buyer = $offer->getUsers();
        $seller = $offer->getProducts()->getUsers();
        $buyerName = $buyer->getUsername();
        $productTitle = $offer->getProducts()->getTitle();

        $text = "{$buyerName} a fait une offre pour {$productTitle}";

        $em = $this->entityManager;

        $notifOffer = new Notification();
        $notifOffer->setOffer($offer);
        $notifOffer->setProduct($offer->getProducts());
        $notifOffer->setBuyer($buyer);
        $notifOffer->setSeller($seller);
        $notifOffer->setSender($buyer->getId());
        $notifOffer->setText($text);

        $em->persist($notifOffer);
        $em->flush();
    }

    public function notifOrder(Order $order): void
    {

        // Création de la notification si elle n'existe pas
        $buyer = $order->getBuyer();
        $seller = $order->getSeller();
        $buyerName = $buyer->getUsername();
        $productTitle = $order->getProducts()->getTitle();

        $text = "{$seller->getUsername()} a accepté votre offre pour {$productTitle}, veuillez valider le paiement.";

        $notifOrder = new Notification();
        $notifOrder->setProduct($order->getProducts());
        $notifOrder->setBuyer($buyer);
        $notifOrder->setSeller($seller);
        $notifOrder->setSender($seller->getId());
        $notifOrder->setText($text);

        $this->entityManager->persist($notifOrder);
        $this->entityManager->flush();
    }

    public function notifFav(Product $product): void
    {

        $buyer = $this->security->getUser();
        $productTitle = $product->getTitle();

        $text = "{$buyer->getUsername()} a ajouté {$productTitle} à ses favoris.";

        $notifFav = new Notification();
        $notifFav->setProduct($product);
        $notifFav->setBuyer($buyer);
        $notifFav->setSeller($this->security->getUser());
        $notifFav->setSender($buyer->getId());
        $notifFav->setText($text);

        $this->entityManager->persist($notifFav);
        $this->entityManager->flush();
    }

}