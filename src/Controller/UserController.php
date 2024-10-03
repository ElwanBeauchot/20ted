<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SecurityUser;
use App\Repository\CategoryRepository;
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
    public function me(CategoryRepository $categoryRepository): Response
    {        
        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////        
        $bMe = true;
        $catalogUser = true;
        $productList = $this->entityManagerInterface->getRepository(Product::class)->findBy(['users' => $this->getUser()]);
        $categoryList = $categoryRepository->findAll();

        return $this->render('catalog/index.html.twig', [
            'productList' => $productList,
            'categoryList' => $categoryList,
            'bMe' => $bMe,
            'catalogUser' => $catalogUser,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_user')]
    public function index($id, CategoryRepository $categoryRepository): Response
    {
        $user = $this->entityManagerInterface->getRepository(SecurityUser::class)->find($id);
        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }else if($this->getUser() === $user){
            return $this->redirectToRoute('app_me');
        }else if($user === null){
            return $this->redirectToRoute('app_me');
        }
        //////////////////////////////////////////////
        
        $bMe = false;
        $catalogUser = true;
        $productList = $this->entityManagerInterface->getRepository(Product::class)->findBy(['users' => $user]);
        $categoryList = $categoryRepository->findAll();

        return $this->render('catalog/index.html.twig', [
            'productList' => $productList,
            'categoryList' => $categoryList,
            'catalogUser' => $catalogUser,
            'bMe' => $bMe,
            'user' => $user,
        ]);
    }
}
