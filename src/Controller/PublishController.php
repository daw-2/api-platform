<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    public function __invoke(Game $data): Game
    {
        $data->setIsEnabled(! $data->isEnabled());

        return $data;
    }

    #[Route('/publish')]
    public function publish(HubInterface $hub)
    {
        $update = new Update(
            '/topic/1',
            json_encode(['status' => 'ok'])
        );

        $hub->publish($update);

        return new Response('Published !');
    }
}
