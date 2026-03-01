<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Last name',
                'attr' => ['autocomplete' => 'family-name'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'First name',
                'attr' => ['autocomplete' => 'given-name'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['autocomplete' => 'email'],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Phone (optional)',
                'required' => false,
                'attr' => ['autocomplete' => 'tel'],
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Birth date (optional)',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'The password fields must match.',
                'first_options' => [
                    'label' => 'Password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'second_options' => [
                    'label' => 'Confirm password',
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a password']),
                    new Length([
                        'min' => 8,
                        'max' => 4096,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
