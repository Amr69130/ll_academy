<?php

namespace App\Controller;

use App\Entity\Enrollment;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/payment/{enrollment}', name: 'payment_new')]
    public function new(Enrollment $enrollment, Request $request, EntityManagerInterface $em): Response
    {
        // Ici tu pourrais vérifier que l'utilisateur a le droit de payer cette inscription

        if ($request->isMethod('POST')) {
            // Traitement simple du formulaire (par exemple juste créer un paiement)

            $payment = new Payment();
            $payment->setEnrollment($enrollment);
            $payment->setAmount($enrollment->getCourse()->getPrice());
            $payment->setPaymentDate(new \DateTime());
            $payment->setStatus('completed');

            $em->persist($payment);
            $em->flush();

            $this->addFlash('success', 'Paiement enregistré avec succès.');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('payment/new.html.twig', [
            'enrollment' => $enrollment,
        ]);
    }
}
