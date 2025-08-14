<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;




class EmailService
{

    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
    ) {
    }
    public function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer, TranslatorInterface $translator): bool
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        dump($user);
        if (!$user) {
            return false;
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     '%s - %s',
            //     $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
            //     $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            // ));
            dump($e);

            return false;
        }

        dump("email");
        $email = (new TemplatedEmail())

            // Modifier le from en fonction du mail dans le MAILER_DSN qui se trouve dans notre .env.local
            // Supprimer le messenger qui permet de filtrer les mails en les affichant dans notre bdd 
            // enlever messenger via cette commande "composer remove symfony/doctrine-messenger"

            ->from(new Address('yukamiro2@gmail.com', 'll-academy'))
            ->to((string) $user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        dump($email);

        $mailer->send($email);
        dump($email);
        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return true;
    }

    public function sendEmailNotif(MailerInterface $mailer, User $user)
    {
        $email = (new TemplatedEmail())

            // Modifier le from en fonction du mail dans le MAILER_DSN qui se trouve dans notre .env.local
            // Supprimer le messenger qui permet de filtrer les mails en les affichant dans notre bdd 
            // enlever messenger via cette commande "composer remove symfony/doctrine-messenger"

            ->from(new Address('yukamiro2@gmail.com', 'll-academy'))
            ->to((string) $user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig');

        dump($email);

        $mailer->send($email);
    }
}