{*
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
*}
{assign var='i' value=0}
{foreach from=$products_list key='key' item="product"}
    {block name='product_miniature'}
        {assign var='i' value=$i + 1}
    {/block}
{/foreach}
<div class="clearfix"></div>
{if $i > 0}
<div class="ybc_countdown">
    <div id="ets_hotdeals" class="{if isset($ets_hotdeals_display_type) && $ets_hotdeals_display_type} ets_hotdeal_{$ets_hotdeals_display_type|escape:'html':'UTF-8'}{/if}">
        <h3 class="h1 products-section-title text-uppercase">
            {$specific_title|escape:'html':'UTF-8'}
        </h3>
        <ul class="ets-product-specific product_list grid row" data-desktop="{$ets_hotdeals_product_per_row_desktop|escape:'html':'UTF-8'}" data-tablet-horz="{$ets_hotdeals_product_per_row_tabletlarge|escape:'html':'UTF-8'}" data-tablet="{$ets_hotdeals_product_per_row_tablet|escape:'html':'UTF-8'}" data-mobile="{$ets_hotdeals_product_per_row_mobile|escape:'html':'UTF-8'}" data-speed="{$ets_hotdeals_speed|escape:'html':'UTF-8'}" data-play="{$ets_hotdeals_auto_play_slider|escape:'html':'UTF-8'}" data-stop="{$ets_hotdeals_stop_hover|escape:'html':'UTF-8'}" >
            {assign var='ets_item_desktop' value=intval(12/$ets_hotdeals_product_per_row_desktop)}
            {assign var='ets_item_tabletlarge' value=intval(12/$ets_hotdeals_product_per_row_tabletlarge)}
            {assign var='ets_item_tablet' value=intval(12/$ets_hotdeals_product_per_row_tablet)}
            {assign var='ets_item_mobile' value=intval(12/$ets_hotdeals_product_per_row_mobile)}
            
            {foreach from=$products_list key='key' item="product"}
                {block name='product_miniature'}
                    {include file='modules/ets_hotdeals/views/templates/hook/_product_16.tpl' product=$product key=$key}
                {/block}
            {/foreach}
        </ul>
    </div>
</div>
<div class="clearfix"></div>
{/if}