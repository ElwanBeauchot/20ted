<?php

namespace App\Controller;

use App\Repository\SecurityUserRepository;
use App\Entity\SecurityUser;
use App\Form\FormupdateType;
use App\Form\FormUpdatemdpType;
use App\Form\WalletformType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;

class AccountPageController extends AbstractController
{

    #[Route('/account/page', name: 'app_account_page')]
    public function index(SecurityUserRepository $SecurityUserRepository, Request $request,EntityManagerInterface $entityManager): Response
    {

        //récupère les données utilisateurs

        $user = $SecurityUserRepository->findBy(['id' => $this->getUser()]);

        //modifie les données de user(username et mail) avec un form
        
        $userupdate = $entityManager->getRepository(SecurityUser::class)->find(id: $this->getUser());

        if (!$userupdate) {
            throw $this->createNotFoundException('User not found');
        }

        // Formulaire pour les informations utilisateur
        $formUser = $this->createForm(FormupdateType::class, $userupdate, [
            'method' => 'POST'
        ]);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_account_page');
        }

        // Formulaire pour le mot de passe
        $formPassword = $this->createForm(FormUpdatemdpType::class, $userupdate, [
            'method' => 'POST'
        ]);
        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            // Ici, tu peux gérer le mot de passe (pour l'instant, nous ne faisons rien avec le mot de passe)
            $entityManager->flush();
            return $this->redirectToRoute('app_account_page');
        }

        //Gestion du porte monnaie

        $walletform = $this->createForm(WalletformType::class);
        $walletform->handleRequest($request);

        if ($walletform->isSubmitted() && $walletform->isValid()) {
        
            $data = $walletform->getData();
            $amount =  $data['amount'];
            $operation = $data['operation'];

            if ($operation === 'add') {
                $userupdate->setWallet($userupdate->getWallet() + $amount);
            } elseif ($operation === 'subtract') {
                if ($amount <= $userupdate->getWallet()) {
                    $userupdate->setWallet($userupdate->getWallet() - $amount);
                } else {
                    $this->addFlash('error', 'Le montant dépasse votre solde.');
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_account_page');
        }

        return $this->render('account_page/index.html.twig', [
            'user' => $user,
            'formUser' => $formUser->createView(),
            'formPassword' => $formPassword->createView(),
            'walletForm' => $walletform->createView(),
        ]);
    }
}
