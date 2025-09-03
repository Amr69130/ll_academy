<?php

namespace App\EventListener;

use App\Entity\Course;
use App\Service\StripeService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;

// Cette event Listener lance la methode addProduct lorsque qu'on crée un Course 
#[AsEntityListener(event: Events::prePersist, method: 'addProduct', entity: Course::class)]
class PrepersistProduct
{

    public function __construct(private EntityManagerInterface $entityManager, private StripeService $stripeService, private LoggerInterface $logger)
    {

    }

    public function addProduct(Course $course)
    {
        // Vérification des logs comme un vardump dans le dossier var
        $this->logger->info('Event add product fonctionne');

        // Récupération des données générer coté stripe (Product,Price,PaymentLink)
        // Cette methode remplace toute les intervention manuel stripeAdmin et les automatisé
        $stripeData = $this->stripeService->createProductToLink($course);

        $this->logger->info('Stripe data', [
            "stripeData" => $stripeData
        ]);

        // Complétions de l'entité Course avec les données issu de stripe
        $course->setPaymentLinkUrl($stripeData['paymentLinkUrl']);
        $course->setStripeProductId($stripeData["productId"]);
        $course->setStripePriceId($stripeData["priceId"]);
        $course->setStripePaymentLinkId($stripeData["paymentLinkId"]);
    }
}