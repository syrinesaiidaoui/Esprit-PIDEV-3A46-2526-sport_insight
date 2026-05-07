<?php

namespace App\Entity;

use App\Repository\SponsorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: SponsorRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQUE_sponsor_nom', columns: ['nom'])]
#[UniqueEntity(
    fields: ['nom'],
    message: 'Ce sponsor existe déjà dans la base de données'
)]
#[Vich\Uploadable]
class Sponsor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du sponsor est obligatoire')]
    #[Assert\Length(
        min: 3,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
        max: 255,
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email doit être valide')]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le téléphone est obligatoire')]
    #[Assert\Length(
        min: 8,
        max: 8,
        exactMessage: 'Le téléphone doit contenir exactement {{ limit }} caractères'
    )]
    private ?string $telephone = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le budget est obligatoire')]
    #[Assert\Positive(message: 'Le budget doit être un nombre positif')]
    #[Assert\Type(type: 'float', message: 'Le budget doit être un nombre')]
    private ?float $budget = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoName = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[Vich\UploadableField(mapping: 'sponsor_logo', fileNameProperty: 'logoName')]
    #[Assert\File(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        mimeTypesMessage: 'Le fichier doit être une image (JPEG, PNG, GIF, WebP)'
    )]
    private ?File $logoFile = null;

    /**
     * @var Collection<int, ContratSponsor>
     */
    #[ORM\OneToMany(targetEntity: ContratSponsor::class, mappedBy: 'sponsor')]
    private Collection $contratSponsors;

    public function __construct()
    {
        $this->contratSponsors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(float $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getLogoName(): ?string
    {
        return $this->logoName;
    }

    public function setLogoName(?string $logoName): static
    {
        $this->logoName = $logoName;

        return $this;
    }

    public function setLogoFile(?File $file = null): void
    {
        $this->logoFile = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logoName;
    }

    public function setLogo(?string $logo): static
    {
        $this->logoName = $logo;

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
            $contratSponsor->setSponsor($this);
        }

        return $this;
    }

    public function removeContratSponsor(ContratSponsor $contratSponsor): static
    {
        if ($this->contratSponsors->removeElement($contratSponsor)) {
            // set the owning side to null (unless already changed)
            if ($contratSponsor->getSponsor() === $this) {
                $contratSponsor->setSponsor(null);
            }
        }

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }
}
