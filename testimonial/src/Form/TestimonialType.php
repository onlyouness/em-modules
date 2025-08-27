<?php

declare(strict_types=1);
namespace Hp\Testimonial\Form;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class TestimonialType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',TextType::class,[
            'label'=>'Enter the name',
        ])->add('message',TextareaType::class,[
            'label'=>'Enter the message',
            'help' => 'Message content (e.g. All for one, one for all).',
        ])
        ;
    }
}