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
    #[Route('/productsseller/popupProduct', name: 'app_popup_product')]
    public function index(Request $request): Response
    {
        // $new_categorie= new Category();
        // $new_categorie->setName('VêtementS');
        // $this->entityManagerInterface->persist($new_categorie);
        // $this->entityManagerInterface->flush();

        
        $new_product = new Product();
        $form = $this->createForm(AddProductType::class, $new_product);
        $form->handleRequest ($request );

        if ($form->isSubmitted () && $form->isValid()) {
            dd($this->getUser());
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
}
