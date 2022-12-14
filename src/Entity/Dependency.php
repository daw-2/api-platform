<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'put', 'delete'],
    paginationEnabled: false
)]
class Dependency
{
    protected $id;

    #[Assert\NotBlank, Assert\Length(min: 2)]
    public $name;

    #[Assert\NotBlank]
    public $version;

    public function __construct($name, $version)
    {
        $this->id = md5($name.$version);
        $this->name = $name;
        $this->version = $version;
    }

    public function getId()
    {
        return $this->id;
    }
}
