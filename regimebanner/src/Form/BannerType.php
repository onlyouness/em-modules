<?php
namespace Hp\Regimebanner\Form;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface as FormFormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BannerType extends AbstractType
{

    public function buildForm(FormFormBuilderInterface $builder, array $options)
    {
        // Tools::dieObject($options);
        $features = $options['data']['features'];
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
                'label'       => 'Bannner Image',
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
            ->add('features', ChoiceType::class, [
                'label'        => 'Select A To Features',
                'required'     => true,
                'choices'      => $features,
                'expanded'     => false,
                'multiple'     => false,
                'choice_label' => function ($value, $key, $index) {
                    return $key;
                },
                'choice_value' => function ($key) {
                    return $key;
                },
                'attr'         => ['class' => 'product-select'],
                'required'     => true,
            ])
            

        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'groups'     => [],
        ]);
    }
}
