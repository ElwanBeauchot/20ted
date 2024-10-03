<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class MessageController extends AbstractController
{
    #[Route('/messages', name: 'app_messages_buyer')]
    public function indexBuyer(MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $messagesBuyer = $messageRepository->findBy(['buyer' => $user] );
        $messagesSeller = $messageRepository->findBySeller($user);


        return $this->render('message/index.html.twig', [
            'messages' => $messagesSeller,
        ]);
    }

    #[Route('/messages/{id}', name: 'view_message')]
    public function view(Message $message): Response
    {
        return $this->render('message/view.html.twig', [
            'message' => $message,
        ]);
    }
}
