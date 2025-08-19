<?php

namespace App\Controller\Admin;

use App\Repository\CourseRepository;
use App\Repository\EnrollmentPeriodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        // ici j récupére tous les cours avec leurs inscriptions et étudiants
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
        // ici je récupére la période sélectionnée ou la période ouverte la plus récente
        $selectedPeriod = $selectedPeriodId
            ? $periodRepo->find($selectedPeriodId)
            : $periodRepo->findOneBy(['isOpen' => true], ['id' => 'DESC']);

        // ici je récupére les cours ouverts pour cette période
        $openCourses = $courseRepo->findOpenCoursesByPeriod($selectedPeriod);

        return $this->render('admin/courses/open.html.twig', [
            'openCourses' => $openCourses,
            'selectedPeriod' => $selectedPeriod,
        ]);
    }
}
