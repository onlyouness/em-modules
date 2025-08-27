<?php
namespace Developpement\Checkoutinformation\Form;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface as FormFormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EnseigneContactType extends AbstractType
{
    public function buildForm(FormFormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subtitle', TranslatableType::class, [
                'label'    => '',
                'required' => true,
                'type'     => TextType::class,
                'attr'     => [
                    'placeholder' => "here is the sub title",
                    'class'       => 'form-control',
                ],
            ])
            ->add('title', TranslatableType::class, [
                'label'    => '',
                'required' => true,
                'type'     => TextType::class,
                'attr'     => [
                    'placeholder' => "here the title",
                    'class'       => 'form-control',
                ],
            ])
            ->add('description', TranslatableType::class, [
                'label'    => '',
                'required' => true,
                'type'     => FormattedTextareaType::class,
                'attr'     => [
                    'placeholder' => "here is the description",
                    'class'       => 'form-control',
                ],
            ])
            ->add('link', TranslatableType::class, [
                'label'    => '',
                'required' => true,
                'type'     => TextType::class,
                'attr'     => [
                    'placeholder' => "The Link",
                    'class'       => 'form-control',
                ],
            ])
            ->add('bgimage', FileType::class, [
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
            ])
            ->add('mapimage', FileType::class, [
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
            ])
        ;

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
