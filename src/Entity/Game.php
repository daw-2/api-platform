<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Attributes\ApiSecurityGroups;
use App\Controller\GameCountController;
use App\Controller\GameImageController;
use App\Controller\PublishController;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[Uploadable]
#[ApiResource(
    order: ['createdAt' => 'DESC'],
    paginationItemsPerPage: 3,
    paginationClientItemsPerPage: true,
    paginationMaximumItemsPerPage: 3,
    normalizationContext: ['groups' => ['game:read']],
    denormalizationContext: ['groups' => ['game:write']],
    collectionOperations: [
        'get',
        'post' => [
            'validation_groups' => ['game:create'],
            'security' => 'is_granted("ROLE_USER")'
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
            'normalization_context' => ['groups' => ['game:read:item']]
        ],
        'put' => [
            'denormalization_context' => ['groups' => ['game:put']],
            'validation_groups' => ['game:create'],
            'security' => 'is_granted("edit", object)'
        ],
        'patch',
        'delete',
        'publish' => [
            'method' => 'POST',
            'path' => '/games/{id}/publish',
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
        ],
        'image' => [
            'method' => 'POST',
            'path' => '/games/{id}/image',
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
            // 'deserialize' => false,
            'controller' => GameImageController::class,
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object', 
                                'properties' => [
                                    'file' => [
                                        'type' => 'string', 
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial'])]
#[ApiSecurityGroups([
    'edit' => ['game:read:owner'],
    'ROLE_USER' => ['game:read:user']
])]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['game:read', 'game:read:item'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['game:read', 'game:read:item', 'game:write', 'game:put', 'category:read'])]
    #[Assert\NotBlank(groups: ['game:create']), Assert\Length(min: 5, groups: ['game:create'])]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['game:read:user', 'game:read:item', 'game:write'])]
    private $slug;

    #[ORM\Column(type: 'text')]
    #[Groups(['game:read', 'game:read:item', 'game:write', 'game:put'])]
    #[Assert\NotBlank(groups: ['game:create'])]
    private $content;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['game:read', 'game:read:item', 'game:write'])]
    private $image;

    #[UploadableField(mapping: 'games', fileNameProperty: 'image')]
    #[Groups(['game:write'])]
    private $file;

    #[Groups(['game:read', 'game:read:item'])]
    public $contentUrl;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['game:read', 'game:read:item'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['game:read', 'game:read:item'])]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'games', cascade: ['persist'])]
    #[Groups(['game:read:item', 'game:write', 'game:put'])]
    #[Assert\Valid]
    private $category;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['game:read:owner'])]
    private $isEnabled = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'games')]
    #[Groups(['game:read', 'game:read:item'])]
    private $user;

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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;
        $this->setUpdatedAt(new \DateTimeImmutable());

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

    public function isEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
