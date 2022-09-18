<?php

namespace App\Serializer;

use App\Entity\Game;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class UserDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function __construct(private Security $security)
    {
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Game::class && ! ($data['already_called_user'] ?? false);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $data['already_called_user'] = true;
        $object = $this->denormalizer->denormalize($data, $type, $format, $context);
        $object->setUser($this->security->getUser());

        return $object;
    }
}
