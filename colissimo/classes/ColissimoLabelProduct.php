<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2024 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class ColissimoLabelProduct
 */
class ColissimoLabelProduct extends ObjectModel
{
    /** @var int */
    public $id_colissimo_label_product;

    /** @var int */
    public $id_colissimo_label;

    /** @var int */
    public $id_product;

    /** @var int */
    public $id_product_attribute;

    /** @var int */
    public $quantity;

    /** @var string */
    public $date_add;

    /** @var array */
    public static $definition = [
        'table' => 'colissimo_label_product',
        'primary' => 'id_colissimo_label_product',
        'fields' => [
            'id_colissimo_label' => [
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'required' => true,
            ],
            'id_product' => [
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => true,
            ],
            'id_product_attribute' => [
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => true,
            ],
            'quantity' => [
                'type' => self::TYPE_INT,
                'validate' => 'isInt',
                'required' => true,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
            ],
        ],
    ];

    /**
     * @param int $idColissimolabel
     * @return bool
     */
    public function deleteLabelProducts($idColissimolabel)
    {
        Db::getInstance()->delete('colissimo_label_product', 'id_colissimo_label = ' . (int) $idColissimolabel);

        return true;
    }

    /**
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idColissimoOrder
     * @return int
     */
    public static function getProductShippedQuantity($idProduct, $idProductAttribute, $idColissimoOrder)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('SUM(clp.quantity)')
            ->from('colissimo_label_product', 'clp')
            ->leftJoin('colissimo_label', 'cl', 'cl.id_colissimo_label = clp.id_colissimo_label')
            ->where('clp.id_product =' . (int) $idProduct)
            ->where('clp.id_product_attribute =' . (int) $idProductAttribute)
            ->where('cl.id_colissimo_order = ' . (int) $idColissimoOrder);
        $nbProducts = (int) Db::getInstance()->getValue($dbQuery);

        return $nbProducts;
    }

    /**
     * @param int $idProduct
     * @param int $idProductAttribute
     * @param int $idColissimoOrder
     * @return array
     */
    public function getRelatedLabels($idProduct, $idProductAttribute, $idColissimoOrder)
    {
        $labels = [];
        $dbQuery = new DbQuery();
        $dbQuery->select('clp.id_colissimo_label')
            ->from('colissimo_label_product', 'clp')
            ->leftJoin('colissimo_label', 'cl', 'cl.id_colissimo_label = clp.id_colissimo_label')
            ->where('clp.id_product =' . (int) $idProduct)
            ->where('clp.id_product_attribute =' . (int) $idProductAttribute)
            ->where('cl.id_colissimo_order = ' . (int) $idColissimoOrder);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)
            ->executeS($dbQuery);
        foreach ($result as $key => $raw) {
            $label = new ColissimoLabel((int) $raw['id_colissimo_label']);
            $labels[$key] = $label;
        }

        return $labels;
    }
}
