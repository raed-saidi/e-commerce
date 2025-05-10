<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile_index')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    #[Route('/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Your profile has been updated!');

            return $this->redirectToRoute('profile_index');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/orders', name: 'profile_orders')]
    public function orders(): Response
    {
        $user = $this->getUser();
        $orders = $user->getOrders();

        return $this->render('profile/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/orders/{id}', name: 'profile_order_show')]
    public function showOrder(int $id): Response
    {
        $user = $this->getUser();
        $order = null;

        foreach ($user->getOrders() as $userOrder) {
            if ($userOrder->getId() === $id) {
                $order = $userOrder;
                break;
            }
        }

        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        return $this->render('profile/order_show.html.twig', [
            'order' => $order,
        ]);
    }
}