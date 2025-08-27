<?php

namespace Hp\Mmbrandbanner\Form;

use Hp\Mmbrandbanner\Entity\BrandBanner;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;

class BrandBannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $brands =$options['brands'];

        $builder
            ->add('title',TextType::class,array(
                'attr' => array(
                    'placeholder' => 'Add a title',
                ),
                'required'=>false,
            ))
            ->add('description',FormattedTextareaType::class,array(
                'attr' => array(
                    'placeholder' => 'Add a description',
                ),
                'required'=>false,
            ))
            ->add('manufacturer', ChoiceType::class, [
                'label' => 'Manufacturer',
                'choices' => $brands,
                'choice_label' => function ($value, $key, $index) {
                    return $key;
                },
                'choice_value' => function ($key) {
                    return $key;
                },
                'attr' => ['class' => 'product-select'],
                'required' => true,
            ])
            ->add('image',FileType::class,array(
                'label' => 'Bannner Image',
                'mapped' => false,
                'required' => false,

                'attr' => ['class' => 'form-control-file'],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF).',
                    ]),
                ],
            ))


        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BrandBanner::class,
            'brands' => [],
        ]);
    }


}