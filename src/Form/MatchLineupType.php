<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Joueur;
use App\Entity\MatchLineup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\QueryBuilder;

class MatchLineupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $equipe = $options['equipe'];
        
        $builder
            ->add('joueur', EntityType::class, [
                'class' => Joueur::class,
                'choice_label' => function(Joueur $joueur) {
                    return sprintf('%d - %s %s', $joueur->getNumero(), $joueur->getNom(), $joueur->getPrenom());
                },
                'query_builder' => function (QueryBuilder $qb) use ($equipe) {
                    return $qb
                        ->andWhere('j.equipe = :equipe')
                        ->setParameter('equipe', $equipe)
                        ->orderBy('j.numero', 'ASC');
                },
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Joueur',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MatchLineup::class,
            'equipe' => null,
        ]);
    }
}
