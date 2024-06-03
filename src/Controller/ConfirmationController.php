<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class ConfirmationController extends AbstractController
{
    #[Route('/confirmation', name: 'app_confirmation')]
    public function confirm(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $token = $request->query->get('token');
        if (!$token) {
            throw $this->createNotFoundException('Token non valide');
        }

        $user = $userRepository->findOneBy(['confirmationToken' => $token]);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé pour ce token');
        }

        $user->setConfirmationToken(null);
        $user->setIsVerified(true);
        $entityManager->flush();

        // Display a success message or redirect to another page
        return $this->render('confirmation/success.html.twig');
    }
}
