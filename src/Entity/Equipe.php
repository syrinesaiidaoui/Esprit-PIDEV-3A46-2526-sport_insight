<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
<<<<<<< HEAD
=======
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

<<<<<<< HEAD
    #[ORM\Column(length: 100)]
    private ?string $id_equipe = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $coach = null;

=======
    #[Assert\NotBlank(message: 'Le nom de l\'équipe est obligatoire.')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Le nom de l\'équipe ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[Assert\Length(
        max: 100,
        maxMessage: 'Le nom du coach ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $coach = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
    /**
     * @var Collection<int, Matchs>
     */
    #[ORM\OneToMany(targetEntity: Matchs::class, mappedBy: 'equipeDomicile')]
    private Collection $matchs;

    /**
     * @var Collection<int, ContratSponsor>
     */
    #[ORM\OneToMany(targetEntity: ContratSponsor::class, mappedBy: 'equipe')]
    private Collection $contratSponsors;

<<<<<<< HEAD
=======
    /**
     * @var Collection<int, Joueur>
     */
    #[ORM\OneToMany(targetEntity: Joueur::class, mappedBy: 'equipe', cascade: ['remove'])]
    private Collection $joueurs;

>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
    public function __construct()
    {
        $this->matchs = new ArrayCollection();
        $this->contratSponsors = new ArrayCollection();
<<<<<<< HEAD
=======
        $this->joueurs = new ArrayCollection();
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
    }

    public function getId(): ?int
    {
        return $this->id;
    }

<<<<<<< HEAD
    public function getIdEquipe(): ?string
    {
        return $this->id_equipe;
    }

    public function setIdEquipe(string $id_equipe): static
    {
        $this->id_equipe = $id_equipe;

        return $this;
    }

=======
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCoach(): ?string
    {
        return $this->coach;
    }

    public function setCoach(?string $coach): static
    {
        $this->coach = $coach;

        return $this;
    }

<<<<<<< HEAD
=======
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
    /**
     * @return Collection<int, Matchs>
     */
    public function getMatchs(): Collection
    {
        return $this->matchs;
    }

    public function addMatch(Matchs $match): static
    {
        if (!$this->matchs->contains($match)) {
            $this->matchs->add($match);
            $match->setEquipeDomicile($this);
        }

        return $this;
    }

    public function removeMatch(Matchs $match): static
    {
        if ($this->matchs->removeElement($match)) {
            // set the owning side to null (unless already changed)
            if ($match->getEquipeDomicile() === $this) {
                $match->setEquipeDomicile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ContratSponsor>
     */
    public function getContratSponsors(): Collection
    {
        return $this->contratSponsors;
    }

    public function addContratSponsor(ContratSponsor $contratSponsor): static
    {
        if (!$this->contratSponsors->contains($contratSponsor)) {
            $this->contratSponsors->add($contratSponsor);
            $contratSponsor->setEquipe($this);
        }

        return $this;
    }

    public function removeContratSponsor(ContratSponsor $contratSponsor): static
    {
        if ($this->contratSponsors->removeElement($contratSponsor)) {
            // set the owning side to null (unless already changed)
            if ($contratSponsor->getEquipe() === $this) {
                $contratSponsor->setEquipe(null);
            }
        }

        return $this;
    }
<<<<<<< HEAD
=======

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Joueur>
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Joueur $joueur): static
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs->add($joueur);
            $joueur->setEquipe($this);
        }

        return $this;
    }

    public function removeJoueur(Joueur $joueur): static
    {
        if ($this->joueurs->removeElement($joueur)) {
            // set the owning side to null (unless already changed)
            if ($joueur->getEquipe() === $this) {
                $joueur->setEquipe(null);
            }
        }

        return $this;
    }
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
}
