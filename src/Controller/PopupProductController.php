<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\AddProductType;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
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
            $new_product->setHoliday(0);
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

    #[Route('/user/me/billspdf', name: 'app_popup_product_billspdf')]
    public function billspdf(): Response
    {
        
         //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////

        $myOrdersSeller = $this->entityManagerInterface->getRepository(Order::class)->findBy(['seller' => $this->getUser()]);
        $myOrdersBuyer = $this->entityManagerInterface->getRepository(Order::class)->findBy(['buyer' => $this->getUser()]);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        $depense = 0;
        $revenu = 0;
        $total = 0;
        foreach($myOrdersSeller as $order){
            $revenu += $order->getProducts()->getPrice();
        }
        foreach($myOrdersBuyer as $order){
            $depense += $order->getProducts()->getPrice();
        }
        $total += $revenu - $depense;

        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('popup_product/billspdf.html.twig', [
            'myOrdersSeller' => $myOrdersSeller,
            'myOrdersBuyer' => $myOrdersBuyer,
            'depense' => $depense,
            'revenu' => $revenu,
            'total' => $total,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="bills.pdf"');

        return $response;
    }
}
