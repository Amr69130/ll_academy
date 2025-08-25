<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class StudentController extends AbstractController
{
    #[Route('/student/new', name: 'student_new')]
    public function new(Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $student = new Student();
        $student->setUser($this->getUser());

        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('profilePicture')->getData(); // 'picture' = nom du champ dans StudentType
            if ($pictureFile) {
                $fileName = $fileUploader->upload($pictureFile);
                $student->setProfilePicture($fileName); // setter de l'entité
            }

            $em->persist($student);
            $em->flush();

            $this->addFlash('success', 'Fiche élève créée avec succès.');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('student/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/student/{id}', name: 'student_show', methods: ['GET'])]
    public function show(Student $student): Response
    {
        // Vérifie que le student appartient bien à l'utilisateur connecté
        if ($student->getUser() !== $this->getUser()) {
            throw new AccessDeniedException('Accès refusé.');
        }

        return $this->render('student/show.html.twig', [
            'student' => $student,
            'profilePictureUrl' => $student->getProfilePicture() ? '/uploads/profile/' . $student->getProfilePicture() : null

        ]);
    }

    #[Route('/student/{id}/edit', name: 'student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        if ($student->getUser() !== $this->getUser()) {
            throw new AccessDeniedException('Accès refusé.');
        }

        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('profilePicture')->getData();
            if ($pictureFile) {
                $fileName = $fileUploader->upload($pictureFile);
                $student->setProfilePicture($fileName);
            }

            $em->flush();

            $this->addFlash('success', 'Fiche élève modifiée avec succès.');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('student/edit.html.twig', [
            'form' => $form->createView(),
            'student' => $student,
            'profilePictureUrl' => $student->getProfilePicture() ? '/uploads/profile/' . $student->getProfilePicture() : null

        ]);
    }

    #[Route('/student/{id}', name: 'student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, EntityManagerInterface $em): Response
    {
        if ($student->getUser() !== $this->getUser()) {
            throw new AccessDeniedException('Accès refusé.');
        }

        if ($this->isCsrfTokenValid('delete' . $student->getId(), $request->request->get('_token'))) {
            $em->remove($student);
            $em->flush();
            $this->addFlash('success', 'Fiche élève supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_user_profile');
    }
}
