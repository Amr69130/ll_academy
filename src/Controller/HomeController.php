<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\PostRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CourseRepository $courseRepository, PostRepository $postRepository): Response
    {

        //        ICI ON DOIT TROUVER PAR TYPE NAME ET PAS ID SINON A LA RECHARGE DES FIXTURES ON NE TROUVE PLUS

        $posts = $postRepository->findByTypeName('Actualité');
        $courses = $courseRepository->findAll();
        return $this->render('home/index.html.twig', [
            'courses' => $courses,
            "posts" => $posts,
        ]);
    }

    #[Route('/accueil/faq', name: 'app_faq')]
    public function faq(CourseRepository $courseRepository, PostRepository $postRepository): Response
    {

        //        ICI ON DOIT TROUVER PAR TYPE NAME ET PAS ID SINON A LA RECHARGE DES FIXTURES ON NE TROUVE PLUS

        $posts = $postRepository->findByTypeName('FAQ');

        return $this->render('footer/faq.html.twig', [
            "posts" => $posts,
        ]);
    }
}
