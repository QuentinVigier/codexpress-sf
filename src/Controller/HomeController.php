<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'Martin',
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        return $this->render('home/profile.html.twig', [
        ]);
    }

    

    #[Route('/note', name: 'app_note')]
    public function show(): Response
    {
        return $this->render('home/note.html.twig', [
        ]);
    }

    

    #[Route('/category', name: 'app_category')]
    public function category(): Response
    {
        return $this->render('home/category.html.twig', [
        ]);
    }
}
