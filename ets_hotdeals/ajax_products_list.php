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

//if (!defined('_PS_VERSION_')) { exit; }
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
/* Getting cookie or logout */
$query = trim(Tools::getValue('q', false));
if (!$query OR $query == '' OR Tools::strlen($query) < 1)
	die();
/*
 * In the SQL request the "q" param is used entirely to match result in database.
 * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list, 
 * they are no return values just because string:"(ref : #ref_pattern#)" 
 * is not write in the name field of the product.
 * So the ref pattern will be cut for the search request.
 */
if($pos = strpos($query, ' (ref:'))
	$query = Tools::substr($query, 0, $pos);

$excludeIds = Tools::getValue('excludeIds', false);
if ($excludeIds && $excludeIds != 'NaN')
	$excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
else
	$excludeIds = '';

// Excluding downloadable products from packs because download from pack is not supported
$excludeVirtuals = (bool)Tools::getValue('excludeVirtuals', false);
$exclude_packs = (bool)Tools::getValue('exclude_packs', false);
$hotdeals = Module::getInstanceByName('ets_hotdeals');
$sql = 'SELECT p.`id_product`, p.`reference`, pl.name,pl.link_rewrite,pa.id_product_attribute
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = '.(int)Context::getContext()->language->id.Shop::addSqlRestrictionOnLang('pl').' AND pl.id_shop='.(int)Context::getContext()->shop->id.')
        LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.id_product= p.id_product)
		WHERE p.id_product='.(int)$query.' OR (pl.name LIKE \'%'.pSQL($query).'%\' OR p.reference LIKE \'%'.pSQL($query).'%\')'.
		(!empty($excludeIds) ? ' AND p.id_product NOT IN ('.$excludeIds.') ' : ' ').
		($excludeVirtuals ? 'AND p.id_product NOT IN (SELECT pd.id_product FROM `'._DB_PREFIX_.'product_download` pd WHERE (pd.id_product = p.id_product))' : '').
		($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '').' LIMIT 20';

$items = Db::getInstance()->executeS($sql);

if ($items)
	foreach ($items AS $item)
    {
        $id_image = null;
        if($item['id_product_attribute'])
        {
             $image=Product::getCombinationImageById($item['id_product_attribute'],Context::getContext()->language->id);
             $id_image=$image ? $image['id_image'] : null;
        }
        if(!isset($id_image)|| !$id_image) {
            $id_image = Db::getInstance()->getValue("SELECT id_image FROM " . _DB_PREFIX_ . "image WHERE id_product=" . (int)$item['id_product'] . ' AND cover=1');
        }

		$type_image= ImageType::getFormattedName('small');
        if($id_image)
            $url_image = Context::getContext()->link->getImageLink($item['link_rewrite'],$id_image,$type_image);
        else
            $url_image = '';
        if($item['id_product_attribute'])
            $attributes = ($attrHotDeal = $hotdeals->getAllAttributes($item['id_product_attribute'])) ? $attrHotDeal['attributes'] : 'Null';
        else
            $attributes='Null';
        echo trim($item['name']).'|'.(int)($item['id_product']).'|'.$url_image.'|'.$attributes.'|'.$item['id_product_attribute'].'|'.$item['reference']."\n";
    }