<?php

namespace App\Controller;

use App\Service\FavoriteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteController extends AbstractController
{
    private FavoriteService $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    /**
     * @Route("/add-favorite/{productId}", name="add_favorite", methods={"POST"})
     */
    public function addFavorite(int $productId): JsonResponse
    {
        $this->favoriteService->addFav($productId);
        return new JsonResponse(['success' => true, 'message' => 'Added to favorites!']);
    }
    /**
     * @Route("/remove-favorite/{productId)}", name="remove_favorite", methods={"POST"})
     */
    public function removeFavorite(int $productId): JsonResponse
    {
        $this->favoriteService->removeFav($productId);
        return new JsonResponse(['success' => true, 'message' => 'Removed from favorites!']);
    }
}
