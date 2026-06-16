<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Size;
use App\Repository\SizeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProductType extends AbstractType
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $this->getCurrentLocale();

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit (FR) *',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('nameAr', TextType::class, [
                'label' => 'Nom du produit (AR) *',
                'attr' => ['class' => 'form-control', 'dir' => 'rtl'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 255),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (FR) *',
                'attr' => ['class' => 'form-control', 'rows' => 4],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('descriptionAr', TextareaType::class, [
                'label' => 'Description (AR) *',
                'attr' => ['class' => 'form-control', 'rows' => 4, 'dir' => 'rtl'],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix *',
                'scale' => 2,
                'attr' => ['class' => 'form-control', 'step' => '0.01', 'min' => 0],
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\PositiveOrZero(),
                ],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantite en stock *',
                'attr' => ['class' => 'form-control', 'min' => 0],
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\PositiveOrZero(),
                ],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Categorie *',
                'class' => Category::class,
                'choice_label' => static fn (Category $category): string => (string) ($category->getLocalizedName($locale) ?: $category->getName()),
                'attr' => ['class' => 'form-select'],
                'placeholder' => $locale === 'ar' ? 'اختر قسما...' : 'Choisir une categorie...',
            ])
            ->add('sizes', EntityType::class, [
                'class' => Size::class,
                'choice_label' => static fn (Size $size): string => (string) ($size->getLocalizedName($locale) ?: $size->getName()),
                'multiple' => true,
                'expanded' => false,
                'query_builder' => function (SizeRepository $sizeRepository) {
                    return $sizeRepository->createQueryBuilder('s')
                        ->where('s.isActive = :active')
                        ->setParameter('active', true)
                        ->orderBy('s.position', 'ASC');
                },
                'attr' => [
                    'class' => 'form-select product-size-select',
                    'data-placeholder' => $locale === 'ar' ? 'ابحث واختر عدة مقاسات' : 'Rechercher et choisir plusieurs tailles',
                    'data-allow-clear' => 'true',
                ],
                'label' => 'Tailles disponibles',
            ])
            ->add('color', TextareaType::class, [
                'label' => 'Couleurs disponibles (FR) *',
                'help' => 'Separez les couleurs par des virgules : Noir, Blanc, Rose poudre',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                    'placeholder' => 'Noir, Blanc, Rose poudre',
                ],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('colorAr', TextareaType::class, [
                'label' => 'Couleurs disponibles (AR) *',
                'help' => 'Gardez le meme ordre que les couleurs FR.',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                    'placeholder' => 'أسود، أبيض، وردي باهت',
                    'dir' => 'rtl',
                ],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Produit actif',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }

    private function getCurrentLocale(): string
    {
        return $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';
    }
}
