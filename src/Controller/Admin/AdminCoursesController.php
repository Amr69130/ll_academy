<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\EnrollmentPeriodRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
#[Route('/admin/courses')]
class AdminCoursesController extends AbstractController
{
    #[Route('', name: 'admin_courses_index')]
    public function index(CourseRepository $courseRepository): Response
    {
        $courses = $courseRepository->findAllWithEnrollmentsAndStudents();

        return $this->render('admin/courses/index.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/open/{selectedPeriodId}', name: 'admin_courses_open', defaults: ['selectedPeriodId' => 0])]
    public function openCourses(
        int $selectedPeriodId,
        CourseRepository $courseRepo,
        EnrollmentPeriodRepository $periodRepo
    ): Response {
        $selectedPeriod = $selectedPeriodId
            ? $periodRepo->find($selectedPeriodId)
            : $periodRepo->findOneBy(['isOpen' => true], ['id' => 'DESC']);

        $openCourses = $courseRepo->findOpenCoursesByPeriod($selectedPeriod);

        return $this->render('admin/courses/open.html.twig', [
            'openCourses' => $openCourses,
            'selectedPeriod' => $selectedPeriod,
        ]);
    }

    #[Route('/new', name: 'admin_courses_new')]
    public function new(Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        // On vérifie que le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $flagFile = $form->get('flagPicture')->getData();
            if ($flagFile) {
                $fileName = $fileUploader->upload($flagFile);
                $course->setFlagPicture($fileName);
            }

            $em->persist($course);
            $em->flush();

            $this->addFlash('success', 'Cours créé avec succès !');
            return $this->redirectToRoute('admin_courses_index');
        }

        // Si formulaire invalide ou pas soumis, on renvoie la vue avec les erreurs
        return $this->render('admin/courses/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_courses_edit')]
    public function edit(Course $course, Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $flagFile = $form->get('flagPicture')->getData();
            if ($flagFile) {
                $fileName = $fileUploader->upload($flagFile);
                $course->setFlagPicture($fileName);
            }

            $em->flush();

            $this->addFlash('success', 'Cours mis à jour avec succès !');
            return $this->redirectToRoute('admin_courses_index');
        }

        return $this->render('admin/courses/edit.html.twig', [
            'form' => $form->createView(),
            'course' => $course,
        ]);
    }
}
