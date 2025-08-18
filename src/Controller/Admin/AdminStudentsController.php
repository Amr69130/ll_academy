<?php

namespace App\Controller\Admin;

use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class AdminStudentsController extends AbstractController
{
    #[Route('/admin/students', name: 'admin_students_index')]
    public function index(StudentRepository $studentRepository): Response
    {
        $students = $studentRepository->findAllWithParentsAndEnrollments();


        return $this->render('admin/students/index.html.twig', [
            'students' => $students,
        ]);
    }
}
