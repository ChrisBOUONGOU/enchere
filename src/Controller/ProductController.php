<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Products;
use App\Entity\Purchase as PurchaseEntity;
use App\Form\ContactType;
use App\Form\ProductsType;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Mime\Email;

class ProductController extends AbstractController
{
    private $purchaseRepository;

    public function __construct(PurchaseRepository $purchaseRepository)
    {
      
        $this->purchaseRepository = $purchaseRepository;
    }
    #[Route('/liste-produits', name: 'app_product')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repo = $entityManager->getRepository(Products::class);
        $produits = $repo->findAll();
        return $this->render('product/index.html.twig',[
            'produits' => $produits,
          
        ]);
    }

    #[Route('/produit/ajout_nouveau_produit', name: 'product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $product = new Products();
        $isPlublished = false;
        $product->setPublished($isPlublished);
        $form = $this->createForm(ProductsType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Votre produit a ete ajoute, une verification sous 48 heures sera fait pour voir si votre produit est conforme.');

            return $this->redirectToRoute('product_new'); // Redirect to a product list page or other page
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'home')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $produit = new Products();
        $highestBid = $this->getHighestBid($produit);
        $repo = $entityManager->getRepository(Products::class);
        $produits = $repo->findAll();

        return $this->render('product/home.html.twig', [
            'produits' => $produits,
            'highest' => $highestBid
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
       

        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $formData = $form->getData();

        $address = $formData['email'];
        $content = $formData['message'];

        $toEmail = 'chrisjeffersonboukongou@gmail.com';

        $email = (new Email())
            ->from($address)
            ->to($toEmail)
            ->replyTo($address)
            ->subject('Nouveau message de ' . $formData['nom'])
            ->text($content);
      

        $mailer->send($email);

        $this->addFlash('success', 'Votre message a été envoyé avec succès.');

        return $this->redirectToRoute('contact');
    }
        return $this->render('product/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/?{id}', name: 'product_show')]
    public function show(EntityManagerInterface $entityManager,$id): Response
    {
        $repo = $entityManager->getRepository(Products::class);
        $produit = $repo->find($id);

        $currentDate = new \DateTime();
        $highestBid = null;
        $isWinner = false;

        if ($currentDate > $produit->getDateCreation()) {
            // Récupérer l'enchère la plus élevée si l'enchère est terminée
            $highestBid = $entityManager->getRepository(PurchaseEntity::class)->findHighestBidByAuction( $produit);
            // Vérifier si l'utilisateur actuel est le gagnant
            $user = $this->getUser();
            $isWinner = $highestBid && $highestBid->getUsers() === $user;
        }
    
    
        return $this->render('product/show.html.twig',[
            'produit' => $produit,  
            'hight' => $highestBid,
            'isWinner' => $isWinner,   
            'isAuctionEnded' => $currentDate > $produit->getDateCreation(),
        ]);
    }

    #[Route('/mesencheres', name: 'enchere_show')]
    public function enchereShow(): Response
    {
         $user = $this->getUser(); // Récupère l'utilisateur connecté

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $purchases = $user->getPurchases();
        $encheres = [];

        foreach ($purchases as $purchase) {
            $encheres [] = $purchase->getProducts();
        }
        return $this->render('encheres/index.html.twig',[
            'encheres' => $encheres,
      
        ]);
    }

    private function getHighestBid(Products $auction): ?PurchaseEntity
    {
        return $this->purchaseRepository->findRecentBidByAuction($auction->getId());
    }
  
}
