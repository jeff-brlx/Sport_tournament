<?php

namespace App\Entity;

use App\Repository\SportMatchRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: SportMatchRepository::class)]
#[Broadcast]
class SportMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'sportMatches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tournament $tournament = null;





    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $matchDate = null;


    #[ORM\Column(length: 255)]
    private ?string $status = 'pending';

    #[ORM\ManyToOne(inversedBy: 'sportMatchesPlayer1')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $player1 = null;

    #[ORM\ManyToOne(inversedBy: 'sportMatchesPlayer2')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $player2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $scorePlayer1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $scorePlayer2 = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): static
    {
        $this->tournament = $tournament;

        return $this;
    }


    public function getPlayer2(): ?User
    {
        return $this->player2;
    }

    public function setPlayer2(?User $player2): static
    {
        $this->player2 = $player2;

        return $this;
    }

    public function getMatchDate(): ?\DateTimeInterface
    {
        return $this->matchDate;
    }

    public function setMatchDate(\DateTimeInterface $matchDate): static
    {
        $this->matchDate = $matchDate;

        return $this;
    }





    public function getScorePlayer2(): ?int
    {
        return $this->scorePlayer2;
    }

    public function setScorePlayer2(?int $scorePlayer2): static
    {

        $this->scorePlayer2 = $scorePlayer2;
        $this->updateStatus();
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

    public function getPlayer1(): ?User
    {
        return $this->player1;
    }

    public function setPlayer1(?User $player1): static
    {
        $this->player1 = $player1;

        return $this;
    }

    public function getScorePlayer1(): ?int
    {
        return $this->scorePlayer1;
    }

    public function setScorePlayer1(?int $scorePlayer1): static
    {
        $this->scorePlayer1 = $scorePlayer1;
        $this->updateStatus();
        return $this;
    }
    public function updateStatus(): void
    {
        if ($this->scorePlayer1 !== null && $this->scorePlayer2 !== null) {
            $this->status = 'completed';
        } else if ($this->scorePlayer1 === null && $this->scorePlayer2 === null) {
            $this->status = 'pending';
        } else {
            $this->status = 'pending';
        }
    }
}
