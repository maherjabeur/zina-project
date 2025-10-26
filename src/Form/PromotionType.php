<?php

namespace App\Form;

use App\Entity\Promotion;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EntityType; // For selecting a product
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Promotion Title'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('discount', NumberType::class, [
                'label' => 'Discount (%)'
            ])
            ->add('product', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',  // Display product name
                'label' => 'Select Product'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
