<?php
// src/Form/CategoryType.php
namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la catégorie *',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Robes, Jupes, Pantalons...'
                ]
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug (URL) *',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: robes, jupes, pantalons'
                ],
                'help' => 'Version URL du nom (minuscules, tirets)'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Description de la catégorie...'
                ]
            ])
            ->add('color', ColorType::class, [
                'label' => 'Couleur',
                'required' => false,
                'attr' => [
                    'class' => 'form-control form-control-color'
                ]
            ])
            ->add('icon', ChoiceType::class, [
                'label' => 'Icône',
                'required' => false,
                'choices' => [
                    'Robe' => 'fas fa-dress',
                    'T-shirt' => 'fas fa-tshirt',
                    'Jupe' => 'fas fa-skirt',
                    'Pantalon' => 'fas fa-vest',
                    'Veste' => 'fas fa-jacket',
                    'Chaussure' => 'fas fa-shoe-prints',
                    'Sac' => 'fas fa-handbag',
                    'Bijou' => 'fas fa-gem',
                    'Maillot' => 'fas fa-swimmer',
                    'Accessoire' => 'fas fa-glasses',
                    'Sous-vêtement' => 'fas fa-tshirt',
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
                'label' => 'Catégorie active',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}