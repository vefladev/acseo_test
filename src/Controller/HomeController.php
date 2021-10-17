<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // fonction de rediction lors de la connexion selon le role du User
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('user_index');
        } else {
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
    }
}
