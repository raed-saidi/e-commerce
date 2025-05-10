<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $featuredProducts = $productRepository->findBy([], ['id' => 'DESC'], 8);
        $categories = $categoryRepository->findAll();

        return $this->render('home/index.html.twig', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ]);
    }
}
