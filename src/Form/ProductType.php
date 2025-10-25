<?php
// src/Form/ProductType.php
namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Size;
use App\Repository\SizeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit *',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description *',
                'attr' => ['class' => 'form-control', 'rows' => 4]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix *',
                'scale' => 2,
                'attr' => ['class' => 'form-control', 'step' => '0.01']
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité en stock *',
                'attr' => ['class' => 'form-control', 'min' => 0]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie *',
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => ['class' => 'form-select'],
                'placeholder' => 'Choisir une catégorie...'
            ])
            ->add('sizes', EntityType::class, [
                'class' => Size::class,
                'choice_label' => 'name',
                'multiple' => true, // Permettre la sélection multiple
                'expanded' => false, // Ou true pour des checkboxes
                'query_builder' => function (SizeRepository $sizeRepository) {
                    return $sizeRepository->createQueryBuilder('s')
                        ->where('s.isActive = :active')
                        ->setParameter('active', true)
                        ->orderBy('s.position', 'ASC');
                },
                'attr' => [
                    'class' => 'form-select',
                    'data-placeholder' => 'Choisir les tailles'
                ],
                'label' => 'Tailles'
            ])
            ->add('color', TextType::class, [
                'label' => 'Couleur *',
                'attr' => ['class' => 'form-control']
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Produit actif',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
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