<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $isFrom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $isTo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getIsFrom(): ?User
    {
        return $this->isFrom;
    }

    public function setIsFrom(?User $isFrom): static
    {
        $this->isFrom = $isFrom;

        return $this;
    }

    public function getIsTo(): ?User
    {
        return $this->isTo;
    }

    public function setIsTo(?User $isTo): static
    {
        $this->isTo = $isTo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $createdAt = new \DateTimeImmutable();
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDate(): string
    {
        $createdAt = $this->getCreatedAt();
        $now = new \DateTime();
        $yesterday = clone($now);
        $yesterday->modify('-1 day');

        if($createdAt->format('Ymd') === $now->format('Ymd')) {
            return $createdAt->format('H:i');
        }

        if($createdAt->format('Ymd') === $yesterday->format('Ymd')) {
            return 'Hier à ' . $createdAt->format('H:i');
        }

        return $createdAt->format('d/m/Y') . ' à ' . $this->getCreatedAt()->format('H:i');

    }


    /**
     * @param User $user Must be the logged in user in app
     * @return string
     */
    public function getClass(UserInterface $user): string
    {
        return $this->getIsTo() === $user ? 'from' : 'to';
    }
}
