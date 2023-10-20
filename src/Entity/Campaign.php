<?php

namespace App\Entity;

use App\Repository\CampaignRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampaignRepository::class)]
class Campaign
{
    #[ORM\Id]
    #[ORM\Column(length: 50)]
    private ?string $id = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    private ?int $goal = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    private ?int $totalParticipants = null;

    private ?int $totalAmount = null;

    private ?int $percentage = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(): self
    {
        $id = md5(random_bytes(50));
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getGoal(): ?int
    {
        return $this->goal;
    }

    public function setGoal(?int $goal): static
    {
        $this->goal = $goal;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get the value of totalParticipants
     */
    public function getTotalParticipants(): ?int
    {
        return $this->totalParticipants;
    }

    /**
     * Set the value of totalParticipants
     */
    public function setTotalParticipants(?int $totalParticipants): self
    {
        $this->totalParticipants = $totalParticipants;

        return $this;
    }

    /**
     * Get the value of totalAmount
     */
    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    /**
     * Set the value of totalAmount
     */
    public function setTotalAmount(?int $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function setPercentage() {
        $totalAmount = $this->totalAmount;
        $percentage = round(($totalAmount / $this->getGoal()) * 100, 1);
        $this->percentage = $percentage;
    }

    /**
     * Get the value of percentage
     */
    public function getPercentage(): ?int
    {
        return $this->percentage;
    }
}
