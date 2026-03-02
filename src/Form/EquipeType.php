<?php

namespace App\Form;

use App\Entity\Equipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints as Assert;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_equipe', TextType::class, [
                'label' => 'Identifiant de l\'équipe',
                'attr' => ['placeholder' => 'Ex: EQ-001'],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'identifiant de l\'équipe est obligatoire.'
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'équipe',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom de l\'équipe est obligatoire.'
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom de l\'équipe ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('coach', TextType::class, [
                'label' => 'Nom du coach',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'Le nom du coach ne peut pas dépasser {{ limit }} caractères.'
                    ]),
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email de contact',
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => 'Logo de l\'équipe',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, GIF, WebP)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
            'attr' => [
                'novalidate' => 'novalidate', // Disable HTML5 validation
            ],
        ]);
    }
}
