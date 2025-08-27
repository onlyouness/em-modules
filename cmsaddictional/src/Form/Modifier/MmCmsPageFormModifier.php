<?php

declare (strict_types = 1);

namespace  Hp\Cmsaddictional\Form\Modifier;

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use PrestaShopBundle\Form\FormBuilderModifier;
use Symfony\Component\Form\FormBuilderInterface;

final class MmCmsPageFormModifier
{
    /**
     * @var FormBuilderModifier
     */
    private $formBuilderModifier;

    /**
     * @param FormBuilderModifier $formBuilderModifier
     */
    public function __construct(
        FormBuilderModifier $formBuilderModifier
    ) {
        $this->formBuilderModifier = $formBuilderModifier;
    }

    /**
     * @param int|null $productId
     * @param FormBuilderInterface $productFormBuilder
     */
    public function modify(
        int $productId,
        FormBuilderInterface $cmsFormBuilder
    ): void {

        $descriptionFormBuilder = $cmsFormBuilder;
        $this->formBuilderModifier->addAfter(
            $descriptionFormBuilder,
            'title',
            'mini_description',
            TranslatableType::class,
            [
                'label'      => 'Add Mini Description',
                'required'   => true,
                'type'       => TextType::class,
                'attr'       => [
                    'placeholder' => 'Ex: Mini',
                ],
                'data'       => '',
                'empty_data' => '',
                'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit_base.html.twig',
            ]

        );
    }


}
