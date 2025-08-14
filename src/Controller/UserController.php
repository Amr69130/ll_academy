<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePhoneNumberType;
use App\Form\Settings\ChangePasswordFormType;
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
#[IsGranted("ROLE_USER")]
final class UserController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier, private EmailService $emailService)
    {
    }

    #[Route('/', name: 'app_user_profile')]
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

    #[Route('/settings', name: 'app_user_profile_settings')]

    public function params(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir cette page.');
        }

        return $this->render('user/params.html.twig', [
            'user' => $user,
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
            $this->addFlash('', 'Votre email est envoyer');
            return $this->redirectToRoute('app_user_profile_settings');

        }
        $this->addFlash('', 'Email à déjà été envoyer');
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
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return $this->redirectToRoute("app_user_profile");
    }

    #[Route('/settings/resetNumber', name: 'app_user_profile_settings_number', methods: ['GET', 'POST'])]

    public function number(Request $request, EntityManagerInterface $em): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePhoneNumberType::class, $user);

        $form->handleRequest($request);
        dump($form);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form);
            $em->persist($user);
            $em->flush();
            $this->addFlash("", "Votre numéro à bien été modifier");
            return $this->redirectToRoute("app_user_profile");
        }
        return $this->render("user/changePhoneNumber.html.twig", [
            "resetNumber" => $form
        ]);
    }
}
