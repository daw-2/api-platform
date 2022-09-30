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

    #[Route('/publish/{message}/{to}')]
    public function publish(HubInterface $hub, $message, $to)
    {
        $update = new Update(
            ['/topic/'.$to],
            json_encode(['message' => $message]),
            true
        );

        $hub->publish($update);

        return new Response('Published !');
    }
}
