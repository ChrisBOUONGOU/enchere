<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendConfirmationEmail(string $to, string $token)
    {
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($to)
            ->subject('Confirmation d\'inscription')
            ->html('<p>Merci de vous être inscrit. Cliquez sur le lien suivant pour confirmer votre inscription : <a href="http://hianpe.com/connexion">Confirmer mon inscription</a></p>');

        $this->mailer->send($email);
    }
}