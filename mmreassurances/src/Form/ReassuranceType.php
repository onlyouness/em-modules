<?php
namespace Hp\Mmreassurances\Form;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ReassuranceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TranslatableType::class, [
                'label'    => 'Add title',
                'required' => true,
                'type'     => TextType::class,
                'attr'     => [
                    'placeholder' => 'Title here',
                ],
            ])
            ->add('description', TranslatableType::class, [
                'label'    => 'Add Description',
                'required' => true,
                'type'     => FormattedTextareaType::class,
                'options'  => [
                    'attr' => [
                        'class'       => 'autoload_rte',
                        'data-rte'    => 'true',
                        'placeholder' => '',
                    ],
                ],
            ])->add('image', FileType::class, [
            'label'    => 'Image',
            'required' => true,
            'mapped'   => false,
        ])
        ;
    }

}
