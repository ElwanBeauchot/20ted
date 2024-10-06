<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\AddProductType;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PopupProductController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManagerInterface)
    {
        
    }
    #[Route('/user/me/add-product', name: 'app_popup_product')]
    public function index(Request $request): JsonResponse
    {
        $form = $this->createForm(AddProductType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new_product = $form->getData();
            $new_product->setUsers($this->getUser());
            $new_product->setFav(0);
            $new_product->setStatus(1);

            try {
                $this->entityManagerInterface->persist($new_product);
                $this->entityManagerInterface->flush();
                return $this->json(['success' => true]);
            } catch (\Exception $e) {
                return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            return $this->json(['success' => false, 'message' => 'Form validation failed', 'errors' => $errors], 400);
        }

        return $this->json(['success' => false, 'message' => 'Invalid request'], 400);
    }
    #[Route('/user/me/edit-product/{productId}', name: 'app_popup_product_edit')]
    public function edit(Request $request, $productId): JsonResponse
    {
        $update_product = $this->entityManagerInterface->getRepository(Product::class)->find($productId);

        $form = $this->createForm(AddProductType::class, $update_product);

        if ($request->isMethod('GET')) {
            return $this->json([
                'success' => true,
                'product' => [
                    'id' => $update_product->getId(),
                    'title' => $update_product->getTitle(),
                    'description' => $update_product->getDescription(),
                    'price' => $update_product->getPrice(),
                    'imageUrl' => $update_product->getImageUrl(),
                    'categories' => $update_product->getCategories()->getId(),
                ]
            ]);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManagerInterface->flush();
                return $this->json(['success' => true]);
            } catch (\Exception $e) {
                return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }

        return $this->json(['success' => false, 'message' => 'Invalid request'], 400);
    }

    #[Route('/user/me/delete-product/{productId}', name: 'app_popup_product_delete')]
    public function delete($productId): JsonResponse
    {
        $product = $this->entityManagerInterface->getRepository(Product::class)->find($productId);

        try {
            $this->entityManagerInterface->remove($product);
            $this->entityManagerInterface->flush();
            return $this->json(['success' => true, 'message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    #[Route('/user/me/billspdf', name: 'app_popup_product_billspdf')]
    public function billspdf(): Response
    {
        $myOrdersSeller = $this->entityManagerInterface->getRepository(Order::class)->findBy(['seller' => $this->getUser()]);
        $myOrdersBuyer = $this->entityManagerInterface->getRepository(Order::class)->findBy(['buyer' => $this->getUser()]);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        $depense = 0;
        $revenu = 0;
        $total = 0;
        foreach($myOrdersSeller as $order){
            $revenu += $order->getAmount();
        }
        foreach($myOrdersBuyer as $order){
            $depense += $order->getAmount();
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
