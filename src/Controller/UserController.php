<?php

namespace App\Controller;

use App\Entity\EnrollmentPeriod;
use App\Entity\Student;
use App\Entity\User;
use App\Form\ChangePhoneNumberType;
use App\Form\Settings\ChangePasswordFormType;
use App\Form\UserSettingType;
use App\Security\EmailVerifier;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route("/profile")]
final class UserController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier, private EmailService $emailService)
    {
    }

    #[Route('', name: 'app_user_profile')]
    public function profile(Request $request): Response
    {

        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir cette page.');
        }

        // ICI ON VERIFIE SI LE STUDENT N'EST PAS DEJA INSCRIT QUELQUE PART
        $students = $user->getStudents();

        $existingEnrollment = null;

        foreach ($students as $student) {
            foreach ($student->getEnrollments() as $enrollment) {
                $course = $enrollment->getCourse()->getName();
                $period = $enrollment->getEnrollmentPeriod()->getTitle();
                if (in_array($enrollment->getStatus(), ['pending', 'confirmed'], true)) {
                    $existingEnrollment = $enrollment;
                    break 2; // quitte les deux boucles directement
                }
            }
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'students' => $students,
            "existingEnrollment" => $existingEnrollment,
            "course" => $course,
            "period" => $period

        ]);
    }

    #[Route('/settings', name: 'app_user_profile_settings')]

    public function params(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserSettingType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();
            $this->addFlash("", "Votre profile a bien été modifié");
            return $this->redirectToRoute("app_user_profile");
        }

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir cette page.');
        }


        return $this->render('user/params.html.twig', [
            'user' => $user,
            "resetProfil" => $form
        ]);
    }

    #[Route('/settings/resetPassword', name: 'app_user_profile_settings_password', methods: ['GET', 'POST'])]
    public function password(
        Request $request,
        UserPasswordHasherInterface $hasher,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): Response {

        /** @var User $user */
        $user = $this->getUser();
        $resultSendEmail = $this->emailService->processSendingPasswordResetEmail(
            $user->getEmail(),
            $mailer,
            $translator
        );
        if ($resultSendEmail) {
            $this->addFlash('', 'Votre email est envoyé');
            return $this->redirectToRoute('app_user_profile_settings');

        }
        $this->addFlash('', 'Email à déjà été envoyé');
        return $this->redirectToRoute('app_user_profile_settings');
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
                ->subject('Confirmez votre adresse e-mail')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->redirectToRoute("app_user_profile");
    }
}
