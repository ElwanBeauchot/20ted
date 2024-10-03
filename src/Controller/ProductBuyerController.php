<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\FavoriteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class ProductBuyerController extends AbstractController
{
    #[Route('/', name: 'app_product_buyer')]
    public function index(Request $request, ProductRepository $productRepository, FavoriteService $favoriteService, CategoryRepository $categoryRepository): Response
    {

        $productList = $productRepository->findAll();
        $categoryList = $categoryRepository->findAll();
        $updatedProductList = [];
        $bMe = false;
        $catalogUser = false;

        foreach ($productList as $product) {
            $favoriteService->updateFav($product);
            if($product->getUsers() !== $this->getUser()) {
                $updatedProductList[] = $product;
            }
        }

        return $this->render('catalog/index.html.twig', [
            'controller_name' => 'ProductBuyerController',
            'productList' => $updatedProductList,
            'categoryList' => $categoryList,
            'bMe' => $bMe,
            'catalogUser' => $catalogUser,
            'user' => $this->getUser(),
        ]);
    }

}
