<?php

namespace Hp\Productselect\Form;



use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface as FormFormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSelectType extends AbstractType
{
    public function buildForm(FormFormBuilderInterface $builder, array $options)
    {
        $products = $options['data']['products_options'];
        $categories = $options['data']['categories_options'];
        // \Tools::dieObject($products);
        $builder
            ->add('products', ChoiceType::class, [
                'label'       => 'Choose products',
                'multiple'=>true,
                'choices'     => $products,
                'placeholder' => 'Select a product',
                'required'    => false,
                'attr' => ['class' => 'chosen-select'],
            ])
            ->add('category_id', ChoiceType::class, [
                'label'       => 'Choose category',
                'multiple'=>false,
                'choices'     => $categories,
                'placeholder' => 'Select a Category',
                'required'    => false,
                'attr' => ['class' => 'chosen-select'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Enter title',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter A Custom title if you didn\'t choose a category',
                ],
            ])
            ->add('link', TextType::class, [
                'label' => 'Enable link',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter a link if you didn\'t choose a category',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'products_options' => [],
            'categories_options' => [],
        ]);

    }

}

