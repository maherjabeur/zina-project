<?php
// src/Form/SettingsType.php
namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingFee', NumberType::class, [
                'label' => 'Frais de livraison',
                'required' => true,
                'scale' => 2,
                'attr' => [
                    'step' => '0.01',
                    'min' => '0',
                ],
            ])
            ->add('seoTitle', TextType::class, [
                'label' => 'Titre SEO du site',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                    'placeholder' => 'Bella Couture - Mode feminine elegante',
                ],
            ])
            ->add('seoDescription', TextareaType::class, [
                'label' => 'Description SEO',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'maxlength' => 320,
                    'placeholder' => 'Decrivez la boutique en 150 a 160 caracteres environ.',
                ],
            ])
            ->add('seoKeywords', TextareaType::class, [
                'label' => 'Mots-cles',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'mode feminine, vetements femme, boutique Sousse',
                ],
            ])
            ->add('seoImage', TextType::class, [
                'label' => 'Image de partage',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                    'placeholder' => 'logo/logo.webp ou https://...',
                ],
            ])
            ->add('seoAuthor', TextType::class, [
                'label' => 'Auteur / Marque',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                    'placeholder' => 'Bella Couture',
                ],
            ])
            ->add('seoIndexingEnabled', CheckboxType::class, [
                'label' => "Autoriser l'indexation Google",
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
