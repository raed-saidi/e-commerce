<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EcommerceController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        // Dummy product data
        $products = [
            ['id' => 1, 'name' => 'iPhone 15', 'price' => 999, 'image' => '/images/iphone.jpg'],
            ['id' => 2, 'name' => 'MacBook Air', 'price' => 1299, 'image' => '/images/macbook.jpg'],
        ];

        return $this->render('pages/home.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'product_detail')]
    public function productDetail(int $id): Response
    {
        $product = [
            'id' => $id,
            'name' => 'Product ' . $id,
            'price' => 49.99,
            'description' => 'Product description here...',
            'image' => '/images/default.jpg',
        ];

        return $this->render('pages/product-detail.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/cart', name: 'cart')]
    public function cart(SessionInterface $session): Response
    {
        $cartItems = $session->get('cart', []);

        return $this->render('pages/cart.html.twig', [
            'cart_items' => $cartItems,
        ]);
    }


    #[Route('/checkout', name: 'checkout')]
    public function checkout(): Response
    {
        return $this->render('pages/checkout.html.twig');
    }
    #[Route('/cart/add', name: 'cart_add', methods: ['POST'])]
    public function addToCart(Request $request, SessionInterface $session): Response
    {
        $id = $request->request->get('product_id');
        $name = $request->request->get('product_name');
        $price = $request->request->get('product_price');

        $cart = $session->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += 1;
        } else {
            $cart[$id] = [
                'name' => $name,
                'price' => $price,
                'quantity' => 1,
            ];
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }
}
