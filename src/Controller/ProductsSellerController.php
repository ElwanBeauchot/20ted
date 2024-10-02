<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductsSellerController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManagerInterface, private ProductRepository $productRepository)
    {
    }
    #[Route('/productsseller', name: 'app_productsseller')]
    public function index(): Response
    {
        
        $my_products = $this->productRepository->findBy(['user' => $this->getUser()]);
        // $my_products=[
        //     [
        //         'id' => 1,
        //         'title' => 'Produit 1',
        //         'description' => 'Description du produit 1',
        //         'price' => 100,
        //         'status' => 'active',
        //         'image' => 'https://via.placeholder.com/150',
        //     ],
        //     [
        //         'id' => 2,
        //         'title' => 'Produit 2',
        //         'description' => 'Description du produit 2',
        //         'price' => 200,
        //         'status' => 'inactive',
        //         'image' => 'https://via.placeholder.com/150',
        //     ],
            
        // ];

        return $this->render('products_seller/index.html.twig', [
            'my_products' => $my_products,
        ]);
    }
}
