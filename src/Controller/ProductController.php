<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repo = $entityManager->getRepository(Products::class);
        $produits = $repo->findAll();
        return $this->render('product/index.html.twig',[
            'produits' => $produits,
          
        ]);
    }

    #[Route('/', name: 'home')]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $repo = $entityManager->getRepository(Products::class);
        $produits = $repo->findAll();

        return $this->render('product/home.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact()
    {
        return $this->render('product/contact.html.twig');
    }

    #[Route('/product/?{id}', name: 'product_show')]
    public function show(EntityManagerInterface $entityManager,$id): Response
    {
        $repo = $entityManager->getRepository(Products::class);
        $produit = $repo->find($id);
        return $this->render('product/show.html.twig',[
            'produit' => $produit
        ]);
    }
}
