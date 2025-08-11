<?php

namespace App\Controller\Admin;

use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCoursesController extends AbstractController
{
    #[Route('/admin/courses', name: 'admin_courses_index')]
    public function index(CourseRepository $courseRepository): Response
    {
        //Pour recuperer tous les cours
        $courses = $courseRepository->findAll();

        //Pour recuperer tous les cours avec leurs inscriptions et étudiants
        $courses = $courseRepository->findAllWithEnrollmentsAndStudents();

        return $this->render('admin/courses/index.html.twig', [
            'courses' => $courses,
        ]);
    }
}
