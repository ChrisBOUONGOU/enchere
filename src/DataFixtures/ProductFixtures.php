<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Products;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for($i = 1; $i <= 10 ;$i++){
            $product = new Products();
            $product->setNom("Titre du produit numero $i")
                    ->setStartingPrice(12.65)
                    ->setDescription("sdfsdfsd")
                    ->setImage("http://placehold.it/350x150")
                    ->setDateCreation(new \DateTime());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
