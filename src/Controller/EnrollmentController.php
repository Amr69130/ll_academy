<?php

namespace App\Controller;

use App\Entity\Enrollment;
use App\Entity\Student;
use App\Form\EnrollmentType;
use App\Repository\EnrollmentPeriodRepository; // ⬅️ AJOUT
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class EnrollmentController extends AbstractController
{
    #[Route('/enrollment', name: 'app_enrollment')]
    public function index(): Response
    {
        throw new AccessDeniedException("Accès refusé");
    }

    #[Route('/student/{id}/enroll', name: 'student_enroll')]
    public function enroll(
        Student $student,
        Request $request,
        EntityManagerInterface $em,
        EnrollmentPeriodRepository $periodRepo // ⬅️ AJOUT
    ): Response {
        // Vérifie que le student appartient à l'user connecté
        if ($student->getUser() !== $this->getUser()) {
            throw new AccessDeniedException('Accès refusé.');
        }

        // Vérifie s'il a déjà une inscription en attente/confirmée
        $existingEnrollment = null;
        foreach ($student->getEnrollments() as $enrollment) {
            if (in_array($enrollment->getStatus(), ['pending', 'confirmed'], true)) {
                $existingEnrollment = $enrollment;
            }
        }
        if ($existingEnrollment) {
            $this->addFlash('warning', 'Cet élève est déjà inscrit à un cours.');
            return $this->redirectToRoute('app_user_profile');
        }

        // ⬅️ Récupère la période ouverte par défaut
        $defaultPeriod = $periodRepo->findDefaultOpenPeriod();
        if (!$defaultPeriod) {
            $this->addFlash('error', "Aucune période d'inscription ouverte.");
            return $this->redirectToRoute('app_user_profile');
        }

        // Crée l'inscription et POSE la période avant le form (évite NULL en DB)
        $enrollment = new Enrollment();
        $enrollment->setStudent($student);
        $enrollment->setEnrollmentPeriod($defaultPeriod);

        // Form (si ton type gère 'lock_period', tu peux le laisser)
        $form = $this->createForm(EnrollmentType::class, $enrollment, [
            'lock_period' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enrollment->setEnrollmentDate(new \DateTime());
            $enrollment->setStatus(Enrollment::STATUS_PENDING);

            $em->persist($enrollment);
            $em->flush();

            $this->addFlash('success', 'Inscription enregistrée avec succès.');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('enrollment/enroll.html.twig', [
            'form' => $form->createView(),
            'student' => $student,
        ]);
    }
}
