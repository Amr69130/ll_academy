<?php

namespace App\Controller\Admin;

use App\Entity\EnrollmentPeriod;
use App\Repository\CourseRepository;
use App\Repository\EnrollmentRepository;
use App\Repository\EnrollmentPeriodRepository;
use App\Repository\PaymentRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin/dashboard/{selectedPeriodId}', name: 'admin_dashboard_index', defaults: ['selectedPeriodId' => '0'])]
    public function index(
        int    $selectedPeriodId,
        Request                    $request,
        StudentRepository          $studentRepo,
        CourseRepository           $courseRepo,
        EnrollmentRepository       $enrollmentRepo,
        PaymentRepository          $paymentRepo,
        EnrollmentPeriodRepository $periodRepo
    ): Response {
        // 1️⃣ Récupérer toutes les périodes
        $periods = $periodRepo->findAll();
        if ($selectedPeriodId == 0) {
            $selectedPeriod = $periodRepo->findOneBy(['isOpen' => true], ['id' => 'DESC']);
        }
        else{

        $selectedPeriod = $selectedPeriodId ? $periodRepo->find($selectedPeriodId) : null;
        }


        // 3️⃣ Compter avec filtre sur la période
        if ($selectedPeriod) {
            $totalStudents = $studentRepo->countByPeriod($selectedPeriod);
            $totalActiveCourses = $courseRepo->countByPeriod($selectedPeriod, true);
            $totalEnrollments = $enrollmentRepo->countByPeriodAndStatus($selectedPeriod, 'valid');
            $pendingPayments = $paymentRepo->countByPeriodAndStatus($selectedPeriod, 'pending');
            $pendingEnrollments = $enrollmentRepo->countByPeriodAndStatus($selectedPeriod, 'pending');
        } else {
            $totalStudents = $studentRepo->count([]);
            $totalActiveCourses = $courseRepo->count(['isOpen' => true]);
            $totalEnrollments = $enrollmentRepo->count(['status' => 'valid']);
            $pendingPayments = $paymentRepo->count(['status' => 'pending']);
            $pendingEnrollments = $enrollmentRepo->count(['status' => 'pending']);
        }

        return $this->render('admin/dashboard/index.html.twig', [
            'totalStudents' => $totalStudents,
            'totalActiveCourses' => $totalActiveCourses,
            'totalEnrollments' => $totalEnrollments,
            'pendingPayments' => $pendingPayments,
            'pendingEnrollments' => $pendingEnrollments,
            'periods' => $periods,
            'selectedPeriod' => $selectedPeriod,
        ]);
    }
}
