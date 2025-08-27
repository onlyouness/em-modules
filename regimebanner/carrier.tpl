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
 * @author    JA Modules <info@jamodules.com>
 * @copyright Since 2007 JA Modules
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}

<div id="carrier_wizard" class="jmarketplace-panel">
    <div class="jmarketplace-panel-header">
        {if $action == 'add'}
            <h2>{l s='Add carrier' mod='jmarketplace'}</h2>
        {else}
            <h2>{l s='Edit carrier' mod='jmarketplace'} "{$carrier->name|escape:'html':'UTF-8'}"</h2>
        {/if}
    </div>
    <form action="{$form_action|escape:'html':'UTF-8'}" method="post" class="std" enctype="multipart/form-data">
        <div class="form-group">
            <label for="carrier_name">{l s='Carrier name' mod='jmarketplace'} <span class="required">*</span></label>
            <input class="form-control" type="text" name="carrier_name" {if isset($carrier) AND $carrier}
                value="{$carrier->name|escape:'html':'UTF-8'}" {else}placeholder="{l s='My carrier' mod='jmarketplace'}"
                {/if} required />
            <p class="help-block">
                {l s='Allowed characters: letters, spaces and ().-. The carrier name will be displayed during checkout. For in-store pickup, enter 0 to replace the carrier name with your shop name.' mod='jmarketplace'}
            </p>
        </div>
        {if $JMARKETPLACE_SELLERCARRIER_DELAY == 1}
            <div class="row">
                <div class="required form-group col-sm-10 col-xs-9">
                    <label for="delay_lang" class="required">{l s='Transit time' mod='jmarketplace'}</label>
                    {foreach from=$languages item=language}
                        <input{if $id_lang != $language.id_lang} style="display:none;" {/if}
                            class="is_required validate form-control delay" type="text" id="delay_{$language.id_lang|intval}"
                            name="delay_{$language.id_lang|intval}" {if isset($carrier) AND $carrier} value="{$carrier->delay[{$language.id_lang|intval}]|escape:'html':'UTF-8'}" {else} value="{l s='72h' mod='jmarketplace'}" 
                            {/if} required />

                    {/foreach}
                    <p class="help-block">
                        {l s='The estimated delivery time will be displayed during checkout.' mod='jmarketplace'}</p>
                </div>
                <div class="col-sm-2 col-xs-3">
                    <label for="id_lang">{l s='Language' mod='jmarketplace'}</label>
                    <select name="id_lang" class="form-control delay_lang">
                        {foreach from=$languages item=language}
                            <option value="{$language.id_lang|intval}" {if $id_lang == $language.id_lang} selected="selected"
                                {/if}>{$language.iso_code|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {else}
            {foreach from=$languages item=language}
                <input type="hidden" name="delay_{$language.id_lang|intval}" value="-" />
            {/foreach}
        {/if}
        {if $JMARKETPLACE_SELLERCARRIER_GRADE == 1}
            <div class="form-group">
                <label for="grade">{l s='Speed grade' mod='jmarketplace'}</label>
                <input class="form-control" type="text" name="grade" {if isset($carrier) AND $carrier}
                    value="{$carrier->grade|intval}" {else}placeholder="0" 
                    {/if} />
                <p class="help-block">
                    {l s='Enter 0 for a longest shipping delay, or 9 for the shortest shipping delay.' mod='jmarketplace'}
                </p>
            </div>
        {/if}
        {if $JMARKETPLACE_SELLERCARRIER_LOGO == 1}
            <div class="form-group">
                <label for="logo">{l s='Logo' mod='jmarketplace'}</label>
                <input type="file" name="logo" class="filestyle" data-buttonText="{l s='Choose file' mod='jmarketplace'}">
                {if $carrier_logo != false}
                    <p class="help-block"><img class="img-fluid" src="{$carrier_logo|escape:'html':'UTF-8'}" width="65"
                            height="65" /></p>
                {/if}
                <p class="help-block">
                    {l s='Format JPG, GIF, PNG. Filesize 2.00 MB max. Current size undefined.' mod='jmarketplace'}</p>
            </div>
        {/if}
        {if $JMARKETPLACE_SELLERCARRIER_TRACKING == 1}
            <div class="form-group">
                <label for="url">{l s='Tracking URL' mod='jmarketplace'}</label>
                <input class="form-control" type="text" name="url" {if isset($carrier) AND $carrier}
                    value="{$carrier->url|escape:'html':'UTF-8'}" {/if} />
                <p class="help-block">
                    {l s='For example: http://exampl.com/track.php?num=@ with @ where the tracking number should appear.' mod='jmarketplace'}
                </p>
            </div>
        {/if}
        <div class="form-group" style="display:none;">
            <label class="control-label col-lg-3">{l s='Add handling costs' mod='jmarketplace'}</label>
            <div class="col-lg-9 ">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" value="1" id="shipping_handling_on" name="shipping_handling">
                    <label for="shipping_handling_on">{l s='Yes' mod='jmarketplace'}</label>
                    <input type="radio" checked="checked" value="0" id="shipping_handling_off" name="shipping_handling">
                    <label for="shipping_handling_off">{l s='No' mod='jmarketplace'}</label>
                    <a class="slide-button btn"></a>
                </span>
                <p class="help-block">{l s='Include the handling costs in the final carrier price.' mod='jmarketplace'}
                </p>
            </div>
        </div>
        <div class="row">
            {if $JMARKETPLACE_SELLERCARRIER_FREE == 1}
                <div class="form-group col-lg-4">
                    <label for="is_free">{l s='Free shipping' mod='jmarketplace'}</label>
                    <select id="is_free" name="is_free" class="form-control">
                        <option value="0" {if isset($carrier) AND $carrier} 
                            {if $carrier->is_free == 0} selected="selected"
                                {/if} 
                            {/if}>
                            {l s='No' mod='jmarketplace'}</option>
                        <option value="1" {if isset($carrier) AND $carrier} 
                            {if $carrier->is_free == 1} selected="selected"
                                {/if} 
                            {/if}>
                            {l s='Yes' mod='jmarketplace'}</option>
                    </select>
                </div>
            {else}
                <input type="hidden" name="is_free" value="0" />
            {/if}
            {if $JMARKETPLACE_SELLERCARRIER_SHIPPING_METHOD == 1}
                <div class="form-group col-lg-4">
                    <label for="shipping_method">{l s='Billing' mod='jmarketplace'}</label>
                    <select id="shipping_method" name="shipping_method" class="form-control">
                        <option value="1" {if isset($carrier) AND $carrier} 
                            {if $carrier->shipping_method == 1}
                                selected="selected" {/if} 
                            {/if}>
                            {l s='According to total weight.' mod='jmarketplace'}</option>
                        <option value="2" {if isset($carrier) AND $carrier} 
                            {if $carrier->shipping_method == 2}
                                selected="selected" {/if} 
                            {/if}>
                            {l s='According to total price.' mod='jmarketplace'}</option>
                    </select>
                </div>
            {else}
                <input type="hidden" name="shipping_method" value="1" />
            {/if}
            {if $JMARKETPLACE_SELLERCARRIER_TAX == 1}
                <div class="form-group col-lg-4">
                    <label for="id_tax_rules_group">{l s='Tax' mod='jmarketplace'}</label>
                    <select id="id_tax_rules_group" name="id_tax_rules_group" class="form-control">
                        <option value="0">{l s='no tax' mod='jmarketplace'}</option>
                        {foreach from=$taxes item=tax}
                            <option value="{$tax.id_tax_rules_group|intval}" {if $id_tax_rules_group == $tax.id_tax_rules_group}
                                selected="selected" {/if}>{$tax.name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    </select>
                </div>
            {else}
                <input type="hidden" name="id_tax_rules_group" value="0" />
            {/if}
        </div>
      
        <div id="zone_ranges" class="jmarketplace-panel">
            <div class="jmarketplace-panel-header">
                <h2>{l s='Ranges' mod='jmarketplace'}</h2>
            </div>
            <div class="table-responsive">
                {if $action == 'add'}
                    <table style="max-width:100%" class="table" id="zones_table">
                        <tbody>
                            <tr class="range_inf">
                                <td class="range_type">{l s='Will be applied when the weight is' mod='jmarketplace'}</td>
                                <td class="border_left border_bottom range_sign">&gt;=</td>
                                <td class="border_bottom">
                                    <div class="input-group fixed-width-md">
                                        <span class="input-group-addon weight_unit">kg</span>
                                        <span class="input-group-addon price_unit"
                                            style="display: none;">{$currency_sign|escape:'html':'UTF-8'}</span>
                                        <input type="text" value="0.000000" name="range_inf[]" class="form-control">
                                    </div>
                                </td>
                            </tr>
                            <tr class="range_sup">
                                <td class="range_type">{l s='Will be applied when the weight is' mod='jmarketplace'}</td>
                                <td class="border_left range_sign">&lt;</td>
                                <td class="range_data">
                                    <div class="input-group fixed-width-md">
                                        <span class="input-group-addon weight_unit">kg</span>
                                        <span class="input-group-addon price_unit"
                                            style="display: none;">{$currency_sign|escape:'html':'UTF-8'}</span>
                                        <input type="text" autocomplete="off" value="" name="range_sup[]"
                                            class="form-control">
                                    </div>
                                </td>
                            </tr>
                            <tr class="fees_all" style="display:none;">
                                <td class="border_top border_bottom border_bold">
                                    <span class="fees_all">{l s='All' mod='jmarketplace'}</span>
                                </td>
                                <td><input type="checkbox" class="form-control" onclick="checkAllZones(this);"></td>
                                <td class="border_top border_bottom ">
                                    <div class="input-group fixed-width-md">
                                        <span style="display:none"
                                            class="input-group-addon currency_sign">{$currency_sign|escape:'html':'UTF-8'}</span>
                                        <input type="text" autocomplete="off" style="display:none" disabled="disabled"
                                            class="form-control">
                                    </div>
                                </td>
                            </tr>

                            {foreach from=$zones item=zone}
                                <tr data-zoneid="{$zone.id_zone|intval}" class="fees">
                                    <td>
                                        <label for="zone_{$zone.id_zone|intval}">{$zone.name|escape:'html':'UTF-8'}</label>
                                    </td>
                                    <td class="zone">
                                        <input type="checkbox" value="{$zone.id_zone|intval}" name="zone_{$zone.id_zone|intval}"
                                            id="zone_{$zone.id_zone|intval}" class="input_zone" checked="checked">
                                    </td>
                                    <td>
                                        <div class="input-group fixed-width-md">
                                            <span class="input-group-addon">{$currency_sign|escape:'html':'UTF-8'}</span>
                                            <input type="text" value="" name="fees[{$zone.id_zone|intval}][0]"
                                                class="form-control">
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                            <tr class="delete_range">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                {else}
                    <table style="max-width:100%" class="table" id="zones_table">
                        <tbody>
                            <tr class="range_inf">
                                <td class="range_type">
                                    {if isset($carrier) AND $carrier}
                                        {if $carrier->shipping_method == 1}
                                            {l s='Will be applied when the weight is' mod='jmarketplace'}
                                        {else}
                                            {l s='Will be applied when the price is' mod='jmarketplace'}
                                        {/if}
                                    {else}
                                        {l s='Will be applied when the weight is' mod='jmarketplace'}
                                    {/if}
                                </td>
                                <td class="border_left border_bottom range_sign">&gt;=</td>
                                {foreach from=$tpl_vars.ranges key=r item=range}
                                    <td class="border_bottom">
                                        <div class="input-group fixed-width-md">
                                            <span class="input-group-addon weight_unit" {if $carrier->shipping_method == 2}
                                                style="display: none;" {/if}>{$PS_WEIGHT_UNIT|escape:'html':'UTF-8'}</span>
                                            <span class="input-group-addon price_unit" {if $carrier->shipping_method == 1}
                                                style="display: none;" {/if}>{$currency_sign|escape:'html':'UTF-8'}</span>
                                            <input class="form-control" name="range_inf[{$range.id_range|intval}]" type="text"
                                                value="{$range.delimiter1|string_format:"%.6f"}" />
                                        </div>
                                    </td>
                                {foreachelse}
                                    <td class="border_bottom">
                                        <div class="input-group fixed-width-md">
                                            <span class="input-group-addon weight_unit" {if $carrier->shipping_method == 2}
                                                style="display: none;" {/if}>{$PS_WEIGHT_UNIT|escape:'html':'UTF-8'}</span>
                                            <span class="input-group-addon price_unit" {if $carrier->shipping_method == 1}
                                                style="display: none;" {/if}>{$currency_sign|escape:'html':'UTF-8'}</span>
                                            <input class="form-control" name="range_inf[{$range.id_range|intval}]"
                                                type="text" />
                                        </div>
                                    </td>
                                {/foreach}
                            </tr>
                            <tr class="range_sup">
                                <td class="range_type">
                                    {if isset($carrier) AND $carrier}
                                        {if $carrier->shipping_method == 1}
                                            {l s='Will be applied when the weight is' mod='jmarketplace'}
                                        {else}
                                            {l s='Will be applied when the price is' mod='jmarketplace'}
                                        {/if}
                                    {else}
                                        {l s='Will be applied when the weight is' mod='jmarketplace'}
                                    {/if}
                                </td>
                                <td class="border_left range_sign">&lt;</td>
                                {foreach from=$tpl_vars.ranges key=r item=range}
                                    <td class="range_data">
                                        <div class="input-group fixed-width-md">
                                            <span class="input-group-addon weight_unit" {if $carrier->shipping_method == 2}
                                                style="display: none;" {/if}>{$PS_WEIGHT_UNIT|escape:'html':'UTF-8'}</span>
                                            <span class="input-group-addon price_unit" {if $carrier->shipping_method == 1}
                                                style="display: none;" {/if}>{$currency_sign|escape:'html':'UTF-8'}</span>
                                            <input class="form-control" name="range_sup[{$range.id_range|intval}]" type="text"
                                                value="{if $range.id_range == 0} {else}{$range.delimiter2|string_format:"%.4f"}{/if}"
                                                autocomplete="off" />
                                        </div>
                                    </td>
                                {foreachelse}
                                    <td class="range_data_new">
                                        <div class="input-group fixed-width-md">
                                            <span class="input-group-addon weight_unit" {if $carrier->shipping_method == 2}
                                                style="display: none;" {/if}>{$PS_WEIGHT_UNIT|escape:'html':'UTF-8'}</span>
                                            <span class="input-group-addon price_unit" {if $carrier->shipping_method == 1}
                                                style="display: none;" {/if}>{$currency_sign|escape:'html':'UTF-8'}</span>
                                            <input class="form-control" name="range_sup[{$range.id_range|intval}]" type="text"
                                                autocomplete="off" />
                                        </div>
                                    </td>
                                {/foreach}
                            </tr>
                            <tr class="fees_all" style="display:none;">
                                <td class="border_top border_bottom border_bold">
                                    <span class="fees_all">{l s='All' mod='jmarketplace'}</span>
                                </td>
                                <td>
                                    <input type="checkbox" class="form-control" onclick="checkAllZones(this);">
                                </td>
                                <td class="border_top border_bottom ">
                                    <div class="input-group fixed-width-md">
                                        <span style="display:none"
                                            class="input-group-addon currency_sign">{$currency_sign|escape:'html':'UTF-8'}</span>
                                        <input type="text" autocomplete="off" style="display:none" disabled="disabled"
                                            class="form-control">
                                    </div>
                                </td>
                            </tr>
                            {foreach from=$zones key=i item=zone}
                                <tr class="fees" data-zoneid="{$zone.id_zone|intval}">
                                    <td>
                                        <label
                                            for="zone_{$zone.id_zone|intval}">{$zone.name|escape:'htmlall':'UTF-8'}{if !$zone.active}
                                            <small>({l s='inactive' mod='jmarketplace'})</small>{/if}</label>
                                    </td>
                                    <td class="zone">
                                        <input class="form-control input_zone" id="zone_{$zone.id_zone|intval}"
                                            name="zone_{$zone.id_zone|intval}" value="1" type="checkbox"
                                            {if isset($fields_value['zones'][$zone.id_zone]) && $fields_value['zones'][$zone.id_zone]}
                                            checked="checked" {/if} />
                                    </td>
                                    {foreach from=$tpl_vars.ranges key=r item=range}
                                        <td>
                                            <div class="input-group fixed-width-md">
                                                <span class="input-group-addon">{$currency_sign|escape:'html':'UTF-8'}</span>
                                                <input class="form-control"
                                                    name="fees[{$zone.id_zone|intval}][{$range.id_range|intval}]" type="text"
                                                    {if !isset($fields_value['zones'][$zone.id_zone]) || (isset($fields_value['zones'][$zone.id_zone]) && !$fields_value['zones'][$zone.id_zone])}
                                                    disabled="disabled" {/if}
                                                    {if isset($tpl_vars.price_by_range) && isset($fields_value['zones'][$zone.id_zone]) && $fields_value['zones'][$zone.id_zone]}
                                                        value="{$tpl_vars.price_by_range[$range.id_range][$zone.id_zone]|floatval|string_format:'%.4f'}"
                                                    {else} value="" 
                                                    {/if} />
                                            </div>
                                        </td>
                                    {/foreach}
                                </tr>
                            {/foreach}
                            <tr class="delete_range">
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                {foreach from=$tpl_vars.ranges name=ranges key=r item=range}
                                    {if $smarty.foreach.ranges.first}
                                        <td>&nbsp;</td>
                                    {else}
                                        <td style="text-align:center;"><button
                                                class="btn btn-list btn-default">{l s='Delete' mod='jmarketplace'}</button></td>
                                    {/if}
                                {/foreach}
                            </tr>
                        </tbody>
                    </table>
                {/if}
            </div>
            <div class="row new-range">
                <div class="col-lg-12">
                    <a class="btn btn-outline-primary" href="#">
                        {if $use_icons == 'fontawesome'}
                            <i class="fa fas fa-plus-circle"></i>
                        {else}
                            <span class="material-icons">add_circle</span>
                        {/if}
                        <span>{l s='Add new range' mod='jmarketplace'}</span>
                    </a>
                </div>
            </div>
        </div>
        {if $JMARKETPLACE_SELLERCARRIER_MAX_WIDTH == 1}
            <div class="form-group">
                <label for="max_width">{l s='Maximum package width (cm)' mod='jmarketplace'}</label>
                <input class="form-control" type="text" name="max_width" {if isset($carrier) AND $carrier}
                    value="{$carrier->max_width|floatval}" {else}placeholder="0" 
                    {/if} />
                <p class="help-block">
                    {l s='Maximum width managed by this carrier. Set the value to "0", or leave this field blank to ignore. The value must be an integer.' mod='jmarketplace'}
                </p>
            </div>
        {/if}
        {if $JMARKETPLACE_SELLERCARRIER_MAX_HEIGHT == 1}
            <div class="form-group">
                <label for="max_height">{l s='Maximum package height (cm)' mod='jmarketplace'}</label>
                <input class="form-control" type="text" name="max_height" {if isset($carrier) AND $carrier}
                    value="{$carrier->max_height|floatval}" {else}placeholder="0" 
                    {/if} />
                <p class="help-block">
                    {l s='Maximum height managed by this carrier. Set the value to "0", or leave this field blank to ignore. The value must be an integer.' mod='jmarketplace'}
                </p>
            </div>
        {/if}
        {if $JMARKETPLACE_SELLERCARRIER_MAX_DEPTH == 1}
            <div class="form-group">
                <label for="max_depth">{l s='Maximum package depth (cm)' mod='jmarketplace'}</label>
                <input class="form-control" type="text" name="max_depth" {if isset($carrier) AND $carrier}
                    value="{$carrier->max_depth|floatval}" {else}placeholder="0" 
                    {/if} />
                <p class="help-block">
                    {l s='Maximum depth managed by this carrier. Set the value to "0", or leave this field blank to ignore. The value must be an integer.' mod='jmarketplace'}
                </p>
            </div>
        {/if}
        {if $JMARKETPLACE_SELLERCARRIER_MAX_WEIGHT == 1}
            <div class="form-group">
                <label for="max_weight">{l s='Maximum package weight (kg)' mod='jmarketplace'}</label>
                <input class="form-control" type="text" name="max_weight" {if isset($carrier) AND $carrier}
                    value="{$carrier->max_weight|floatval}" {else}placeholder="0" 
                    {/if} />
                <p class="help-block">
                    {l s='Maximum weight managed by this carrier. Set the value to "0", or leave this field blank to ignore.' mod='jmarketplace'}
                </p>
            </div>
        {/if}
        {if $JMARKETPLACE_SELLERCARRIER_ASSOCIATE_PRODUCTS == 1}
            <div class="form-group">
                <p class="checkbox">
                    <input type="checkbox" value="1" id="associate_products" name="associate_products">
                    <label
                        for="associate_products">{l s='Associate this carrier with all my products' mod='jmarketplace'}</label>
                </p>
            </div>
        {/if}
        <div class="form-group" style="display:none;">
            {foreach from=$groups item=group}
                <input type="checkbox" checked="checked" value="{$group.id_group|intval}"
                    id="groupBox_{$group.id_group|intval}" class="groupBox" name="groupBox[]">
            {/foreach}
        </div>
        <hr>

        <div class="footer-form">
            <p class="required"><sup>*</sup> {l s='Required fields' mod='jmarketplace'}</p>
            <button type="submit" name="submitCarrier" class="btn btn-primary">
                {if $use_icons == 'fontawesome'}
                    <i class="fa fas fa-save"></i>
                {else}
                    <span class="material-icons">save</span>
                {/if}
                <span>{l s='Save' mod='jmarketplace'}</span>
            </button>
        </div>
        
    </form>
</div>
<script type="text/javascript">
    var need_to_validate = "{l s='Please validate the last range before create a new one.' mod='jmarketplace'}";
    var string_weight = "{l s='Will be applied when the weight is' mod='jmarketplace'}";
    var string_price = "{l s='Will be applied when the price is' mod='jmarketplace'}";
    var PS_WEIGHT_UNIT = "{$PS_WEIGHT_UNIT|escape:'html':'UTF-8'}";
    var currency_sign = "{$currency_sign|escape:'html':'UTF-8'}";
    var delete_range_confirm = '{l s='Are you sure to delete this range?' mod='jmarketplace'}';
    var delete_text = '{l s='Delete' mod='jmarketplace'}';
</script>