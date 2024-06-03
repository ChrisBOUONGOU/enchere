<?php

namespace App\Repository;

use App\Entity\Products;
use App\Entity\Purchase;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Purchase>
 */
class PurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Purchase::class);
    }

    public function findLastBidByProduct($productId)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.products = :products')
            ->setParameter('products', $productId)
            ->orderBy('b.purchaseDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

        /**
     * Find all auctions that have expired.
     *
     * @param \DateTime $now The current date and time
     * @return Purchase[] Returns an array of expired Auction objects
     */
    public function findExpiredAuctions(\DateTime $now)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.purchaseDate < :now')
            ->andWhere('a.status = :status')
            ->setParameter('now', $now)
            ->setParameter('status', 'active') // Assuming 'active' is the status for ongoing auctions
            ->getQuery()
            ->getResult();
    }


    public function findRecentBidByAuction($auctionId)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.products = :products')
            ->setParameter('products', $auctionId)
            ->orderBy('b.purchaseDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByProductOrderByDateDesc(Products $product)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.products = :products')
            ->setParameter('products', $product)
            ->orderBy('p.purchaseDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findHighestBidByUserAndAuction(User $user, Products $auction): ?Purchase
    {
        return $this->createQueryBuilder('b')
            ->where('b.users = :users')
            ->andWhere('b.products = :products')
            ->setParameter('users', $user)
            ->setParameter('products', $auction)
            ->orderBy('b.amount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findHighestBidByAuction(Products $auction): ?Purchase
    {
        return $this->createQueryBuilder('b')
            ->where('b.products = :products')
            ->setParameter('products', $auction)
            ->orderBy('b.amount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    

    //    /**
    //     * @return Purchase[] Returns an array of Purchase objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Purchase
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
