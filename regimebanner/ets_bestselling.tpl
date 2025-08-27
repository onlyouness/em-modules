{*
* 2007-2022 ETS-Soft
*
* NOTICE OF LICENSE
*
* This file is not open source! Each license that you purchased is only available for 1 wesite only.
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
*  @copyright  2007-2022 ETS-Soft
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
        {assign var="groupCondition" value=""}
        {if $group_id == 4}
            {assign var="groupCondition" value="cg.id_default_group = 4"}
        {/if}
        {if $group_id == 5}
            {assign var="groupCondition" value="cg.id_default_group = 4 OR cg.id_default_group = 5"}
        {/if}
        {if $group_id == 3}
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

<section class="bestselling_product_list_section clearfix up_mb-0">
    <div class="container">
        <h2 class="h2 products-section-title">
            {$block_title|escape:'html':'UTF-8'} 
            <hr>
        </h2>
        {if $group_id|in_array:[4, 5, 6]}
            <div class="swiper myProductBestSellers">
                <div class="swiper-wrapper products">
                    {foreach from=$products item="product"}
                            <div class="swiper-slide">
                                {include file="catalog/_partials/miniatures/product.tpl" product=$product}
                            </div>
                    {/foreach}
                </div>
            </div>
            <div class="swiper-bestsel-next">
                <svg width="10" height="14" viewBox="0 0 10 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 1.10168L2.4 6.70168L8 12.3017" stroke="white" stroke-width="2.2"
                        sbestselling_product_list_section troke-linecap="round">
                    </path>
                </svg>
            </div>
            <div class="swiper-bestsel-prev">
                <svg width="10" height="14" viewBox="0 0 10 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 1.10168L7.6 6.70168L2 12.3017" stroke="white" stroke-width="2.2" stroke-linecap="round">
                    </path>
                </svg>
            </div>
        {else if $group_id|in_array:[1, 2, 3]}

            <div class="myProductBestSellersWit">
                <div class="products">
                    {foreach from=$products item="product"}
                        {include file="catalog/_partials/miniatures/product.tpl" product=$product}
                    {/foreach}
                </div>
            </div>
        {/if}
    </div>
</section>