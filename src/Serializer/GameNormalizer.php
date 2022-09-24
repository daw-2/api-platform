<?php

namespace App\Serializer;

use App\Entity\Game;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class GameNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Game && ! ($context['already_called_game'] ?? false);
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $context['already_called_game'] = true;

        if (isset($context['groups']) && $this->authorizationChecker->isGranted('edit', $object)) {
            $context['groups'][] = 'read:collection:user';
        }

        return $this->normalizer->normalize($object, $format, $context);
    }
}
