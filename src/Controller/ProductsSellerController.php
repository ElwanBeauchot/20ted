<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;
class ProductsSellerController extends AbstractController
{
    #[Route('/productsseller', name: 'app_productsseller')]
    public function index(): Response
    {
        //$products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $products=[
            [
                'id' => 1,
                'title' => 'Produit 1',
                'description' => 'Description du produit 1',
                'price' => 100,
                'status' => 'active',
                'image' => 'https://via.placeholder.com/150',
            ],
            [
                'id' => 2,
                'title' => 'Produit 2',
                'description' => 'Description du produit 2',
                'price' => 200,
                'status' => 'inactive',
                'image' => 'https://via.placeholder.com/150',
            ],
            
        ];
        return $this->render('products_seller/index.html.twig', [
            'products' => $products,
        ]);
    }
}
