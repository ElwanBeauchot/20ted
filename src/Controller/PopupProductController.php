<?php

namespace App\Controller;

use App\Entity\Category;
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
        
        $new_product = new Product();
        $form = $this->createForm(AddProductType::class, $new_product);
        $form->handleRequest ($request );

        if ($form->isSubmitted () && $form->isValid()) {
            $new_product->setUsers($this->getUser());
            $new_product->setFav(0);
            $this->entityManagerInterface->persist($new_product);
            $this->entityManagerInterface->flush();
            return $this->redirectToRoute('app_productsseller');
        }

      return $this->render('popup_product/index.html.twig', [
         'form' => $form->createView(),
      ]);
    }
    #[Route('/user/me/edit-product/{id}', name: 'app_popup_product_edit')]
    public function edit(Request $request, $id): Response
    {

        $update_product = $this->entityManagerInterface->getRepository(Product::class)->find($id);
        $form = $this->createForm(AddProductType::class, $update_product);
        $form->handleRequest ($request );


        if ($form->isSubmitted () && $form->isValid()) {
            $this->entityManagerInterface->persist($update_product);
            $this->entityManagerInterface->flush();
            return $this->redirectToRoute('app_productsseller');
        }

        return $this->render('popup_product/index.html.twig', [
            'form' => $form->createView(),
         ]);
    }
    #[Route('/user/me/delete-product/{id}', name: 'app_popup_product_delete')]
    public function delete($id): Response
    {

        $update_product = $this->entityManagerInterface->getRepository(Product::class)->find($id);
        $this->entityManagerInterface->remove($update_product);
        $this->entityManagerInterface->flush();

        return $this->redirectToRoute('app_productsseller');

        return $this->render('popup_product/index.html.twig', [

         ]);
    }
}
