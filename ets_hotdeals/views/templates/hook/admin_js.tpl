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
<div id="productlist" style="display:none;">
    <ul>
        {if $products}
            {foreach from=$products item='product'}
                <li>
                    <img src="{$product.url_image|escape:'html':'UTF-8'}"/>
                    <span class="product-search-info">
                        <span class="product-name">{$product.name|escape:'html':'UTF-8'}</span>
                        {if $product.attributes!='Null'}
                            <span class="attribute-name"> {$product.attributes|escape:'html':'UTF-8'} </span>
                        {/if}
                        {if $product.reference}
                            ({l s='Ref: ' mod='ets_hotdeals'}{$product.reference|escape:'html':'UTF-8'})
                        {/if}
                    </span>
                    <span class="delete-product-id" data-id="{$product.id_product|intval}-{$product.id_product_attribute|intval}">{l s='Delete' mod='ets_hotdeals'}</span>
                </li>
            {/foreach}
        {/if}
    </ul>
</div>
<script type="text/javascript" src="{$ets_hotdeals_module_dir|escape:'html':'UTF-8'}views/js/admin.js"></script>
<link rel="stylesheet" href="{$ets_hotdeals_module_dir|escape:'html':'UTF-8'}views/css/admin.css" type="text/css" media="all"/>