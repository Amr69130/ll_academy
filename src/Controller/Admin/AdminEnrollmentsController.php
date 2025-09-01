<?php

namespace App\Controller\Admin;

use App\Entity\Enrollment;
use App\Repository\CourseRepository;
use App\Repository\EnrollmentRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AdminEnrollmentsController extends AbstractController
{
    public function __construct(private EmailService $emailService)
    {
    }

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
    public function validate(Enrollment $enrollment, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $enrollment->setStatus('valid');
        $em->flush();


        $this->emailService->emailStudentValidated(
            $enrollment,
            $mailer,
        );

        $this->addFlash('success', 'Inscription validée avec succès !');

        return $this->redirectToRoute('admin_enrollments_pending');
    }

    #[Route('/admin/enrollments/{id}/reject', name: 'admin_enrollments_reject', methods: ['POST'])]
    public function reject(Enrollment $enrollment, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $enrollment->setStatus('rejected');
        $em->flush();

        /** @var User $user */
        $user = $this->getUser();
        $this->emailService->emailStudentRefuse(
            $user->getEmail(),
            $mailer,
        );

        $this->addFlash('success', 'Inscription refusée avec succès !');

        return $this->redirectToRoute('admin_enrollments_pending');
    }

}
