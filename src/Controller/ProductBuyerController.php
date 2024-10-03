<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SecurityUser;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SecurityUserRepository;
use App\Service\FavoriteService;
use Doctrine\ORM\EntityManagerInterface;
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

        $productList = $productRepository->findAll();
        $categoryList = $categoryRepository->findAll();
        $updatedProductList = [];
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
        ]);
    }

}
