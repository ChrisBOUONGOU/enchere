<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Stripe\Stripe;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use App\Entity\Products;
use App\Service\CardService;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{

    private EntityManagerInterface $em;
    private UrlGeneratorInterface $generator;
    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $generator)
    {
        $this->em = $em;
        $this->generator = $generator;
    }


    #[Route('/order/create-session-stripe/{reference}', name: 'payment_stripe')]
    public function stripeCheckout($reference): RedirectResponse
    {
        $productStripe = [];

        $order = $this->em->getRepository(Order::class)->findOneBy(['reference'=> $reference]);
        
        if(!$order){
            return $this->redirectToRoute('card_index');
        }

        foreach($order->getRecapDetails()->getValues() as $product){
            $productData = $this->em->getRepository(Products::class)->findOneBy(['nom' => $product->getProduct()]);
            $productStripe[] = [
                'price_data' => [
                    'currency' => 'cad',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct()
                    ]
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $productStripe[] = [
            'price_data' => [
                'currency' => 'cad',
                'unit_amount' => $order->getTransporterPrice(),
                'product_data' => [
                    'name' => $order->getTransporterName()
                ]
            ],
            'quantity' => 1,
        ];

       
        Stripe::setApiKey('sk_test_51PBaHuHKOWjzLA6viSTF0zET3maGowGec6HptfVtdavGGAJAdnEEFeVnOAPlsiXKqpvOnuyHHvxsgCsC84TvQUVh00OcmmBmfQ');
    
        
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getUserIdentifier(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $productStripe
            ]],
            'mode' => 'payment',
            'success_url' => $this->generator->generate('payment_success',[
                'reference' => $order->getReference()
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generator->generate('payment_error',[
                'reference' => $order->getReference()
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'automatic_tax' => [
                'enabled' => true,
            ],
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $this->em->flush();
        return new RedirectResponse($checkout_session->url);

    }


    #[Route('/order/success/{reference}', name: 'payment_success')]
    public function stripeSuccess($reference, CardService $cardService)
    {
        return $this->render('order/success.html.twig');
    }

    #[Route('/order/error/{reference}', name: 'payment_error')]
    public function stripeError($reference, CardService $cardService): Response
    {
        return $this->render('order/error.html.twig');
    }

    
}
