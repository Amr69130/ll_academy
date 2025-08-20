<?php

namespace App\Controller\Admin;

use App\Entity\Enrollment;
use App\Repository\EnrollmentPeriodRepository;
use App\Repository\EnrollmentRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
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
    public function pending(
        EnrollmentRepository $enrollmentRepository,
        EnrollmentPeriodRepository $periodRepo,
        Request $request
    ): Response
    {
        $selectedPeriodId = $request->query->get('selectedPeriodId', 0);

        $selectedPeriod = $selectedPeriodId == 0
            ? $periodRepo->findOneBy(['isOpen' => true], ['id' => 'DESC'])
            : $periodRepo->find($selectedPeriodId);

        $enrollments = $selectedPeriod
            ? $enrollmentRepository->findByPeriodAndStatus($selectedPeriod, 'pending')
            : $enrollmentRepository->findBy(['status' => 'pending']);

        return $this->render('admin/enrollments/pending.html.twig', [
            'enrollments' => $enrollments,
            'selectedPeriod' => $selectedPeriod,
        ]);
    }

    #[Route('/admin/enrollments/approved', name: 'admin_enrollments_approved')]
    public function approved(
        EnrollmentRepository $enrollmentRepository,
        EnrollmentPeriodRepository $periodRepo,
        Request $request
    ): Response
    {
        $selectedPeriodId = $request->query->get('selectedPeriodId', 0);

        $selectedPeriod = $selectedPeriodId == 0
            ? $periodRepo->findOneBy(['isOpen' => true], ['id' => 'DESC'])
            : $periodRepo->find($selectedPeriodId);

        $enrollments = $selectedPeriod
            ? $enrollmentRepository->findByPeriodAndStatus($selectedPeriod, 'approved')
            : $enrollmentRepository->findBy(['status' => 'approved']);

        return $this->render('admin/enrollments/approved.html.twig', [
            'enrollments' => $enrollments,
            'selectedPeriod' => $selectedPeriod,
        ]);
    }

    #[Route('/admin/enrollments/{id}/validate', name: 'admin_enrollments_validate', methods: ['POST'])]
    public function validate(Enrollment $enrollment, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $enrollment->setStatus('approved'); // <- remplacé valid par approved
        $em->flush();

        /** @var User $user */
        $user = $this->getUser();
        $this->emailService->emailStudentValidated(
            $user->getEmail(),
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
