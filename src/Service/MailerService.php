<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class MailerService
{
    private $mailer;
    private $router;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router )
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function sendConfirmationEmail(string $to, string $confirmationToken)
    {
        $confirmationUrl = $this->router->generate('app_confirm_account', ['token' => $confirmationToken], UrlGeneratorInterface::ABSOLUTE_URL);
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($to)
            ->subject('Confirmation d\'inscription')
            ->html('<p>Merci de vous être inscrit. Cliquez sur le lien suivant pour confirmer votre inscription : <a href="' . $confirmationUrl . '">Confirmer mon inscription</a></p>');

        $this->mailer->send($email);
    }

    public function sendResetPasswordEmail(string $toEmail, string $resetToken): void
    {
        $resetPasswordUrl = $this->router->generate('app_reset_password', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new Email())
            ->from('noreply@example.com')
            ->to($toEmail)
            ->subject('Réinitialisation de votre mot de passe')
            ->html('<p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci-dessous :</p><a href="' . $resetPasswordUrl . '">Réinitialiser mon mot de passe</a>');

        $this->mailer->send($email);
    }
}