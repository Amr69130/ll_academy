<?php

namespace App\Controller\Admin;

use App\Repository\StudentRepository;
use App\Repository\EnrollmentPeriodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class AdminStudentsController extends AbstractController
{
    #[Route('/admin/students', name: 'admin_students_index')]
    public function index(
        StudentRepository $studentRepository,
        EnrollmentPeriodRepository $periodRepo,
        Request $request
    ): Response
    {
        // ICI LA METHODE QUI PREND TOUS LES ETUDIANTS MÊME NON INSCRITS (inutile pour le moment)
        // $students = $studentRepository->findAllWithParentsAndEnrollments();
        // ICI LA METHODE QUI PREND TOUS LES ETUDIANTS MÊME NON INSCRITS (inutile pour le moment)

        // ici on récupère l'ID de la période sélectionnée depuis l'URL (query param)
        $selectedPeriodId = $request->query->get('selectedPeriodId', 0);

        // ici on détermine la période sélectionnée : si aucun ID, prend la période ouverte la plus récente
        $selectedPeriod = $selectedPeriodId == 0
            ? $periodRepo->findOneBy(['isOpen' => true], ['id' => 'DESC'])
            : $periodRepo->find($selectedPeriodId);

        // ici on récupère les étudiants inscrits filtrés par période
        $students = $selectedPeriod
            ? $studentRepository->findWithEnrollmentsByPeriod($selectedPeriod)
            : $studentRepository->findAllWithEnrollmentsOnly();

        // ici on rend le Twig avec la liste des étudiants et la période sélectionnée
        return $this->render('admin/students/index.html.twig', [
            'students' => $students,
            'selectedPeriod' => $selectedPeriod,
        ]);
    }
}
