<?php

namespace App\Controller\Admin;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use App\Repository\EnrollmentPeriodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class AdminPaymentsController extends AbstractController
{
    #[Route('/admin/payments', name: 'admin_payments_index')]
    public function index(PaymentRepository $paymentRepository): Response
    {
        // ici on récupère tous les paiements
        $payments = $paymentRepository->findAll();

        // ici on rend le Twig avec tous les paiements
        return $this->render('admin/payments/index.html.twig', [
            'payments' => $payments,
        ]);
    }

    #[Route('/admin/payments/pending', name: 'admin_payments_pending')]
    public function pending(
        PaymentRepository $paymentRepository,
        EnrollmentPeriodRepository $periodRepo,
        Request $request
    ): Response
    {
        // ici on récupère l'ID de la période sélectionnée depuis l'URL (query param)
        $selectedPeriodId = $request->query->get('selectedPeriodId', 0);

        // ici on détermine la période sélectionnée : si aucun ID, prend la période ouverte la plus récente
        $selectedPeriod = $selectedPeriodId == 0
            ? $periodRepo->findOneBy(['isOpen' => true], ['id' => 'DESC'])
            : $periodRepo->find($selectedPeriodId);

        // ici on récupère les paiements pending filtrés par période
        // si aucune période, on récupère tous les paiements pending
        $payments = $selectedPeriod
            ? $paymentRepository->findByPeriodAndStatus($selectedPeriod, 'pending')
            : $paymentRepository->findBy(['status' => 'pending']);

        // ici on rend le Twig avec la liste des paiements et la période sélectionnée
        return $this->render('admin/payments/pending.html.twig', [
            'payments' => $payments,
            'selectedPeriod' => $selectedPeriod,
        ]);
    }
}
