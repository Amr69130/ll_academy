<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class AdminUsersController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_users_index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $em): RedirectResponse
    {
        // Vérification CSRF
        if (!$this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('admin_users_index');
        }

        // Vérifie si l'utilisateur a des étudiants inscrits à des cours
        $studentsWithEnrollments = array_filter(
            $user->getStudents()->toArray(),
            fn($student) => count($student->getEnrollments()) > 0
        );

        if (count($studentsWithEnrollments) > 0) {
            $this->addFlash('error', 'ATTENTION : cet utilisateur a des étudiants inscrits à des cours. Suppression impossible.');
            return $this->redirectToRoute('admin_users_index');
        }

        // Supprime l'utilisateur avec tous ses étudiants (cascade)
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', "L'utilisateur ainsi que tous ses étudiants supprimés avec succès.");
        return $this->redirectToRoute('admin_users_index');
    }
}
