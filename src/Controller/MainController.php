<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
final class MainController extends AbstractController
{
    #[Route( name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[ROUTE('/init', name: 'app_init')]
    public function init(): Response
    {
        return $this->render('mainChoose/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
