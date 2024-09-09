<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: TournamentRepository::class)]
#[Broadcast]
class Tournament
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255),Groups(["full"])]
    private ?string $tournamentName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE),Groups(["full"])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE),Groups(["full"])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 255, nullable: true),Groups(["full"])]
    private ?string $location = null;

    #[ORM\Column(type: Types::TEXT),Groups(["full"])]
    private ?string $description = null;

    #[ORM\Column,Groups(["full"])]
    private ?int $maxParticipants = null;

    #[ORM\Column(length: 255),Groups(["full"])]
    private ?string $status = null;

    #[ORM\Column(length: 255),Groups(["full"])]
    private ?string $sport = null;


    #[ORM\ManyToOne(inversedBy: 'tournaments')]
    private ?User $winner = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizer = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTournamentName(): ?string
    {
        return $this->tournamentName;
    }

    public function setTournamentName(string $tournamentName): static
    {
        $this->tournamentName = $tournamentName;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(int $maxParticipants): static
    {
        $this->maxParticipants = $maxParticipants;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSport(): ?string
    {
        return $this->sport;
    }

    public function setSport(string $sport): static
    {
        $this->sport = $sport;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }


    public function getWinner(): ?User
    {
        return $this->winner;
    }

    public function setWinner(?User $winner): static
    {
        $this->winner = $winner;

        return $this;
    }
}
