<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class OfferController extends AbstractController
{
    #[Route('/submit-offer/{productId}', name: 'submit_offer', methods: ['POST'])]
    public function submitOffer(Request $request, ProductRepository $productRepository, EntityManagerInterface $em, OfferRepository $offerRepository, int $productId): JsonResponse
    {
        //////////////////////////////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////
        
        $data = json_decode($request->getContent(), true);
        $offerPrice = $data['offerPrice'];

        $product = $productRepository->find($productId);

        if (!$product) {
            return new JsonResponse(['success' => false, 'message' => 'Product not found'], 404);
        }
        $offer = new Offer();

        $offer->setProducts($product);
        $offer->setPrice($offerPrice);
        $offer->setUsers($this->getUser());
        $offer->setStatus(0);

        $em->persist($offer);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
