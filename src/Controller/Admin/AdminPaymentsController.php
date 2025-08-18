<?php

namespace App\Controller\Admin;

use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class AdminPaymentsController extends AbstractController
{
    #[Route('/admin/payments', name: 'admin_payments_index')]
    public function index(PaymentRepository $paymentRepository): Response
    {
        $payments = $paymentRepository->findAll();

        return $this->render('admin/payments/index.html.twig', [
            'payments' => $payments,
        ]);
    }
}
