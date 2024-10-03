<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PopupProductController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
    }
    #[Route('/user/me/add-product', name: 'app_popup_product')]
    public function index(Request $request): Response
    {
        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////
        
        $new_product = new Product();
        $form = $this->createForm(AddProductType::class, $new_product);
        $form->handleRequest ($request );

        if ($form->isSubmitted () && $form->isValid()) {
            $new_product->setUsers($this->getUser());
            $new_product->setFav(0);
            $new_product->setStatus(1);
            $this->entityManagerInterface->persist($new_product);
            $this->entityManagerInterface->flush();
            return $this->redirectToRoute('app_me');
        }

      return $this->render('popup_product/index.html.twig', [
         'form' => $form->createView(),
      ]);
    }
    #[Route('/user/me/edit-product/{id}', name: 'app_popup_product_edit')]
    public function edit(Request $request, $id): Response
    {
        $update_product = $this->entityManagerInterface->getRepository(Product::class)->find($id);

        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }else if($update_product === null){
            return $this->redirectToRoute('app_me');
        }else if($this->getUser() !== $this->entityManagerInterface->getRepository(Product::class)->find($id)->getUsers()){
            return $this->redirectToRoute('app_me');
        }
        //////////////////////////////////////////////

        $form = $this->createForm(AddProductType::class, $update_product);
        $form->handleRequest ($request );


        if ($form->isSubmitted () && $form->isValid()) {
            $this->entityManagerInterface->persist($update_product);
            $this->entityManagerInterface->flush();
            return $this->redirectToRoute('app_me');
        }

        return $this->render('popup_product/index.html.twig', [
            'form' => $form->createView(),
         ]);
    }
    #[Route('/user/me/delete-product/{id}', name: 'app_popup_product_delete')]
    public function delete($id): Response
    {
        $update_product = $this->entityManagerInterface->getRepository(Product::class)->find($id);

         //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }else if($update_product === null){
            return $this->redirectToRoute('app_me');
        }else if($this->getUser() !== $this->entityManagerInterface->getRepository(Product::class)->find($id)->getUsers()){
            return $this->redirectToRoute('app_me');
        }
        //////////////////////////////////////////////

        $this->entityManagerInterface->remove($update_product);
        $this->entityManagerInterface->flush();

        return $this->redirectToRoute('app_me');
    }
}
