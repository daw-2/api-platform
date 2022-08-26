<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GameCountController extends AbstractController
{
    public function __invoke(Request $request, GameRepository $repository): int
    {
        return $repository->count(['isEnabled' => (bool) $request->get('published')]);
    }   
}
