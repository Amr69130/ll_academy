<?php

namespace App\Service;

use Stripe\Price;
use Stripe\Product;
use Stripe\StripeClient;


class StripeService
{
    private StripeClient $stripeClient;

    public function __construct()
    {

        $this->stripeClient = new StripeClient('sk_test_51QPkpnGMrCujKBNI1M46ziqZQ06PlpuW14tPLldGS17Tf3AK1aOMTP3ip9Sc8PeqnCE8Q8NOWFyxwmv4oCsHovXA00OH4fuU48');

    }
    public function createProduct(): Product
    {

        $product = $this->stripeClient->products->create(
            [
                'name' => 'Anglais TEST',
                "description" => "je suis la"
            ]
        );

        return $product;
    }

    public function createPrice(Product $product)
    {
        $price = $this->stripeClient->prices->create([
            'currency' => 'eur',
            'unit_amount' => 15000,
            'product' => $product->id,
        ]);

        return $price;
    }

    public function createPaymentLink(Price $price)
    {

        $paymentLink = $this->stripeClient->paymentLinks->create([
            'line_items' => [
                [
                    'price' => $price->id,
                    'quantity' => 1,
                ],
            ],
        ]);
        return $paymentLink;
    }

    public function createProductToLink()
    {

        $product = $this->createProduct();
        $price = $this->createPrice($product);
        $paymentLink = $this->createPaymentLink($price);

        return $paymentLink;
    }
}