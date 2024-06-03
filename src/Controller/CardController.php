<?php

namespace App\Controller;

use App\Entity\Products;
use App\Service\CardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CardController extends AbstractController
{

    #[Route('/mon-panier', name: 'card_index')]
    public function index(CardService $cardService): Response
    {
       
        return $this->render('card/index.html.twig', [
            'card' => $cardService->getTotal()
        ]);
    }


    #[Route('/mon-panier/ajouter/{id<\d+>}', name: 'card_add')]
    public function addToRoute(CardService $cardService, int $id): Response
    {
        
        $cardService->addToCard($id);
   
        return $this->redirectToRoute('card_index');
    }

 
 

    #[Route('/mon-panier/remove/{id<\d+>}', name: 'card_remove')]
    public function remove(CardService $cardService, int $id): Response
    {
        $cardService->remove($id);
   
        return $this->redirectToRoute('card_index');
    }

    #[Route('/mon-panier/removeAll', name: 'card_removeall')]
    public function removeAll(CardService $cardService): Response
    {
        $cardService->removeAll();
   
        return $this->redirectToRoute('card_index');
    }
}
