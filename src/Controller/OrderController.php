<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\Purchase;
use App\Entity\RecapDetails;
use App\Form\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\OrderType;
use App\Service\CardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class OrderController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/order/create', name: 'order_index')]
    public function index(CardService $cardService): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('security_login');
        }

       
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'recapCard' => $cardService->getTotal()
        ]);
    }

    #[Route('/order/ajouter-adresse', name: 'order_address')]
    public function addAddress(Request $request, EntityManagerInterface $entityManager, Security $security)
{
    $user = $security->getUser(); 
    $address = new Address();
    $address->setUser($user);
    $form = $this->createForm(AddressType::class, $address, [
        'user' => $user, // Passer l'utilisateur au formulaire
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        $entityManager->persist($address);
        $entityManager->flush();

        return $this->redirectToRoute('order_index');
    }

    return $this->render('address/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/order/verify', name: 'order_prepare', methods: ['POST'])]
    public function prepareOrder(CardService $cardService, Request $request): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('security_login');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $datetime = new \DateTime('now');
            $transporter = $form->get('transporter')->getData();
            $delivery = $form->get('addresses')->getData();
            $deliveryForOrder = $delivery->getNom().' '.$delivery->getPrenom();
            $deliveryForOrder .= '</br>'.$delivery->getPhone();
            if($delivery->getCompany()){
                $deliveryForOrder .= ' - '.$delivery->getCompany();
            }
            
            $deliveryForOrder .= '</br>'.$delivery->getAddress();
            $deliveryForOrder .= '</br>'.$delivery->getPostalCode().' - '.$delivery->getCity();
            $deliveryForOrder .= '</br>'.$delivery->getCountry();

       

            $order = new Order();
            $reference = $datetime->format('dmY').'-'.uniqid();
            $order->setUser($this->getUser());
            $order->setReference($reference);
            $order->setDateCreation($datetime);
            $order->setDelivery($deliveryForOrder);
            $order->setTransporterName($transporter->getTitle());
            $order->setTransporterPrice($transporter->getPrice());
            $order->setPaid(0);
            $order->setMethod('stripe');

            $this->em->persist($order);
            $purchase = new Purchase();
            foreach($cardService->getTotal() as $product){
                $recapDetails = new RecapDetails();
                $recapDetails->setOrderProduct($order);
                $recapDetails->setQuantity($product['quantity']);
                $recapDetails->setPrice($product['product']->getStartingPrice());
                $recapDetails->setProduct($product['product']->getNom());
                $recapDetails->setTotalRecap(
                    $product['product']->getStartingPrice() * $product['quantity']
                );
                $recapDetails->setAmount($product['price']->getAmount());
            
                $this->em->persist($recapDetails);
            }
           
            $this->em->flush();
         
        }

        return $this->render('order/recap.html.twig',[
            'method' => $order->getMethod(),
            'recapCard' => $cardService->getTotal(),
            'transporter' => $transporter,
            'delivery' => $deliveryForOrder,
            'reference' => $order->getReference()

        ]);
    }
}
