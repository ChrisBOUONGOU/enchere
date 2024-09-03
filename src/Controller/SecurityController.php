<?php

namespace App\Controller;

use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Service\MailerService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
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
            $user->setRoles(['ROLE_USER']);
            $token = bin2hex(random_bytes(32));
            $user->setConfirmationToken($token);
            $manager->persist($user);
            $manager->flush();

            
            
            $this->emailService->sendConfirmationEmail($user->getEmail(), $token);
            $this->addFlash('success', 'Votre compte a bien été crée, veillez vérifier votre adresse courriel pour l\'activer. Si vous ne voyez pas le mail de confirmation, regarder dans votre spam.');

            return $this->redirectToRoute('home');
        }
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/confirm-account/{token}', name: 'app_confirm_account')]
    public function confirmAccount(string $token, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // Trouver l'utilisateur avec le token de confirmation
        $user = $userRepository->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            // Si le token n'est pas valide, rediriger avec un message d'erreur
            $this->addFlash('error', 'Token de confirmation invalide ou expiré.');
            return $this->redirectToRoute('security_reg');
        }

        // Valider le compte de l'utilisateur
        $user->setConfirmationToken(null);  // Supprime le token
        $entityManager->flush();

        // Ajouter un message de succès et rediriger vers la page de connexion
        $this->addFlash('success', 'Votre inscription a été confirmée. Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('security_login');
    }

    #[Route('/connexion', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            // Add a flash message for the error
            $this->addFlash('danger', 'Email ou mot de passe invalide.');
        }

        return $this->render('security/login.html.twig', [
            'error' => $error,
        ]);
    }

    #[Route('/deconnexion', name: 'security_logout')]
    public function logout(){}
}
