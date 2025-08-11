<?php

namespace App\Controller\Admin;

use App\Repository\CourseRepository;
use App\Repository\EnrollmentRepository;
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


}
