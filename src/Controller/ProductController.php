<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ReviewType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'product_index')]
    public function index(
        Request $request,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $categoryId = $request->query->get('category');
        $category = $categoryId ? $categoryRepository->find($categoryId) : null;

        $products = $category
            ? $productRepository->findBy(['category' => $category])
            : $productRepository->findAll();

        $categories = $categoryRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $category,
        ]);
    }

    #[Route('/{id}', name: 'product_show', requirements: ['id' => '\d+'])]
    public function show(Product $product): Response
    {
        $reviewForm = $this->createForm(ReviewType::class);

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'reviewForm' => $reviewForm->createView(),
        ]);
    }
}
