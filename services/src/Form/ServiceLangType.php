<?php
declare(strict_types=1);

namespace Hp\Services\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Hp\Services\Entity\Service;
use Hp\Services\Entity\ServiceLang;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ServiceLangType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idLang', ChoiceType::class, [
            'label'=>'lang',
            'choices' => [
                'fr' => 1,
                'en' => 2,
            ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Service Title',
                'required' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter service title']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Service Description',
                'required' => true,
                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Enter service description']
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ServiceLang::class,
        ]);
    }
}
