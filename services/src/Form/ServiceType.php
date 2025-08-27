<?php
declare(strict_types=1);

namespace Hp\Services\Form;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Hp\Services\Entity\Service;
 
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ServiceType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('image', FileType::class, [
                'label' => 'Service Image',
                'required' => false,
                'data_class'=>null,
                'attr' => ['class' => 'form-control-file']
            ]) ->add('title', TextType::class, [
                'label' => 'Service Title',
                'required' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter service title']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Service Description',
                'required' => true,
                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Enter service description']
            ]);;

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Service::class,
        ]);
    }

}
