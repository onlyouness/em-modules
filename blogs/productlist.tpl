{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

{capture assign="productClasses"}{if !empty($productClass)}{$productClass}{else}col-xs-12 col-sm-6 col-md-4 col-xl-3 px-0{/if}{/capture}

{assign var="lang_iso" value=$language.iso_code}
{assign var="id_customer" value=$customer.id}
    {if !is_null($id_customer)}
        {assign var="id_lang" value=$language.id}
        {assign var="lang_iso" value=$language.iso_code}
        {assign var="address" value=Address::getFirstCustomerAddressId($id_customer)}
        {assign var="group_id" value=$customer.id_default_group}
        {assign var="countryId" value=Context::getContext()->cookie->selected_country}
        {assign var="country" value=Country::getNameById($id_lang, $countryId)}
        {assign var="groupCondition" value=""}
        {if $group_id == 4}
            {assign var="groupCondition" value="cg.id_default_group = 4"}
        {/if}
        {if $group_id == 5}
            {assign var="groupCondition" value="cg.id_default_group = 4 OR cg.id_default_group = 5"}
        {/if}
        {if $group_id == 3 || $group_id == 6}
            {assign var="groupCondition" value="cg.id_default_group = 5"}
            {assign var="continent_query" value="SELECT c.continent
                FROM `country` c
                INNER JOIN `country_translation` ct ON ct.country_id = c.id
                WHERE ct.name = '"|cat:$country|cat:"'"
                }
            {assign var="seller_continent" value=Db::getInstance()->getValue($continent_query)}
            {assign var="countries_query" value="SELECT ct.name
                FROM `country` c
                INNER JOIN `country_translation` ct ON ct.country_id = c.id
                WHERE c.continent = '"|cat:$seller_continent|cat:"'
               "}
                {assign var="continent_countries" value=Db::getInstance()->executeS($countries_query)}
                {assign var="country_list" value=""}
                {foreach from=$continent_countries item=continent_country}
                    {if $country_list != ""}
                        {assign var="country_list" value=$country_list|cat:","}
                    {/if}
                    {assign var="country_list" value=$country_list|cat:"'"|cat:$continent_country.name|cat:"'"}
                {/foreach}
                {* Fallback if country_list is empty *}
                {if $country_list == ""}
                    {assign var="country_condition" value=""}
                {else}
                    {assign var="country_condition" value="AND cl.name IN ("|cat:$country_list|cat:")"}
                {/if}
            {assign var="query" value="SELECT sp.id_product
                FROM `ps_seller_product` sp
                INNER JOIN `ps_seller` s ON s.id_seller = sp.id_seller
                INNER JOIN `ps_customer` cg ON cg.id_customer = s.id_customer
                INNER JOIN `ps_country_lang` cl ON cl.name = s.country
                WHERE ("|cat:$groupCondition|cat:")
                    "|cat:$country_condition|cat:"
                "
            }
        {else}
            {assign var="query" value="SELECT sp.id_product
                FROM `ps_seller_product` sp
                INNER JOIN `ps_seller` s ON s.id_seller = sp.id_seller
                INNER JOIN `ps_customer` cg ON cg.id_customer = s.id_customer
                WHERE {$groupCondition}"}
        {/if}
            {assign var="productIds" value=Db::getInstance()->executeS($query)}
            {assign var="productIdList" value=[]}
            {foreach from=$productIds item="productItem"}
                {$productIdList[] = $productItem.id_product}
            {/foreach}
    {/if}

{assign var="empty" value="0"}
    {dump($category.id)}
    <div class="products{if !empty($cssClass)} {$cssClass}{/if}">
      {foreach from=$products item="product" key="position"}
        {if ($category.id !== 2)}
        {if isset($productIdList) && $product.id_product|in_array:$productIdList}
        {include file="catalog/_partials/miniatures/product.tpl" product=$product position=$position productClasses=$productClasses}
            {assign var="empty" value="1"}
        {/if}
        {else}
            {include file="catalog/_partials/miniatures/product.tpl" product=$product position=$position productClasses=$productClasses}
        {/if}
      {/foreach}
      {if $empty == "0"} 
          {if $lang_iso == "en"}
          
            <p class="no-prod">
                There is no product
            </p>
          {else}
            <p class="no-prod">
                Aucun produit
            </p>   
          
          {/if}
      
      {/if}
    </div>
    
