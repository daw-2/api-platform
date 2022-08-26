<?php

namespace App\Controller;

use App\Entity\Game;

class PublishController
{
    public function __invoke(Game $data): Game
    {
        $data->setIsEnabled(true);

        return $data;
    }   
}
