<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Security\Core\Security;
class StripeService
{
    private $stripeSecretKey;
    private $security;
    public function __construct(Security $security,string $stripeSecretKey)
    {
        $this->stripeSecretKey = $stripeSecretKey;
        Stripe::setApiKey($this->stripeSecretKey);
        $this->security = $security;
    }

    public function createCheckoutSession(array $lineItems, string $successUrl, string $cancelUrl): Session
    {
        $user = $this->security->getUser();
        if ($user) {
            $customerEmail = $user->getEmail();
        } else {
            // Gérer le cas où aucun utilisateur n'est connecté
            throw new \Exception('No user is currently logged in');
        }

        return Session::create([
            'customer_email' => $customerEmail,
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'shipping_address_collection' => ['allowed_countries' => ['CA']],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'automatic_tax' => [
                'enabled' => true,
            ],
        ]);
    }
}