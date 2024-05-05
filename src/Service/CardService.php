<?php
namespace App\Service;

use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardService
{
    private RequestStack $requestStack;

    private EntityManagerInterface $em;
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em){
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    public function addToCard(int $id): void
    {
        $card = $this->requestStack->getSession()->get('card',[]);
        if(!empty($card[$id])){
            $card[$id]++;
        }else{
            $card[$id] = 1;
        }
        $this->getSession()->set('card', $card);
    }
    
    public function remove(int $id)
    {
        $card = $this->requestStack->getSession()->get('card',[]);
        unset($card[$id]);
        return $this->getSession()->set('card', $card);
    }
    public function removeAll()
    {
        return $this->getSession()->remove('card');
    }
    public function getTotal(): array
    {
        $card = $this->getSession()->get("card");
        $cardData = [];
        if($card){
            foreach($card as $id => $quantity){
                $product = $this->em->getRepository(Products::class)->findOneBy(['id' => $id]);
            
                if(!$product){
                    $this->remove($id);
                    continue;
                }
                $cardData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            
            
            }
        }
        return $cardData;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}