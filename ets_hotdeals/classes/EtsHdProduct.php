<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }
class EtsHdProduct
{
    public static function is17()
    {
        return version_compare(_PS_VERSION_, '1.7', '>=');
    }
    public static function getHotDealProducts()
    {
        if($configProducts = Configuration::get('ETS_HOTDEALS_PRODUCT_IDS'))
        {
            $context = Context::getContext();
            $Ids= explode(',',$configProducts);
            $productAttributeIds=array();
            $productIds=array();
            if($Ids)
            {
                foreach($Ids as $Id)
                {
                    $id = explode('-',$Id);
                    $productIds[]= (int)$id[0];
                    $productAttributeIds[]= (int)$id[1];
                }
            }
            else{
                $productAttributeIds=array(0);
                $productIds=array(0);
            }

            $products = Db::getInstance()->executeS('
            SELECT p.id_product,p.`reference`,pl.name,pl.link_rewrite,pa.id_product_attribute 
            FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product=pl.id_product AND pl.id_lang='.(int)$context->language->id.') AND pl.id_shop='.(int)$context->shop->id.'
            LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON(pa.id_product=p.id_product AND pa.id_product_attribute IN ('.implode(',',$productAttributeIds).'))
            WHERE p.id_product IN ('.implode(',',$productIds).')');
            if($products) {
                foreach ($products as &$product) {
                    if ($product['id_product_attribute']) {
                        $image = Product::getCombinationImageById($product['id_product_attribute'], Context::getContext()->language->id);
                        $id_image = $image ? $image['id_image'] : null;
                    }
                    if (!isset($id_image) || $id_image)
                        $id_image = Db::getInstance()->getValue("SELECT id_image FROM `" . _DB_PREFIX_ . "image` WHERE id_product=" . (int)$product['id_product'] . ' AND cover=1');
	                $type_image = ImageType::getFormattedName('small');
                    $url_image = Context::getContext()->link->getImageLink($product['link_rewrite'], $id_image, $type_image);
                    if ($product['id_product_attribute'])
                        $product['attributes'] = self::getAllAttributes($product['id_product_attribute'])['attributes'];
                    else
                        $product['attributes'] = "Null";
                    $product['url_image'] = $url_image;
                }
                return $products;
            }
        }
        return array();
    }

    public static function getAllAttributes($id_product_attribute)
    {
        $context = Context::getContext();
        $attributes='';
        $attributes_small='';
        $id_lang = $context->language->id;
        $result = Db::getInstance()->executeS('
			SELECT pac.`id_product_attribute`, agl.`public_name` AS public_group_name, al.`name` AS attribute_name
			FROM `'._DB_PREFIX_.'product_attribute_combination` pac
			LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
			LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (
				a.`id_attribute` = al.`id_attribute`
				AND al.`id_lang` = '.(int)$id_lang.'
			)
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (
				ag.`id_attribute_group` = agl.`id_attribute_group`
				AND agl.`id_lang` = '.(int)$id_lang.'
			)
			WHERE pac.`id_product_attribute` = '.(int)$id_product_attribute.'
			ORDER BY ag.`position` ASC, a.`position` ASC'
        );
        foreach ($result as $row) {
            $attributes .= $row['public_group_name'].' : '.$row['attribute_name'].', ';
            $attributes_small .= $row['attribute_name'].', ';
        }
        return array(
            'attributes'=>trim($attributes,', '),
            'attributes_small'=>trim($attributes_small,', '),
        );
    }
}