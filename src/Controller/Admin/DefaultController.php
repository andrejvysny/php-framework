<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class DefaultController extends AbstractController
{
    #[Route('/admin', name: 'admin_default')]
    public function index(): Response
    {
        return $this->render('admin/default/index.html.twig', [
        ]);
    }
}
