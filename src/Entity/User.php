<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use App\Controller\LoginController;
use App\Controller\RegisterController;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/login',
            controller: LoginController::class,
            openapiContext: [
                'summary' => 'Authentifie un utilisateur',
                'description' => 'Vérifie les identifiants de l\'utilisateur',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'mail' => [
                                        'type' => 'string',
                                        'example' => 'firstname.lastname@example.com',
                                    ],
                                    'password' => [
                                        'type' => 'string',
                                        'example' => 'password',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Authentification réussie',
                    ],
                    '401' => [
                        'description' => 'Identifiants invalides',
                    ],
                ],
            ],
        ),
        new Post(
            uriTemplate: '/register',
            controller: RegisterController::class,
        ),
        new Get(),
        new Patch()
    ]
)]
#[ApiFilter(OrderFilter::class, properties: ['lastname'])]
class User implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?int $seance_left = null;

    #[ORM\Column(length: 255)]
    private ?string $tel_number = null;

    /**
     * @var Collection<int, club>
     */
    #[ORM\ManyToMany(targetEntity: Club::class, inversedBy: 'users')]
    private Collection $club;

    /**
     * @var Collection<int, Lesson>
     */
    #[ORM\ManyToMany(targetEntity: Lesson::class, mappedBy: 'users')]
    private Collection $lessons;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, Inventory>
     */
    #[ORM\ManyToMany(targetEntity: Inventory::class, mappedBy: 'users')]
    private Collection $inventories;

    /**
     * @var Collection<int, Badge>
     */
    #[ORM\ManyToMany(targetEntity: Badge::class, mappedBy: 'user')]
    private Collection $badges;

    public function __construct()
    {
        $this->club = new ArrayCollection();
        $this->lessons = new ArrayCollection();
        $this->inventories = new ArrayCollection();
        $this->badges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getSeanceLeft(): ?int
    {
        return $this->seance_left;
    }

    public function setSeanceLeft(int $seance_left): static
    {
        $this->seance_left = $seance_left;

        return $this;
    }

    public function getTelNumber(): ?string
    {
        return $this->tel_number;
    }

    public function setTelNumber(string $tel_number): static
    {
        $this->tel_number = $tel_number;

        return $this;
    }

    /**
     * @return Collection<int, club>
     */
    public function getClub(): Collection
    {
        return $this->club;
    }

    public function addClub(club $club): static
    {
        if (!$this->club->contains($club)) {
            $this->club->add($club);
        }

        return $this;
    }

    public function removeClub(club $club): static
    {
        $this->club->removeElement($club);

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): static
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->addUser($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): static
    {
        if ($this->lessons->removeElement($lesson)) {
            $lesson->removeUser($this);
        }

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        $roles[] = 'ROLE_USER';

        return $this;
    }

    /**
     * @return Collection<int, Inventory>
     */
    public function getInventories(): Collection
    {
        return $this->inventories;
    }

    public function addInventory(Inventory $inventory): static
    {
        if (!$this->inventories->contains($inventory)) {
            $this->inventories->add($inventory);
            $inventory->addUser($this);
        }

        return $this;
    }

    public function removeInventory(Inventory $inventory): static
    {
        if ($this->inventories->removeElement($inventory)) {
            $inventory->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Badge>
     */
    public function getBadges(): Collection
    {
        return $this->badges;
    }

    public function addBadge(Badge $badge): static
    {
        if (!$this->badges->contains($badge)) {
            $this->badges->add($badge);
            $badge->addUser($this);
        }

        return $this;
    }

    public function removeBadge(Badge $badge): static
    {
        if ($this->badges->removeElement($badge)) {
            $badge->removeUser($this);
        }

        return $this;
    }
}
