<?php



namespace Hp\Blogs\Form;



use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\OptionsResolver\OptionsResolver;



use Hp\Blogs\Entity\Blog;
use Hp\Blogs\Form\SectionType;
use Symfony\Component\Validator\Constraints\File;

class BlogType extends AbstractType

{

    public function buildForm(FormBuilderInterface $builder, array $options)

    {

        $builder->add('title', TextType::class, [

            'label' => 'Blog Title',

            'required' => true,


            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter blog title']

        ])

            ->add('description', TextareaType::class, [

                'label' => 'Blog Description',
                'required' => true,

                // 'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Enter blog short description']
               
            ])
            ->add('shortDescription', TextType::class, [

                'label' => 'Blog Short Description',
                'required' => true,

                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Enter blog description']

            ])

            ->add('image', FileType::class, [

                'label' => 'Blog Image',
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
        // var_dump($options['categories']);

    }

    public function configureOptions(OptionsResolver $resolver)

    {

        $resolver->setDefaults([
            'data_class' => Blog::class,
            // 'categories' => [],

        ]);
    }
}
