<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
class AuctionController extends AbstractController
{
 
    private $stripeService;
    private $entityManager;
    private $bidRepository;
    public function __construct(StripeService $stripeService, EntityManagerInterface $entityManager, PurchaseRepository $bidRepository)
    {
        $this->stripeService = $stripeService;
        $this->entityManager = $entityManager;
        $this->bidRepository = $bidRepository;
    }

  
    #[Route('/auction/{id}', name: 'auction_show')]
    public function show(Products $auction, $id): Response
    {
        $product = $this->entityManager->getRepository(Products::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        // Récupérer la valeur de départ de la base de données
        $startPrice = $auction->getStartingPrice();
        $lastBid = $this->entityManager->getRepository(Purchase::class)->findLastBidByProduct($id);
        $lastBidPrice = $lastBid ? $lastBid->getAmount() : $startPrice;
        // Convertir la valeur de départ
        $convertedPrice = $startPrice / 100;
        $convertedLastBidPrice = $lastBidPrice / 100;


       
        return $this->render('auction/show.html.twig', [
            'auction' => $auction,
            'convert' => $convertedPrice,
        
        ]);
    }

  
    #[Route('/auction/{id}/bid', name: 'auction_bid', methods: "POST")]
    public function bid(Request $request, Products $auction): Response
    {
        $amount = $request->request->get('amount');
        $user = $this->getUser();

        $bid = new Purchase();
        $bid->setUsers($user);
        $bid->setProducts($auction);
        $bid->setAmount($amount * 100);
        $bid->setPurchaseDate(new \DateTime());

        

        $this->entityManager->persist($bid);
        $this->entityManager->flush();
        $this->addFlash('success', 'Votre enchere a ete bien effectue');
        // Notify other users about the new bid (via websockets)

        return $this->redirectToRoute('auction_show');
    }

   
   
    #[Route('/auction/success', name: 'auction_success')]
    public function success(): Response
    {
      
        return $this->render('auction/success.html.twig');
    }

    #[Route('/auction/cancel', name: 'cancel')]
    public function cancel(): Response
    {
   
        return $this->render('auction/cancel.html.twig');
    }

   
}
