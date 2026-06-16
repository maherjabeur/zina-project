<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Promotion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PromotionType extends AbstractType
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $this->getCurrentLocale();

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la promotion *',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Soldes week-end',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('titleAr', TextType::class, [
                'label' => 'Titre de la promotion (AR) *',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'مثال: تخفيضات نهاية الأسبوع',
                    'dir' => 'rtl',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (FR) *',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Expliquez l offre...',
                ],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('descriptionAr', TextareaType::class, [
                'label' => 'Description (AR) *',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'اشرح العرض...',
                    'dir' => 'rtl',
                ],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('discount', NumberType::class, [
                'label' => 'Remise (%) *',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 100,
                    'placeholder' => 'Ex: 20',
                ],
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Range(min: 1, max: 100),
                ],
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => static fn (Product $product): string => (string) ($product->getLocalizedName($locale) ?: $product->getName()),
                'label' => 'Produit concerne *',
                'attr' => ['class' => 'form-select'],
                'placeholder' => $locale === 'ar' ? 'اختر منتجا' : 'Choisir un produit',
                'constraints' => [new Assert\NotNull()],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }

    private function getCurrentLocale(): string
    {
        return $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
    }
}
