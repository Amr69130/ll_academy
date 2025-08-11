<?php

namespace App\Controller\Admin;

use App\Repository\CourseRepository;
use App\Repository\EnrollmentRepository;
use App\Repository\PaymentRepository;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard_index')]
    public function index(StudentRepository $studentRepo, CourseRepository $courseRepo, EnrollmentRepository $enrollmentRepo, PaymentRepository $paymentRepo): Response
    {
        $totalStudents = $studentRepo->count([]);
        $totalActiveCourses = $courseRepo->count(['isOpen' => true]);
        $totalEnrollments = $enrollmentRepo->count(['status' => 'valid']); // adapte selon tes statuts
        $pendingPayments = $paymentRepo->count(['status' => 'pending']);  // idem ici

        return $this->render('admin/dashboard/index.html.twig', [
            'totalStudents' => $totalStudents,
            'totalActiveCourses' => $totalActiveCourses,
            'totalEnrollments' => $totalEnrollments,
            'pendingPayments' => $pendingPayments,
        ]);
    }
}
