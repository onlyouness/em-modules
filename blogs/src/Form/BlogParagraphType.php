<?php

namespace Hp\Blogs\Form;

use Hp\Blogs\Entity\BlogParagraph;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BlogParagraphType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('title', TextType::class, [
            'label' => 'Paragraph Title',
            'required' => false,
            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter Paragraph title'],

        ])

            ->add('description', TextareaType::class, [

                'label' => 'Paragraph Description',
                'required' => true,
                'attr' => [
                    'class' => 'form-control tinymce', // Add a class for TinyMCE
                    'rows' => 5,
                    'placeholder' => 'Enter Paragraph description',
                ],
            ])

            ->add('image', FileType::class, [

                'label' => 'Paragraph Image',
                'mapped' => false,
                'required' => false,

                'attr' => ['class' => 'form-control-file'],
                'constraints' => [
                    new File([
                        'maxSize' => 10485760, // 10 MB in bytes
                        'mimeTypes' => [
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
            'data_class' => BlogParagraph::class,

        ]);
    }
}
