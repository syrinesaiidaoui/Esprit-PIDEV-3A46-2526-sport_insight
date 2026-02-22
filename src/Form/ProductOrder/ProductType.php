<?php

namespace App\Form\ProductOrder;

use App\Entity\ProductOrder\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Entrez le nom du produit'],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du produit est obligatoire']),
                    new Length(['min' => 3, 'max' => 255]),
                ],
            ])
            ->add('category', TextType::class, [
                'label' => 'Categorie',
                'required' => false,
                'attr' => ['placeholder' => 'Categorie optionnelle'],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'currency' => 'USD',
                'attr' => ['placeholder' => 'ex: 19.99'],
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']),
                    new PositiveOrZero(['message' => 'Le prix doit etre positif']),
                ],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'attr' => ['placeholder' => 'Quantite en stock'],
                'constraints' => [
                    new NotBlank(['message' => 'Le stock est obligatoire']),
                    new GreaterThanOrEqual(['value' => 0, 'message' => 'Le stock doit etre superieur ou egal a 0']),
                ],
            ])
            ->add('size', TextType::class, [
                'label' => 'Taille',
                'required' => false,
                'attr' => ['placeholder' => 'Taille optionnelle'],
            ])
            ->add('brand', TextType::class, [
                'label' => 'Marque',
                'required' => false,
                'attr' => ['placeholder' => 'Marque optionnelle'],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du produit',
                'required' => false,
                'mapped' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer le produit',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
