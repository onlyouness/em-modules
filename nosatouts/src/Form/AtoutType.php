<?php
namespace Hp\Nosatouts\Form;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface as FormFormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AtoutType extends AbstractType
{

    public function buildForm(FormFormBuilderInterface $builder, array $options)
    {
        // Tools::dieObject($options);
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
            ])
            ->add('image', FileType::class, [
                'label'       => 'Image',
                'mapped'      => false,
                'required'    => false,

                'attr'        => ['class' => 'form-control-file'],
                'constraints' => [
                    new File([
                        'maxSize'          => '5M',
                        'mimeTypes'        => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF).',
                    ]),
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
