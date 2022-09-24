<?php

namespace App\Serializer;

use App\Attributes\ApiSecurityGroups;
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
        if (! is_object($data)) {
            return false;
        }

        $class = new \ReflectionObject($data);

        return ! empty($class->getAttributes(ApiSecurityGroups::class))
            && ! ($context['already_called_game'] ?? false);
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $context['already_called_game'] = true;

        $class = new \ReflectionObject($object);
        $apiSecurityGroups = $class->getAttributes(ApiSecurityGroups::class)[0]->newInstance();

        foreach ($apiSecurityGroups->groups as $role => $groups) {
            if ($this->authorizationChecker->isGranted($role, $object)) {
                $context['groups'] = array_merge($context['groups'] ?? [], $groups);
            }
        }

        return $this->normalizer->normalize($object, $format, $context);
    }
}
