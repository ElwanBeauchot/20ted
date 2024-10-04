<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Offer;
use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OfferRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\SecurityUserRepository;
use App\Service\NotifService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MyOfferController extends AbstractController
{
    #[Route('/transaction', name: 'app_my_offer')]
    public function index(OfferRepository $offerRepository, Security $security, OrderRepository $orderRepository): Response
    {

        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////

        $offerList = $offerRepository->findAll();
        $orderList = $orderRepository->findAll();

        $updatedOfferList = [];
        foreach ($offerList as $offer) {
            if($offer->getProducts()->getUsers() === $this->getUser()) {
                $updatedOfferList[] = $offer;
            }
        }
        $updatedOrderList = [];
        foreach ($orderList as $order) {
            if($order->getBuyer() === $this->getUser()) {
                $updatedOrderList[] = $order;
            }
        }

        return $this->render('transaction/index.html.twig', [
            'controller_name' => 'MyOfferController',
            'offerList' => $updatedOfferList,
            'orderList' => $updatedOrderList,
        ]);
    }

    #[Route('/my-offer/delete/{id}', name: 'app_my_offer_delete', methods: ['POST'])]
    public function delete(Offer $offer, EntityManagerInterface $entityManager, Request $request): Response
    {

        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////

        // CSRF token sert a supprimer securisé
        if ($this->isCsrfTokenValid('delete-offer-' . $offer->getId(), $request->request->get('_token'))) {

            $notif = $entityManager->getRepository(Notification::class)->findBy(['offer' => $offer->getId()]);
            foreach ($notif as $notification) {
                $entityManager->remove($notification);
            }
            $offer->getProducts()->setStatus(1);
            $entityManager->remove($offer);
            $entityManager->flush();

            return $this->redirectToRoute('app_my_offer');
        }
        return $this->redirectToRoute('app_my_offer');
    }

    #[Route('/my-offer/confirm/{offer}', name: 'app_my_offer_confirm', methods: ['POST'])]
    public function confirm(EntityManagerInterface $entityManager, Request $request, ProductRepository $productRepository, Offer $offer, NotifService $notifService): Response
    {

        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////

        $product = $offer->getProducts();

        $order = new Order();
        $order->setProducts($product);
        $order->setBuyer($offer->getUsers());
        $order->setSeller($this->getUser());
        $order->setAmount($offer->getPrice());
        $order->setStatus(0); // status 0 = en attente
        $offer->setStatus(1); //status 1 = offre acceptée
        $product->setStatus(0); //status 0 = produit vendu

        $entityManager->persist($offer);
        $entityManager->persist($order);
        $entityManager->persist($product);
        $entityManager->flush();

        $notifService->notifOrder($order);

        return $this->redirectToRoute('app_my_offer');

    }

    #[Route('/my-order/delete/{id}', name: 'app_my_order_delete', methods: ['POST'])]
    public function deleteOrder(Order $order, EntityManagerInterface $entityManager, Request $request): Response
    {

        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////


        // CSRF token sert a supprimer securisé
        if ($this->isCsrfTokenValid('delete-order-' . $order->getId(), $request->request->get('_token'))) {

            $order->getProducts()->setStatus(1);
            $entityManager->remove($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_my_offer');
        }
        return $this->redirectToRoute('app_my_offer');
    }

    #[Route('/my-order/confirm/{order}', name: 'app_my_order_confirm', methods: ['POST'])]
    public function confirmOrder(Order $order, EntityManagerInterface $entityManager, Request $request, SecurityUserRepository $securityUserRepository): Response
    {

        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////

        $user = $securityUserRepository->find($this->getUser());

        // CSRF token sert a supprimer securisé
        if ($this->isCsrfTokenValid('confirm-order-' . $order->getId(), $request->request->get('_token'))) {

            if ($user->getWallet() >= $order->getAmount()) {
                $order->setStatus(1);
                $user->setWallet($user->getWallet() - $order->getAmount());
                $order->getSeller()->setWallet($order->getSeller()->getWallet() + $order->getAmount());
                $entityManager->persist($user);
                $entityManager->persist($order);
                $entityManager->flush();
            }else{
                $this->addFlash('errorPayment', 'Fond Insuffisant');
            }
            return $this->redirectToRoute('app_my_offer');
        }
        return $this->redirectToRoute('app_my_offer');
    }

}
