<?php

namespace App\Controller;

use App\Entity\Enrollment;
use App\Entity\Student;
use App\Form\EnrollmentType;
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
        return $this->render('enrollment/index.html.twig', [
            'controller_name' => 'EnrollmentController',
        ]);
    }

    #[Route('/student/{id}/enroll', name: 'student_enroll')]
    public function enroll(Student $student, Request $request, EntityManagerInterface $em): Response
    {
        // ICI ON VERIFIE QUE LE STUDENT "APPARTIENT" BIEN A L'USER CONNECTE
        if ($student->getUser() !== $this->getUser()) {
            throw new AccessDeniedException('Accès refusé.');
        }


        // ICI ON VERIFIE SI LE STUDENT N'EST PAS DEJA INSCRIT QUELQUE PART
        $existingEnrollment = null;
        foreach ($student->getEnrollments() as $enrollment) {
            if (in_array($enrollment->getStatus(), ['pending', 'confirmed'])) {
                $existingEnrollment = $enrollment;
                break;
            }
        }
        if ($existingEnrollment) {
            $this->addFlash('warning', 'Cet élève est déjà inscrit à un cours.');
            return $this->redirectToRoute('student_show', ['id' => $student->getId()]);
        }



        $enrollment = new Enrollment();
        $enrollment->setStudent($student);

        $form = $this->createForm(EnrollmentType::class, $enrollment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $enrollment->setEnrollmentDate(new \DateTime());
            $enrollment->setStatus('pending');

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
