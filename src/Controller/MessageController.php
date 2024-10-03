<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\Product;
use App\Entity\SecurityUser;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class MessageController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }
    #[Route('/messages', name: 'app_messages')]
    public function index(MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $messages = $messageRepository->findLastMessages($user);

        foreach ($messages as $message) {
            if ($message->getBuyer()->getId() == $user->getId()) {
                $message->interlocuteur = $message->getProducts()->getUsers();
            } else {
                $message->interlocuteur = $message->getBuyer();
            }
        }


        return $this->render('message/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/messages/view/{productId}/{buyerId}/{sellerId}', name: 'view_message')]
    public function view(Request $request, MessageRepository $messageRepository, int $productId, int $buyerId, int $sellerId): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $messages = $messageRepository->showDiscussion($productId, $buyerId, $sellerId);

        $buyer = $this->entityManager->getRepository(SecurityUser::class)->find($buyerId);
        $product = $this->entityManager->getRepository(Product::class)->find($productId);
        $seller = $this->entityManager->getRepository(SecurityUser::class)->find($sellerId);
        $user = $this->getUser()->getId();

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $message->setBuyer($buyer);
            $message->setSender($this->getUser());
            $message->setProducts($product);

            $notification = new Notification();
            $notification->setSeller($seller);
            $notification->setBuyer($buyer);
            $notification->setProduct($product);
            $notification->setText("Vous avez reçu un message !");
            $notification->setSender($user);

            $this->entityManager->persist($message);
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
            return $this->redirectToRoute('view_message', [
                'productId' => $productId,
                'buyerId' => $buyerId,
                'sellerId' => $sellerId,
            ]);

        }

        return $this->render('message/view.html.twig', [
            'messages' => $messages,
            'form' => $form->createView(),
        ]);
    }
}
