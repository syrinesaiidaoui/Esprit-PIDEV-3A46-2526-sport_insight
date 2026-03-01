<?php

namespace App\Form;

use App\Entity\Entrainement;
use App\Entity\User;
<<<<<<< HEAD
=======
use Doctrine\ORM\EntityRepository;
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntrainementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateEntrainement')
            ->add('heureDebut')
            ->add('heureFin')
            ->add('type')
            ->add('objectif')
            ->add('lieu')
            ->add('entraineur', EntityType::class, [
<<<<<<< HEAD
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('joueurs', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
=======
                'class'        => User::class,
                'choice_label' => 'nomComplet',
                'placeholder'  => '-- Choisir un entraîneur --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_ENTRAINEUR%')
                        ->orderBy('u.nom', 'ASC');
                },
            ])
            ->add('joueurs', EntityType::class, [
                'class'        => User::class,
                'choice_label' => 'nomComplet',
                'multiple'     => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_JOUEUR%')
                        ->orderBy('u.nom', 'ASC');
                },
>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entrainement::class,
        ]);
    }
}
<<<<<<< HEAD
=======

>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
