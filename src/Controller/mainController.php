<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class mainController extends AbstractController
{
    #[Route('/')]
    public function homePage(): Response
    {
        return $this->render('main/main.html.twig');
    }
}
