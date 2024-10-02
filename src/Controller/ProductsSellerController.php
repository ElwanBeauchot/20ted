<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SecurityUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Security\Model\Authenticator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductsSellerController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
    }
    #[Route('/user/me', name: 'app_me')]
    public function edit(): Response
    {        
        $my_products = $this->entityManagerInterface->getRepository(Product::class)->find(['users' => $this->getUser()]);

        return $this->render('products_seller/index.html.twig', [
            'my_products' => $my_products,
        ]);
    }

    #[Route('/user/{id}', name: 'app_user')]
    public function index($id): Response
    {
        $user = $this->entityManagerInterface->getRepository(SecurityUser::class)->find($id);
        $my_products = $this->entityManagerInterface->getRepository(Product::class)->find(['users' => $user]);

        return $this->render('products_seller/index.html.twig', [
            'my_products' => $my_products,
        ]);
    }
}
