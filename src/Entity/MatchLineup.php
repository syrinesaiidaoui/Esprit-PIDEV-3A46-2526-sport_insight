<?php

namespace App\Entity;

use App\Repository\MatchLineupRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MatchLineupRepository::class)]
class MatchLineup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'matchLineups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Matchs $matchs = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Joueur $joueur = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le type de composition est obligatoire')]
    #[Assert\Choice(choices: ['domicile', 'exterieur'], message: 'Le type doit être "domicile" ou "exterieur"')]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(0, message: 'Le nombre de buts doit être positif')]
    private ?int $buts = 0;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(0, message: 'Le nombre de cartons jaunes doit être positif')]
    private ?int $cartonsJaunes = 0;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(0, message: 'Le nombre de cartons rouges doit être positif')]
    private ?int $cartonsRouges = 0;

    #[ORM\Column(nullable: true)]
    private ?float $positionX = null;

    #[ORM\Column(nullable: true)]
    private ?float $positionY = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatchs(): ?Matchs
    {
        return $this->matchs;
    }

    public function setMatchs(?Matchs $matchs): static
    {
        $this->matchs = $matchs;

        return $this;
    }

    public function getJoueur(): ?Joueur
    {
        return $this->joueur;
    }

    public function setJoueur(?Joueur $joueur): static
    {
        $this->joueur = $joueur;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getButs(): ?int
    {
        return $this->buts;
    }

    public function setButs(?int $buts): static
    {
        $this->buts = $buts;

        return $this;
    }

    public function getCartonsJaunes(): ?int
    {
        return $this->cartonsJaunes;
    }

    public function setCartonsJaunes(?int $cartonsJaunes): static
    {
        $this->cartonsJaunes = $cartonsJaunes;

        return $this;
    }

    public function getCartonsRouges(): ?int
    {
        return $this->cartonsRouges;
    }

    public function setCartonsRouges(?int $cartonsRouges): static
    {
        $this->cartonsRouges = $cartonsRouges;

        return $this;
    }

    public function getPositionX(): ?float
    {
        return $this->positionX;
    }

    public function setPositionX(?float $positionX): static
    {
        $this->positionX = $positionX;

        return $this;
    }

    public function getPositionY(): ?float
    {
        return $this->positionY;
    }

    public function setPositionY(?float $positionY): static
    {
        $this->positionY = $positionY;

        return $this;
    }
}
