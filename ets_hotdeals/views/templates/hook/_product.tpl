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
 
{block name='product_miniature_item'}
  <article class="product-miniature js-product-miniature{if $key%$ets_hotdeals_product_per_row_desktop == 0} desktop_first_line{/if}{if $key%$ets_hotdeals_product_per_row_tablet == 0} tablet_first_line{/if}{if $key%$ets_hotdeals_product_per_row_tabletlarge == 0} tableth_first_line{/if}{if $key%$ets_hotdeals_product_per_row_mobile == 0} mobile_first_line{/if}{if isset($ets_hotdeals_display_type) && $ets_hotdeals_display_type} col-lg-{$ets_item_desktop|escape:'html':'UTF-8'} col-md-{$ets_item_tabletlarge|escape:'html':'UTF-8'} col-sm-{$ets_item_tablet|escape:'html':'UTF-8'} col-xs-{$ets_item_mobile|escape:'html':'UTF-8'}{/if}" data-id-product="{$product.id_product|intval}" data-id-product-attribute="{$product.id_product_attribute|intval}" itemscope itemtype="http://schema.org/Product">
    <div class="thumbnail-container">
      {block name='product_thumbnail'}
        <div class="product_special_img thumbnail-top">
            <a href="{if isset($product.link)}{$product.link|escape:'html':'UTF-8'}{else}{$product.url|escape:'html':'UTF-8'}{/if}" class="thumbnail product-thumbnail">
                <img src ="{$product.link_image|escape:'html':'UTF-8'}" alt = "{$product.cover.legend|escape:'html':'UTF-8'}" data-full-size-image-url = "{$product.cover.large.url|escape:'html':'UTF-8'}" />
            </a>
            {*
            {if $product.has_discount && $ets_hotdeals_display_discounted_amount}
                  <span class="discount-percentage">{if $product.discount_type=='amount'}-{$product.discount_amount|escape:'html':'UTF-8'}{else}{$product.discount_percentage|escape:'html':'UTF-8'}{/if}</span>
            {/if}*}
            {block name='product_flags'}
              <ul class="product-flags">
                {foreach from=$product.flags item=flag}
                    {if $flag.type == 'discount' && !$ets_hotdeals_display_discounted_amount}
                        {continue}
                    {/if}
                  <li class="product-flag {$flag.type|escape:'html':'UTF-8'}">{$flag.label|escape:'html':'UTF-8'}</li>
                {/foreach}
              </ul>
            {/block}
            <div class="highlighted-informations{if !$product.main_variants} no-variants{/if} hidden-sm-down">
                {block name='quick_view'}
                    <a class="quick-view" href="#" data-link-action="quickview">
                        <i class="material-icons search">&#xE8B6;</i> {l s='Quick view' mod='ets_hotdeals'}
                    </a>
                {/block}

                {block name='product_variants'}
                    {if $product.main_variants}
                        {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
                    {/if}
                {/block}
            </div>
        </div>
      {/block}
      <div class="product-infomation-content">
      <div class="product-description">
        {block name='product_name'}
          <h3 class="h3 product-title" itemprop="name">
              <a href="{if isset($product.link)}{$product.link|escape:'html':'UTF-8'}{else}{$product.url|escape:'html':'UTF-8'}{/if}">
                {$product.name|truncate:30:'...'|escape:'html':'UTF-8'} 
                {if $ets_hotdeals_display_product_attribute} 
                    {if isset($product.product_attribute.attributes)}
                        <span class="product-title-attribute">
                        {$product.product_attribute.attributes|escape:'html':'UTF-8'}
                        </span>
                    {/if}
                {/if}
              </a>
          </h3>
        {/block}
        {block name='product_price_and_shipping'}
              {if $product.show_price}
                <div class="product-price-and-shipping">
                  {if $product.has_discount}
                    {hook h='displayProductPriceBlock' product=$product type="old_price"}
    
                    <span class="sr-only">{l s='Regular price' mod='ets_hotdeals'}</span>
                    <span class="regular-price">{$product.regular_price|escape:'html':'UTF-8'}</span>
                  {/if}
    
                  {hook h='displayProductPriceBlock' product=$product type="before_price"}
    
                  <span class="sr-only">{l s='Price' mod='ets_hotdeals'}</span>
                  <span itemprop="price" class="price">{$product.price|escape:'html':'UTF-8'}</span>
    
                  {hook h='displayProductPriceBlock' product=$product type='unit_price'}
    
                {hook h='displayProductPriceBlock' product=$product type='weight'}
              </div>
            {/if}
        {/block}
        {block name='product_reviews'}
            {if $ets_hotdeals_display_rating}
                {hook h='displayProductListReviews' product=$product}
            {/if}
        {/block}
        {if $ets_hotdeals_display_description}
              {if isset($product.description_short) && $product.description_short !=''}
                <div class="short_description">{$product.description_short|truncate:90:'...' nofilter}</div>
              {else}
                <div class="short_description">{$product.description|truncate:90:'...' nofilter}</div>
              {/if}
        {/if}
          {if $ets_hotdeals_display_countdown_clock && isset($product.specific_prices.to)&& $product.specific_prices.to!='0000-00-00 00:00:00'}
            
            <div id="ets_clock_{$product.id_product|intval}" data-id-product="{$product.id_product|intval}" data-date-to="{$product.specific_prices.to|escape:'html':'UTF-8'}" class="ets_clock"></div>
          {/if}
      </div>

        </div>
    </div>
  </article>
{/block}
