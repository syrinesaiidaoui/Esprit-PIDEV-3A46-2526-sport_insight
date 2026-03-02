<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Matchs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_match', TextType::class, [
                'label' => 'Identifiant du Match',
                'attr' => ['placeholder' => 'Ex: M-2024-001'],
            ])
            ->add('dateMatch', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date du Match',
            ])
            ->add('heureDebut', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure de Début',
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
            ])
            ->add('type', TextType::class, [
                'label' => 'Type de Match',
            ])
            ->add('statut', TextType::class, [
                'label' => 'Statut',
            ])
            ->add('lineup_domicile', TextType::class, [
                'label' => 'Composition Domicile',
                'required' => false,
            ])
            ->add('lineup_exterieur', TextType::class, [
                'label' => 'Composition Extérieur',
                'required' => false,
            ])
            ->add('equipeDomicile', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom',
                'label' => 'Équipe Domicile',
            ])
            ->add('equipeExterieur', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom',
                'label' => 'Équipe Extérieur',
            ])
            ->add('scoreEquipeDomicile', IntegerType::class, [
                'label' => 'Score Domicile',
                'required' => false,
            ])
            ->add('scoreEquipeExterieur', IntegerType::class, [
                'label' => 'Score Extérieur',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Matchs::class,
        ]);
    }
}
