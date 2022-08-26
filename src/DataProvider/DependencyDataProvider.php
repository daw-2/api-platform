<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Dependency;
use Symfony\Component\HttpKernel\KernelInterface;

class DependencyDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function __construct(private KernelInterface $kernel)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Dependency::class;
    }

    protected function dependencies()
    {
        return json_decode(file_get_contents($this->kernel->getProjectDir().'/composer.json'), true)['require'] ?? [];
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $items = [];

        foreach ($this->dependencies() as $name => $version) {
            $items[] = new Dependency($name, $version);
        }

        return $items;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        foreach ($this->dependencies() as $name => $version) {
            if (md5($name.$version) === $id) {
                return new Dependency($name, $version);
            }
        }
    }
}
