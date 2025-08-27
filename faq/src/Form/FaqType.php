<?php
namespace Hp\Faq\Form;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface as FormFormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tools;

class FaqType extends AbstractType{

    public function buildForm(FormFormBuilderInterface $builder, array $options)
    {
        // Tools::dieObject($options);
        $groups = $options['data']['groups'];
        $builder
        ->add('question', TranslatableType::class, [
            'label' => 'Add Question',
            'required' => true,
            'type'=>FormattedTextareaType::class,
            'attr' => array(                    
                'placeholder' => 'Ex: How long will it take for my order to arrive?',
            )
        ])
        ->add('response', TranslatableType::class, [   
            'label' => 'Add Response',
            'required' => true,
            'type'=>TextType::class,
            'attr' => array(
                    'placeholder' => 'Ex: It depends on delivery address but usually takes two days',
                )
        ])
        ->add('group',ChoiceType::class,[
            'label'=>'Select A To Group',
            'required' => true,
                'choices' => $groups,
                'expanded'=>false,
                'multiple'=>false,
                'choice_label' => function ($value, $key, $index) {
                    return $key; 
                },
                'choice_value' => function ($key) {
                    return $key; 
                },
                'attr' => ['class' => 'product-select'],
                'required' => true,
            ])
        ->add('section',ChoiceType::class,[
            'label'=>'Select A To Section where to display it',
            'required' => true,
                'choices' => [
                    'Right Section'=>'1',
                    'Left Section'=>'2',
                ],
                'expanded'=>false,
                'multiple'=>false,
                'choice_label' => function ($value, $key, $index) {
                    return $key; 
                },
                'choice_value' => function ($key) {
                    return $key; 
                },
                'required' => true,
            
        ])
        ->add('active', SwitchType::class, [
            'label'=>'Active the FAQ',
            'required'=>false,
            'choices' => [
                'Active' => 1,
                'Desactive' => 0,
            ],
        ]);

        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, 
            'groups' => [],
        ]);
    }
}