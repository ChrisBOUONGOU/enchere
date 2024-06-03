<?php

namespace App\Command;

use App\Entity\Cart;
use App\Entity\CartItem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\Repository\UserRepository;
use App\Repository\CartRepository;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'auction:check-expired',
    description: 'Add a short description for your command',
)]
class AuctionCheckExpiredCommand extends Command
{
    protected static $defaultName = 'auction:check-expired';
    private $auctionRepository;
    private $userRepository;
    private $cartRepository;
    private $entityManager;

     public function __construct(
        PurchaseRepository $auctionRepository,
        UserRepository $userRepository,
        CartRepository $cartRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->auctionRepository = $auctionRepository;
        $this->userRepository = $userRepository;
        $this->cartRepository = $cartRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
        ->setDescription('Check for expired auctions and add won items to cart')
        ->addArgument('date', InputArgument::OPTIONAL, 'Date to use for checking expired auctions (format: Y-m-d H:i:s)', date('Y-m-d H:i:s'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $date = new \DateTime($input->getArgument('date'));

        // Récupérer les enchères expirées
        $expiredAuctions = $this->auctionRepository->findExpiredAuctions($date);

        foreach ($expiredAuctions as $auction) {
            // Déterminer le gagnant de l'enchère
            $winner = $auction->getUsers();

            if ($winner) {
                // Ajouter le produit au panier du gagnant
                $cart = $this->cartRepository->findOneBy(['user' => $winner]);

                if (!$cart) {
                    $cart = new Cart();
                    $cart->setUser($winner);
                    $this->entityManager->persist($cart);
                }

                $cartItem = new CartItem();
                $cartItem->setProduct($auction->getProducts());
                $cartItem->setQuantity(1); // Assumons une quantité de 1 pour cet exemple
                $cart->addItem($cartItem);

                $this->entityManager->persist($cartItem);

                // Marquer l'enchère comme terminée
                $auction->setStatus('completed');
                $this->entityManager->persist($auction);
            }
        }

        $this->entityManager->flush();

        $output->writeln('Expired auctions checked and items added to carts');

        return Command::SUCCESS;
    }
}
