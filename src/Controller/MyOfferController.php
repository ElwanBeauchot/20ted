<?php

namespace App\Controller;

use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MyOfferController extends AbstractController
{
    #[Route('/my-offer', name: 'app_my_offer')]
    public function index(OfferRepository $offerRepository, Security $security): Response
    {
        $offerList = $offerRepository->findBy(['users' => $this->getUser()]);

        return $this->render('my_offer/index.html.twig', [
            'controller_name' => 'MyOfferController',
            'offerList' => $offerList,
        ]);
    }
}
