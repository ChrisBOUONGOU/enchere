<?php

namespace App\Controller;

use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;
class SecurityController extends AbstractController
{
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
            
            $manager->persist($user);
            $manager->flush();

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
