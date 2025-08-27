<?php

declare (strict_types = 1);

namespace Hp\Producttag\Form\Modifier;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\FormBuilderModifier;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductFormModifier
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

    public function modify(int $productId, FormBuilderInterface $productFormBuilder): void
    {
        $db            = \Db::getInstance();
        $idLangs       = array_column(\Language::getLanguages(false), 'id_lang');
        $idLangDefault = (int) \Configuration::get('PS_LANG_DEFAULT');

        $titleData       = [];
        $descriptionData = [];

        $sql    = 'SELECT id FROM `' . _DB_PREFIX_ . 'product_more_info` WHERE id_product = ' . (int) $productId;
        $infoId = (int) $db->getValue($sql);

        if ($infoId) {
            $sql     = 'SELECT id_lang, title, description FROM `' . _DB_PREFIX_ . 'product_more_info_lang` WHERE id_info = ' . (int) $infoId;
            $results = $db->executeS($sql);

            foreach ($results as $row) {
                $titleData[(int) $row['id_lang']]       = $row['title'];
                $descriptionData[(int) $row['id_lang']] = $row['description'];
            }
        }

        $descriptionFormBuilder = $productFormBuilder->get('description');

        $this->formBuilderModifier->addAfter(
            $descriptionFormBuilder,
            'description',
            'more_info_title',
            TranslatableType::class,
            [
                'label'    => 'Add title',
                'required' => false,
                'type'     => TextType::class,
                'data'     => $titleData,
                'attr'     => [
                    'placeholder' => 'Title here',
                ],
            ]
        );

        $this->formBuilderModifier->addAfter(
            $descriptionFormBuilder,
            'more_info_title',
            'more_info_description',
            TranslatableType::class,
            [
                'label'    => 'Add description',
                'required' => false,
                'type'     => FormattedTextareaType::class,
                'data'     => $descriptionData,
                'options'  => [
                    'attr' => [
                        'class'       => 'autoload_rte',
                        'data-rte'    => 'true',
                        'placeholder' => '',
                    ],
                ],
            ]
        );
    }

}
