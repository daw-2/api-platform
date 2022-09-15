<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\GameCountController;
use App\Controller\PublishController;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ApiResource(
    paginationItemsPerPage: 2,
    paginationClientItemsPerPage: true,
    paginationMaximumItemsPerPage: 2,
    normalizationContext: ['groups' => ['read:collection']],
    denormalizationContext: ['groups' => ['write:item', 'write:Game']],
    collectionOperations: [
        'get',
        'post' => [
            'validation_groups' => ['create:item']
        ],
        'count' => [
            'method' => 'GET',
            'path' => '/games/count',
            'controller' => GameCountController::class,
            'pagination_enabled' => false,
            'filters' => [],
            'openapi_context' => [
                'summary' => 'Get total games',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'published',
                        'schema' => [
                            'type' => 'integer',
                            'maximum' => 1,
                            'minimum' => 0,
                        ],
                        'description' => 'Filter by published games'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Game count',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'integer',
                                    'example' => 1,
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:item', 'read:Game']]
        ],
        'put' => [
            'denormalization_context' => ['groups' => ['put:item']]
        ],
        'patch',
        'delete',
        'publish' => [
            'method' => 'POST',
            'path' => '/posts/{id}/publish',
            'controller' => PublishController::class,
            'openapi_context' => [
                'summary' => 'Publish a Game resource',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [],
                            'example' => []
                        ]
                    ]
                ]
            ],
            // 'read' => false,
            // 'write' => false,
        ]
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial'])]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:collection', 'read:item'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:collection', 'read:item', 'write:item', 'put:item'])]
    #[Length(min: 5, groups: ['create:item'])]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:collection', 'read:item', 'write:item'])]
    private $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:item', 'write:item'])]
    private $content;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:collection', 'read:item', 'write:item'])]
    private $image;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['read:collection', 'read:item'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['read:collection', 'read:item'])]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'games', cascade: ['persist'])]
    #[Groups(['read:item', 'write:item', 'put:item'])]
    #[Valid()]
    private $category;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:collection', 'read:item'])]
    private $isEnabled = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function isIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }
}
