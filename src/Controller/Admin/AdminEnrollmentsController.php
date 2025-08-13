<?php

namespace App\Controller\Admin;

use App\Entity\Enrollment;
use App\Repository\CourseRepository;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminEnrollmentsController extends AbstractController
{
    #[Route('/admin/enrollments', name: 'admin_enrollments_index')]



    public function index(EnrollmentRepository $enrollmentRepository): Response
    {
        $enrollments = $enrollmentRepository->findAll();

        return $this->render('admin/enrollments/index.html.twig', [
            'enrollments' => $enrollments,
        ]);
    }


    #[Route('/admin/enrollments/pending', name: 'admin_enrollments_pending')]
    public function pending(EnrollmentRepository $enrollmentRepository): Response
    {
        $enrollments = $enrollmentRepository->findBy(['status' => 'pending']);

        return $this->render('admin/enrollments/pending.html.twig', [
            'enrollments' => $enrollments,
        ]);
    }

    #[Route('/admin/enrollments/{id}/validate', name: 'admin_enrollments_validate', methods: ['POST'])]
    public function validate(Enrollment $enrollment, EntityManagerInterface $em): Response
    {
        $enrollment->setStatus('valid');
        $em->flush();

        $this->addFlash('success', 'Inscription validée avec succès !');

        return $this->redirectToRoute('admin_enrollments_pending');
    }

    #[Route('/admin/enrollments/{id}/reject', name: 'admin_enrollments_reject', methods: ['POST'])]
    public function reject(Enrollment $enrollment, EntityManagerInterface $em): Response
    {
        $enrollment->setStatus('rejected');
        $em->flush();

        $this->addFlash('success', 'Inscription refusée avec succès !');

        return $this->redirectToRoute('admin_enrollments_pending');
    }

}
