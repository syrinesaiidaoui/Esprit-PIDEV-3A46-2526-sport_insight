<?php

namespace App\Tests;

use App\Entity\Annonce;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AnnonceEntityTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        // Use Attribute mapping for Symfony 6/7 validation
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    private function getEntity(): Annonce
    {
        return (new Annonce())
            ->setTitre("Titre de test")
            ->setDescription("Description de test avec plus de dix caractères")
            ->setPosteRecherche("Développeur")
            ->setNiveauRequis("Débutant")
            ->setDatePublication(new \DateTime('today'))
            ->setStatut("active");
    }

    public function testValidAnnonce(): void
    {
        $annonce = $this->getEntity();
        $errors = $this->validator->validate($annonce);
        $this->assertCount(0, $errors);
    }

    public function testInvalidEmptyTitle(): void
    {
        $annonce = $this->getEntity()->setTitre("");
        $errors = $this->validator->validate($annonce);

        // At least one error (NotBlank)
        $this->assertGreaterThan(0, count($errors));

        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }
        $this->assertContains("Le titre est obligatoire", $messages);
    }

    public function testInvalidPastDate(): void
    {
        // Yesterday
        $annonce = $this->getEntity()->setDatePublication(new \DateTime('yesterday'));
        $errors = $this->validator->validate($annonce);

        $this->assertGreaterThan(0, count($errors));

        $messages = [];
        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }
        $this->assertContains("La date de publication doit être aujourd'hui ou une date ultérieure", $messages);
    }

    public function testValidFutureDate(): void
    {
        $annonce = $this->getEntity()->setDatePublication(new \DateTime('+1 week'));
        $errors = $this->validator->validate($annonce);
        $this->assertCount(0, $errors);
    }

    public function testGettersSetters(): void
    {
        $annonce = new Annonce();
        $annonce->setTitre("Mon Titre");
        $annonce->setDescription("Une description");

        $this->assertEquals("Mon Titre", $annonce->getTitre());
        $this->assertEquals("Une description", $annonce->getDescription());
    }
}
