<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\FavoriteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductBuyerController extends AbstractController
{
    #[Route('/product/buyer', name: 'app_product_buyer')]
    public function index(ProductRepository $productRepository, FavoriteService $favoriteService): Response
    {
        $productList = $productRepository->findAll();
        foreach ($productList as $product) {
            $favoriteService->updateFav($product);
        }

        return $this->render('product_buyer/index.html.twig', [
            'controller_name' => 'ProductBuyerController',
            'productList' => $productList,
        ]);
    }
}
