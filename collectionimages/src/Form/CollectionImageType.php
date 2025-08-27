<?php

namespace Hp\Collectionimages\Form;

use Hp\Collectionimages\Entity\QbCollectionImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface as FormFormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;

class CollectionImageType extends AbstractType
{

    public function buildForm(FormFormBuilderInterface $builder, array $options)
    {
        $builder
            // CollectionImageType.php
            ->add('image', FileType::class, [
                'label' => 'Image (JPEG/PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new ConstraintsFile([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QbCollectionImage::class,
        ]);
    }
}
