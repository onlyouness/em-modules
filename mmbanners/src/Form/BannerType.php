<?php

namespace Hp\Mmbanners\Form;


use Hp\Mmbanners\Entity\Banner;
use Hp\Mmbanners\Entity\Position;
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

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $products =$options['products'];

        $builder
            ->add('title',TextType::class,array(
                'attr' => array(
                    'placeholder' => 'Add a title',
                )
            ))
            ->add('shortDescription',TextType::class,array(
                'attr' => array(
                    'placeholder' => 'Add a short descripiton',
                )
            ))
            ->add('description',FormattedTextareaType::class,array(
                'attr' => array(
                    'placeholder' => 'Add a description',
                )
            ))
            ->add('product', ChoiceType::class, [
                'label' => 'Product',
                'choices' => $products,
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
                'required' => true,

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
            'data_class' => Banner::class,
            'products' => [],
        ]);
    }


}