<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    #[Route('/equipe', name: 'team')]
    public function index(PostRepository $postRepository): Response
    {

        $posts = $postRepository->findByTypeId(3);

        return $this->render('team/index.html.twig', [
            "posts" => $posts
        ]);
    }
}
