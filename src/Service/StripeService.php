<?php

namespace App\Service;

use App\Entity\Course;
use Stripe\PaymentLink;
use Stripe\Price;
use Stripe\Product;
use Stripe\StripeClient;


class StripeService
{
    private StripeClient $stripeClient;

    public function __construct()
    {
        // Clé secrete API stripe
        $this->stripeClient = new StripeClient('sk_test_51S3G1i5CikMpfdPint6j2e2qOhFaoqeYiQex9hbs6FfBSTSOclNzXlRrsz4W7y2V2JRaXkIexTvZmM18hNPCYSbz00ULQ52Dja');

    }
    public function createProduct(Course $course): Product
    {

        // Utilisation de l'API stripe pour crée un produit issu de l'entité Course
        $product = $this->stripeClient->products->create(
            [
                'name' => $course->getName(),
                "description" => $course->getDescription()
            ]
        );

        return $product;
    }

    public function createPrice(Product $product, float $price): Price
    {
        // Utilisation de l'API stripe pour crée un prix avec L'id du produit crée précédement et le prix issu du Course
        $price = $this->stripeClient->prices->create([
            'currency' => 'eur',
            'unit_amount' => $price,
            'product' => $product->id,
        ]);

        return $price;
    }

    public function createPaymentLink(Price $price): PaymentLink
    {
        // Utilisation de l'API stripe pour crée un paymentLink qui contient l'url du paiemment 
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

    public function createProductToLink(Course $course)
    {
        // Création d'un produit stripe avec une instance Course 
        /** @var Product $product */
        $product = $this->createProduct($course);
        /**
         * @var Price $price
         */
        $price = $this->createPrice($product, $course->getPrice());
        /** @var PaymentLink $paymentLink */
        $paymentLink = $this->createPaymentLink($price);

        return [
            "productId" => $product->id,
            "priceId" => $price->id,
            "paymentLinkId" => $paymentLink->id,
            "paymentLinkUrl" => $paymentLink->url
        ];
    }
}