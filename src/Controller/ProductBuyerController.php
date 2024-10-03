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
    #[Route('/catalog', name: 'app_product_buyer')]
    public function index(Request $request, ProductRepository $productRepository, FavoriteService $favoriteService, CategoryRepository $categoryRepository): Response
    {
        $categoryId = $request->query->get('category');
        $searchTerm = $request->query->get('search');

        $productList = $productRepository->findBySearch($categoryId, $searchTerm);
        $categoryList = $categoryRepository->findAll();
        $bMe = false;
        $catalogUser = false;

        foreach ($productList as $product) {
            $favoriteService->updateFav($product);
        }

        return $this->render('catalog/index.html.twig', [
            'controller_name' => 'ProductBuyerController',
            'productList' => $productList,
            'categoryList' => $categoryList,
            'bMe' => $bMe,
            'catalogUser' => $catalogUser,
            'user' => $this->getUser(),
        ]);
    }

}
