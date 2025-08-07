<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {
        $cart = $cartService->getFullCart();
        $total = $cartService->getTotal();
        $user = $this->getUser();
        $students = $user ? $user->getStudents() : [];

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'total' => $total,
            'students' => $students,
        ]);
    }


    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(int $id, CartService $cartService)
    {
        $cartService->add($id);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(CartService $cartService): Response
    {
        $cartService->clear();
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/checkout', name: 'cart_checkout')]
    public function checkout(CartService $cartService): Response
    {
        $cart = $cartService->getFullCart();
        $total = $cartService->getTotal();

        // Pour commencer, on affiche un résumé simple
        return $this->render('cart/checkout.html.twig', [
            'cart' => $cart,
            'total' => $total,
        ]);
    }
}
