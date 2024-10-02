<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SecurityUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{   
    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
    }
    #[Route('/user/me', name: 'app_me')]
    public function edit(): Response
    {        
        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////        
        $bMe = true;
        $my_products = $this->entityManagerInterface->getRepository(Product::class)->findBy(['users' => $this->getUser()]);

        return $this->render('user/index.html.twig', [
            'my_products' => $my_products,
            'bMe' => $bMe,
        ]);
    }

    #[Route('/user/{id}', name: 'app_user')]
    public function index($id): Response
    {
        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////
        
        $bMe = false;
        $user = $this->entityManagerInterface->getRepository(SecurityUser::class)->find($id);
        $my_products = $this->entityManagerInterface->getRepository(Product::class)->findBy(['users' => $user]);

        return $this->render('user/index.html.twig', [
            'my_products' => $my_products,
            'bMe' => $bMe,
        ]);
    }
}
