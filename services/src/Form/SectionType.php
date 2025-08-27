<?php
declare(strict_types=1);

namespace Hp\Services\Form;

use Hp\Services\Entity\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Explicitly set the form to be compound
        

        $builder
            ->add('title', TextType::class, [
                'label' => 'Section Title',
                'required' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter title']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Section Description',
                'required' => true,
                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Enter description']
            ])
            ->add('shortDescription', TextType::class, [
                'label' => 'Section short descrtiption',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter Short description']

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Section::class, // The data to bind to the form
        ]);
    }
}
