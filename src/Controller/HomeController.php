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
        $posts = $postRepository->findAll();
        $courses = $courseRepository->findAll();
        return $this->render('home/index.html.twig', [
            'courses' => $courses,
            "posts" => $posts,
        ]);
    }
}
