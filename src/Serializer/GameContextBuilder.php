<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\Game;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class GameContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decorated,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {}

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;

        if ($resourceClass === Game::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_USER')) {
            // $context['groups'][] = 'game:read:user';
        }

        return $context;
    }
}
