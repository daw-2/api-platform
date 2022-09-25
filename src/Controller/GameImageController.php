<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class GameImageController extends AbstractController
{
    public function __invoke(Game $game, Request $request)
    {
        $game->setFile($request->files->get('image'));

        return $game;
    }
}
