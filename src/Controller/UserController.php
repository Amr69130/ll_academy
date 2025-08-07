<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Address;

final class UserController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/profile', name: 'app_user_profile')]
    public function profile(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir cette page.');
        }

        // Récupérer les élèves liés au parent (User)
        $students = $user->getStudents();

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'students' => $students,
        ]);
    }

    #[Route('/verify/mail', name: 'app_user_verify_mail')]
    public function verifyMail()
    {

        /** @var User $user*/

        $user = $this->getUser();
        if (!$user) {
            $this->redirectToRoute("app_home");
        }

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('yukamiro2@gmail.com', 'll-academy'))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->redirectToRoute("app_user_profile");
    }
}
