<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Service\StripeService;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    public function __construct(private StripeService $stripeService)
    {

    }

    #[Route('/test', name: 'index')]
    public function index(): Response
    {

        $result = $this->stripeService->createProductToLink();

        dump($result);

        return $this->render('base.html.twig');
    }
}
