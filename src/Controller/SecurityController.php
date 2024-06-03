<?php

namespace App\Controller;

use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
class SecurityController extends AbstractController
{
    private $emailService;

    public function __construct(MailerService $emailService)
    {
        $this->emailService = $emailService;
    }
    #[Route('/inscription', name: 'security_reg')]
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $date = new \DateTime();
        // Assigner la date à l'entité
        $user->setDateCreation($date);
        $form = $this->createForm(RegistrationType::class, $user);

        

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $hasher->hashPassword($user, $user->getPassword());
            
            $user->setPassword($hash);
            $token = bin2hex(random_bytes(32));
            $user->setConfirmationToken($token);
            $manager->persist($user);
            $manager->flush();

            $this->emailService->sendConfirmationEmail($user->getEmail(), $token);
            $this->addFlash('success', 'Votre compte a bien ete cree, veuillez verifier votre email pour l\'activer.');

            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/connexion', name: 'security_login')]
    public function login(){
        return $this->render('security/login.html.twig');
    }

    #[Route('/deconnexion', name: 'security_logout')]
    public function logout(){}
}
