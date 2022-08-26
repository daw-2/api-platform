<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Dependency;
use Symfony\Component\HttpKernel\KernelInterface;

class DependencyDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(private KernelInterface $kernel)
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Dependency;
    }

    protected function json()
    {
        return json_decode(file_get_contents($this->kernel->getProjectDir().'/composer.json'), true) ?? [];
    }

    public function persist($data, array $context = [])
    {
        $json = $this->json();
        $json['require'][$data->name] = $data->version;
        file_put_contents($this->kernel->getProjectDir().'/composer.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
    }    

    public function remove($data, array $context = [])
    {
        $json = $this->json();
        unset($json['require'][$data->name]);
        file_put_contents($this->kernel->getProjectDir().'/composer.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL);
    } 
}
