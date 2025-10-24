<?php
// src/Form/SizeType.php
namespace App\Form;

use App\Entity\Size;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SizeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la taille *',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Small, Medium, 38, 40...'
                ]
            ])
            ->add('code', TextType::class, [
                'label' => 'Code *',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: S, M, L, 38, 40'
                ],
                'help' => 'Code court utilisé pour l\'affichage'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de taille *',
                'choices' => [
                    'Vêtements' => 'clothing',
                    'Chaussures' => 'shoes',
                    'Accessoires' => 'accessories'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Position',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0
                ],
                'help' => 'Ordre d\'affichage (plus petit = premier)'
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Taille active',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Size::class,
        ]);
    }
}