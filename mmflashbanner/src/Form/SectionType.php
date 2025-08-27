<?php

namespace Hp\MmFlashBanner\Form;

use Hp\MmFlashBanner\Entity\Section;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('shortDescription',TextType::class,array(
            'attr' => array(
                'placeholder' => 'Add a short description',
            )
        ))
            ->add('title',TextType::class,array(
                'attr' => array(
                    'placeholder' => 'Add a title',
                )
            ))
            ->add('description',FormattedTextareaType::class,array(
                'attr' => array(
                    'placeholder' => 'Add a description',
                )
            ))



        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }

}