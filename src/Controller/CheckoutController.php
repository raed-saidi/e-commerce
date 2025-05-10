<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\CheckoutType;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/checkout')]
#[IsGranted('ROLE_USER')]
class CheckoutController extends AbstractController
{
    #[Route('/', name: 'checkout_index')]
    public function index(Request $request, CartService $cartService, EntityManagerInterface $entityManager): Response
    {
        $cart = $cartService->getCart();

        if (count($cart) === 0) {
            $this->addFlash('error', 'Your cart is empty');
            return $this->redirectToRoute('cart_index');
        }

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setTotal($cartService->getTotal());

        $form = $this->createForm(CheckoutType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Create order items
            foreach ($cart as $item) {
                $orderItem = new OrderItem();
                $orderItem->setProduct($item['product']);
                $orderItem->setQuantity($item['quantity']);
                $orderItem->setPrice($item['product']->getPrice());
                $orderItem->setTotal($item['product']->getPrice() * $item['quantity']);

                $order->addItem($orderItem);

                // Update product stock
                $product = $item['product'];
                $product->setStock($product->getStock() - $item['quantity']);
                $entityManager->persist($product);
            }

            $entityManager->persist($order);
            $entityManager->flush();

            // Clear the cart
            $cartService->clear();

            return $this->redirectToRoute('checkout_success', ['id' => $order->getId()]);
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart,
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/success/{id}', name: 'checkout_success')]
    public function success(Order $order): Response
    {
        // Ensure the order belongs to the current user
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You are not authorized to view this order');
        }

        return $this->render('checkout/success.html.twig', [
            'order' => $order,
        ]);
    }
}