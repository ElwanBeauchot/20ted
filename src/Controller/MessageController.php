<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class MessageController extends AbstractController
{
    #[Route('/user/me/messages', name: 'app_messages_buyer')]
    public function indexBuyer(MessageRepository $messageRepository): Response
    {
        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////

        $user = $this->getUser();

        $messagesBuyer = $messageRepository->findBy(['buyer' => $user] );
        $messagesSeller = $messageRepository->findBySeller($user);


        return $this->render('message/index.html.twig', [
            'messages' => $messagesSeller,
        ]);
    }

    #[Route('/user/{id}/messages', name: 'view_message')]
    public function view(Message $message): Response
    {
        //////////////////security////////////////////
        if($this->getUser() === null){
            return $this->redirectToRoute('app_login');
        }
        //////////////////////////////////////////////

        return $this->render('message/view.html.twig', [
            'message' => $message,
        ]);
    }
}
