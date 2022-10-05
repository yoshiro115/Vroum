<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VroumGeneralController extends AbstractController
{
    #[Route('/vroum/general', name: 'app_vroum_general')]
    public function index(): Response
    {
        return $this->render('vroum_general/index.html.twig', [
            'controller_name' => 'VroumGeneralController',
        ]);
    }
}
