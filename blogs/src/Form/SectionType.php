<?php

namespace Hp\Blogs\Form;

use Context;

use Hp\Blogs\Entity\Section;
use PrestaShop\PrestaShop\Adapter\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $products = array_unique($options['products']);

        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
            ])
            ->add('products', ChoiceType::class, [
                'label' => 'Products',
                'choices' => $products,
                'expanded'=>true,
                'multiple'=>true,
                'choice_label' => function ($value, $key, $index) {
                    return $key; 
                },
                'choice_value' => function ($key) {
                    return $key; 
                },
                'attr' => ['class' => 'product-select'],
                'required' => true,
            ]);


        // dump($categories);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
            'products' => [],
        ]);
    }
}
