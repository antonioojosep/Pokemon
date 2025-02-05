<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainChooseController extends AbstractController
{
    #[Route('/main-choose', name: 'app_main_choose')]
    public function index(): Response
    {
        return $this->render('mainChoose/index.html.twig');
    }
}