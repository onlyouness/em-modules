<?php
namespace Hp\Collectionimages\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface as FormFormBuilderInterface;

class CollectionType extends AbstractType{

    public function buildForm(FormFormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('url',TextType::class,[
            'label'=>'Add Url',
            'required' => true,
            'attr' => array(
                    'placeholder' => 'Add url of your images',
                )
        ]);

    }
}