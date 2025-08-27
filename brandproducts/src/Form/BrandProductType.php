<?php

declare (strict_types = 1);
namespace Hp\Brandproducts\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BrandProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // \Tools::dieObject($options);
        $builder
            ->add('display_type', ChoiceType::class, [
                'label'    => 'Choose what do you want to display',
                'choices'  => [
                    'Category Products' => 'category',
                    'Brand Products'    => 'brand',
                ],
                'expanded' => true,
                'required' => true,
            ])->add('brand', ChoiceType::class, [
                'label'    => 'Choose a brand',
                'choices'     =>  $options['data']['brands'],
                'placeholder' => 'Select a Brand',
                'required'    => false,
            ])->add('category', ChoiceType::class, [
                'label'    => 'Choose a Category',
                'choices'     => $options['data']['categories'],
                'placeholder' => 'Select a Category',
                'required'    => false,
            ]);
        // $builder->addEventListener(
        //     FormEvents::PRE_SUBMIT,
        //     function (FormEvent $event) use ($options) {
        //         $data = $event->getData();
        //         $form = $event->getForm();
        //         if (isset($data['display_type']) && $data['display_type'] === 'brand') {
        //             $form->add('brand', ChoiceType::class, [
        //                 'choices'     =>  $options['data']['brands'],
        //                 'placeholder' => 'Select a Brand',
        //                 'required'    => false,
        //             ]);
        //         }
        //         if (isset($data['display_type']) && $data['display_type'] === 'category') {
        //             $form->add('category', ChoiceType::class, [
        //                 'choices'     => $options['data']['categories'],
        //                 'placeholder' => 'Select a Category',
        //                 'required'    => false,
        //             ]);
        //         }
        //     }
        // );
    }
}
