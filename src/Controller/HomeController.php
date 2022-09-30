<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, Authorization $authorization): Response
    {
        if ($this->getUser()) {
            $authorization->setCookie($request, ['/topic/'.$this->getUser()->getId()]);
        }

        return $this->render('home.html.twig');
    }
}
