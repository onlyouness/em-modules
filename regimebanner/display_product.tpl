{*
* 2007-2023 ETS-Soft
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
* versions in the future. If you wish to customize PrestaShop for your
* needs, please contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2023 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}

{assign var="lang_iso" value=$language.iso_code}
{assign var="id_customer" value=$customer.id}
{if !is_null($id_customer)}
    {assign var="id_lang" value=$language.id}
    {assign var="lang_iso" value=$language.iso_code}
    {assign var="address" value=Address::getFirstCustomerAddressId($id_customer)}
    {assign var="group_id" value=$customer.id_default_group}
    {assign var="countryId" value=Context::getContext()->cookie->selected_country}
    {assign var="country" value=Country::getNameById($id_lang, $countryId)}
{/if}
{assign var='i' value=0}
{foreach from=$products_list key='key' item="product"}
    {block name='product_miniature'}
        {assign var='i' value=$i + 1}
    {/block}
{/foreach}
<div class="up-prof">
    {if $i > 0}
        <div class="ybc_countdown">
            <h2 class="h2 products-section-title">
                {$specific_title|escape:'html':'UTF-8'}
                <hr>
            </h2>
            <div id="ets_hotdeals" class="container">
                <div class="ets-product-specific products swiper myProductHot {if $ets_hotdeals_product_per_row_desktop > $i} hidden_nav_des{/if}
                {if $ets_hotdeals_product_per_row_tabletlarge > $i} hidden_nav_tablarge{/if}
                {if $ets_hotdeals_product_per_row_tablet > $i} hidden_nav_tablet{/if}
                {if $ets_hotdeals_product_per_row_mobile > $i} hidden_nav_mobile{/if} row"
                    data-desktop="{$ets_hotdeals_product_per_row_desktop|escape:'html':'UTF-8'}"
                    data-tablet-horz="{$ets_hotdeals_product_per_row_tabletlarge|escape:'html':'UTF-8'}"
                    data-tablet="{$ets_hotdeals_product_per_row_tablet|escape:'html':'UTF-8'}"
                    data-mobile="{$ets_hotdeals_product_per_row_mobile|escape:'html':'UTF-8'}"
                    data-speed="{$ets_hotdeals_speed|escape:'html':'UTF-8'}"
                    data-play="{$ets_hotdeals_auto_play_slider|escape:'html':'UTF-8'}"
                    data-stop="{$ets_hotdeals_stop_hover|escape:'html':'UTF-8'}">
                    {assign var='ets_item_desktop' value=intval(12/$ets_hotdeals_product_per_row_desktop)}
                    {assign var='ets_item_tabletlarge' value=intval(12/$ets_hotdeals_product_per_row_tabletlarge)}
                    {assign var='ets_item_tablet' value=intval(12/$ets_hotdeals_product_per_row_tablet)}
                    {assign var='ets_item_mobile' value=intval(12/$ets_hotdeals_product_per_row_mobile)}
                    <div class="swiper-wrapper">
                        {foreach from=$products_list key='key' item="product"}
                                {block name='product_miniature'}
                                    <div class="swiper-slide">
                                        {include file='modules/ets_hotdeals/views/templates/hook/_product.tpl' product=$product key=$key}
                                    </div>
                                {/block}
                        {/foreach}
                    </div>
                </div>

                <div class="swiper-hot-prev">
                    <svg width="10" height="14" viewBox="0 0 10 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 1.10168L7.6 6.70168L2 12.3017" stroke="white" stroke-width="2.2" stroke-linecap="round">
                        </path>
                    </svg>
                    
                </div>
                <div class="swiper-hot-next">
                    <svg width="10" height="14" viewBox="0 0 10 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 1.10168L2.4 6.70168L8 12.3017" stroke="white" stroke-width="2.2" stroke-linecap="round">
                        </path>
                    </svg>
                </div>

            </div>
        </div>
    {/if}

    {if Module::isEnabled('four_banner_taef', 1) && Module::isEnabled('four_banner_taef', 2) && Module::isEnabled('four_banner_taef', 3)}
        {if $customer.is_logged}
            {if $customer.id_default_group != 4 && $customer.id_default_group != 5 && $customer.id_default_group != 6}
                <div class="banner_teaf_home four_banner">
                    <div class="container up-brn">
                        <h2 class="h2 products-section-title">
                            Régimes particuliers
                            <hr>
                        </h2>
                        <div class="make-bbrn">


                            {hook h="displayFourBannerFirst"}

                            {hook h="displayFourBannerSecond"}

                            {hook h="displayFourBannerThird"}

                            {hook h="displayFourBannerFourth"}
                        </div>
                    </div>
                </div>
            {/if}
        {/if}

    {/if}


</div>