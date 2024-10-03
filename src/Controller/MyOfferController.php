<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Entity\Order;
use App\Entity\Product;
use App\Repository\OfferRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MyOfferController extends AbstractController
{
    #[Route('/my-offer', name: 'app_my_offer')]
    public function index(OfferRepository $offerRepository, Security $security): Response
    {
        $offerList = $offerRepository->findAll();

        $updatedOfferList = [];
        foreach ($offerList as $offer) {
            if($offer->getProducts()->getUsers() === $this->getUser()) {
                $updatedOfferList[] = $offer;
            }
        }

        return $this->render('my_offer/index.html.twig', [
            'controller_name' => 'MyOfferController',
            'offerList' => $updatedOfferList,
        ]);
    }

    #[Route('/my-offer/delete/{id}', name: 'app_my_offer_delete', methods: ['POST'])]
    public function delete(Offer $offer, EntityManagerInterface $entityManager, Request $request): Response
    {
        // CSRF token sert a supprimer securisé
        if ($this->isCsrfTokenValid('delete-offer-' . $offer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($offer);
            $entityManager->flush();

            return $this->redirectToRoute('app_my_offer');
        }
        return $this->redirectToRoute('app_my_offer');
    }

    #[Route('/my-offer/confirm/{offer}', name: 'app_my_offer_confirm', methods: ['POST'])]
    public function confirm(EntityManagerInterface $entityManager, Request $request, ProductRepository $productRepository, Offer $offer): Response
    {
        $product = $offer->getProducts();

        $order = new Order();
        $order->setProducts($product);
        $order->setBuyer($offer->getUsers());
        $order->setSeller($this->getUser());
        $order->setAmount($offer->getPrice());
        $offer->setStatus(0);
        $product->setStatus(0);
        $entityManager->persist($offer);
        $entityManager->persist($order);
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->redirectToRoute('app_my_offer');

    }
}
