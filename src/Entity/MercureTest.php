<?php

namespace App\Entity;

use App\Repository\MercureTestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: MercureTestRepository::class)]
#[ORM\HasLifecycleCallbacks]
class MercureTest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'mercureTests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $testedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTestedAt(): ?\DateTimeImmutable
    {
        return $this->testedAt;
    }

    #[ORM\PrePersist]
    public function setTestedAt(): void
    {
        $testedAt = new \DateTimeImmutable();
        $this->testedAt = $testedAt;
    }
}
