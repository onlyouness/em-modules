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
  <li class="ajax_block_product product-miniature js-product-miniature{if $key%$ets_hotdeals_product_per_row_desktop == 0} desktop_first_line{/if}{if $key%$ets_hotdeals_product_per_row_tablet == 0} tablet_first_line{/if}{if $key%$ets_hotdeals_product_per_row_tabletlarge == 0} tableth_first_line{/if}{if $key%$ets_hotdeals_product_per_row_mobile == 0} mobile_first_line{/if}{if isset($ets_hotdeals_display_type) && $ets_hotdeals_display_type} col-lg-{$ets_item_desktop|escape:'html':'UTF-8'} col-md-{$ets_item_tabletlarge|escape:'html':'UTF-8'} col-sm-{$ets_item_tablet|escape:'html':'UTF-8'} col-xs-{$ets_item_mobile|escape:'html':'UTF-8'}{/if}" data-id-product="{$product.id_product|escape:'html':'UTF-8'}" data-id-product-attribute="{$product.id_product_attribute|escape:'html':'UTF-8'}" itemscope itemtype="http://schema.org/Product">
    <div class="product-container" itemscope itemtype="https://schema.org/Product">
		<div class="left-block">
			<div class="product-image-container">
				<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
					<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width|escape:'html':'UTF-8'}" height="{$homeSize.height|escape:'html':'UTF-8'}"{/if} itemprop="image" />
				</a>
				{if isset($quick_view) && $quick_view}
    				<div class="quick-view-wrapper-mobile">
    					<a class="quick-view-mobile" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}">
    						<i class="icon-eye-open"></i>
    					</a>
    				</div>
    				<a class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}">
    					<span>{l s='Quick view' mod='ets_hotdeals'}</span>
    				</a>
				{/if}
				{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					<div class="content_price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							<span itemprop="price" class="price product-price">
								{hook h="displayProductPriceBlock" product=$product type="before_price"}
								{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
							</span>
							<meta itemprop="priceCurrency" content="{$currency->iso_code|escape:'html':'UTF-8'}" />
							{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$product type="old_price"}
								<span class="old-price product-price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>
							{/if}
                            {if isset($product.specific_prices)&& $product.specific_prices && $ets_hotdeals_display_discounted_amount}
                                  <span class="discount-percentage price-percent-reduction">{if $product.specific_prices.reduction_type=='amount'}-{displayWtPrice p=$product.specific_prices.reduction|floatval}{else}-{$product.specific_prices.reduction*100|floatval}%{/if}</span>
                            {/if}
							{if $PS_STOCK_MANAGEMENT && isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
								<span class="unvisible">
									{if ($product.allow_oosp || $product.quantity > 0)}
											<link itemprop="availability" href="https://schema.org/InStock" />{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='ets_hotdeals'}{/if}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='ets_hotdeals'}{/if}{/if}
									{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
											<link itemprop="availability" href="https://schema.org/LimitedAvailability" />{l s='Product available with different options' mod='ets_hotdeals'}

									{else}
											<link itemprop="availability" href="https://schema.org/OutOfStock" />{l s='Out of stock' mod='ets_hotdeals'}
									{/if}
								</span>
							{/if}
							{hook h="displayProductPriceBlock" product=$product type="price"}
							{hook h="displayProductPriceBlock" product=$product type="unit_price"}
						{/if}
					</div>
				{/if}
				{if isset($product.new) && $product.new == 1}
					<a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
						<span class="new-label">{l s='New' mod='ets_hotdeals'}</span>
					</a>
				{/if}
				{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
					<a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
						<span class="sale-label">{l s='Sale!' mod='ets_hotdeals'}</span>
					</a>
				{/if}
			</div>
			{if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
			{hook h="displayProductPriceBlock" product=$product type="weight"}
		</div>
		<div class="right-block">
			<h5 itemprop="name">
				{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
				<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
					{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
                    {if $ets_hotdeals_display_product_attribute} 
                        {if isset($product.product_attribute.attributes)}
                            <span class="product-title-attribute">
                            {$product.product_attribute.attributes|escape:'html':'UTF-8'}
                            </span>
                        {/if}
                    {/if}
				</a>
			</h5>
			{capture name='displayProductListReviews'}{hook h='displayProductListReviews' product=$product}{/capture}
			{if $smarty.capture.displayProductListReviews && $ets_hotdeals_display_rating}
				<div class="hook-reviews">
				{hook h='displayProductListReviews' product=$product}
				</div>
			{/if}
            {if $ets_hotdeals_display_description} 
				{if isset($product.description_short) && $product.description_short !=''}
                    <div class="short_description">{$product.description_short|truncate:90:'...' nofilter}</div>
                {else}
                    <div class="short_description">{if isset($product.description)}{$product.description|truncate:90:'...' nofilter}{/if}</div>
                {/if}
            {/if}
			{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
			<div class="content_price">
				{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
					{hook h="displayProductPriceBlock" product=$product type='before_price'}
					<span class="price product-price">
						{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
					</span>
					{if $product.price_without_reduction > 0 && isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
						{hook h="displayProductPriceBlock" product=$product type="old_price"}
						<span class="old-price product-price">
							{displayWtPrice p=$product.price_without_reduction}
						</span>
						{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
						{if isset($product.specific_prices)&& $product.specific_prices && $ets_hotdeals_display_discounted_amount}
                              <span class="discount-percentage price-percent-reduction">{if $product.specific_prices.reduction_type=='amount'}-{displayWtPrice p=$product.specific_prices.reduction|floatval}{else}-{$product.specific_prices.reduction*100|floatval}%{/if}</span>
                        {/if}
					{/if}
					{hook h="displayProductPriceBlock" product=$product type="price"}
					{hook h="displayProductPriceBlock" product=$product type="unit_price"}
					{hook h="displayProductPriceBlock" product=$product type='after_price'}
				{/if}
			</div>
			{/if}
			<div class="button-container">
				{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
					{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
						{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($product.id_product_attribute) && $product.id_product_attribute}&amp;ipa={$product.id_product_attribute|intval}{/if}{if isset($static_token)}&amp;token={$static_token|escape:'html':'UTF-8'}{/if}{/capture}
						<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='ets_hotdeals'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
							<span>{l s='Add to cart' mod='ets_hotdeals'}</span>
						</a>
					{else}
						<span class="button ajax_add_to_cart_button btn btn-default disabled">
							<span>{l s='Add to cart' mod='ets_hotdeals'}</span>
						</span>
					{/if}
				{/if}
				<a class="button lnk_view btn btn-default" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View' mod='ets_hotdeals'}">
					<span>{if (isset($product.customization_required) && $product.customization_required)}{l s='Customize' mod='ets_hotdeals'}{else}{l s='More' mod='ets_hotdeals'}{/if}</span>
				</a>
			</div>
			{if isset($product.color_list)}
				<div class="color-list-container">{$product.color_list|escape:'html':'UTF-8'}</div>
			{/if}
			<div class="product-flags">
				{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					{if isset($product.online_only) && $product.online_only}
						<span class="online_only">{l s='Online only' mod='ets_hotdeals'}</span>
					{/if}
				{/if}
				{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
					{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
						<span class="discount">{l s='Reduced price!' mod='ets_hotdeals'}</span>
					{/if}
			</div>
			{if (!$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
				{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
					<span class="availability">
						{if ($product.allow_oosp || $product.quantity > 0)}
							<span class="{if $product.quantity <= 0 && isset($product.allow_oosp) && !$product.allow_oosp} label-danger{elseif $product.quantity <= 0} label-warning{else} label-success{/if}">
								{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='ets_hotdeals'}{/if}{else}{l s='Out of stock' mod='ets_hotdeals'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now|escape:'html':'UTF-8'}{else}{l s='In Stock' mod='ets_hotdeals'}{/if}{/if}
							</span>
						{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
							<span class="label-warning">
								{l s='Product available with different options' mod='ets_hotdeals'}
							</span>
						{else}
							<span class="label-danger">
								{l s='Out of stock' mod='ets_hotdeals'}
							</span>
						{/if}
					</span>
				{/if}
			{/if}
		</div>
		{if $page_name != 'index'}
			<div class="functional-buttons clearfix">
				{hook h='displayProductListFunctionalButtons' product=$product}
				{if isset($comparator_max_item) && $comparator_max_item}
					<div class="compare">
						<a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product|escape:'html':'UTF-8'}">{l s='Add to Compare' mod='ets_hotdeals'}</a>
					</div>
				{/if}
			</div>
		{/if}
        {if $ets_hotdeals_display_countdown_clock && isset($product.specific_prices.to)&& $product.specific_prices.to!='0000-00-00 00:00:00'}
            <div id="ets_clock_{$product.id_product|intval}" data-id-product="{$product.id_product|intval}" data-date-to="{$product.specific_prices.to|escape:'html':'UTF-8'}" class="ets_clock"></div>
        {/if}
	</div><!-- .product-container> -->
  </li>