<?php

namespace App\Controller\Public;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'public_default')]
    public function index(): Response
    {
        return $this->render('public/default/index.html.twig',[]);
    }
}
