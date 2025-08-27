{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    FMM Modules
*  @copyright 2022 FME Modules
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{extends file="helpers/form/form.tpl"}
{block name="fieldset"}
	{include file='../../../menu.tpl'}
	<div class = "col-lg-10">
	 {$smarty.block.parent}
	</div>
{/block}

{block name = 'input'}
	{if $input.type == 'checkbox'}
		{foreach $input.values.query as $value}
			{assign var=id_checkbox value=$value[$input.values.id]}
			<div class="checkbox{if isset($input.expand) && strtolower($input.expand.default) == 'show'} hidden{/if}">
				{strip}
					<label for="checkbox_{$id_checkbox|escape:'htmlall':'UTF-8'}">
						<input type="checkbox" name="{$input.name|escape:'htmlall':'UTF-8'}" id="checkbox_{$id_checkbox|escape:'htmlall':'UTF-8'}" class="{if isset($input.class)}{$input.class|escape:'htmlall':'UTF-8'}{/if}"{if isset($value.val)} value="{$value.val|escape:'html':'UTF-8'}"{/if}{if isset($fields_value[$input.name]) && $fields_value[$input.name] && in_array($id_checkbox, $fields_value[$input.name])} checked="checked"{/if} />
						{$value[$input.values.name]|escape:'htmlall':'UTF-8'}
					</label>
				{/strip}
			</div>
		{/foreach}
	{else if $input.type == 'swap'}
	<div class="form-group swap-container">
		<div class="col-lg-9">
			<div class="form-control-static row">
				<div class="col-xs-6">
					<select {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} availableSwap" id="availableSwap" name="{$input.name|escape:'html':'UTF-8'}_available[]" multiple="multiple">
					{foreach $input.options.query AS $option}
						{if is_object($option)}
							{if !isset($fields_value[$input.name]) && !in_array($option->$input.options.id, $fields_value[$input.name])}
								<option value="{$option->$input.options.id|escape:'htmlall':'UTF-8'}">{$option->$input.options.name|escape:'htmlall':'UTF-8'}</option>
							{/if}
						{elseif $option == "-"}
							<option value="">-</option>
						{else if isset($fields_value[$input.name]) && $fields_value[$input.name]}
							{if !in_array({$option[$input.options.id]}, $fields_value[$input.name])}
								<option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}">{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>
							{/if}
						{else}
							<option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}">{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>
						{/if}
					{/foreach}
					</select>
					<a href="#" id="addSwap" class="btn btn-default btn-block addSwap">{l s='Add'  mod='b2bregistration'} <i class="icon-arrow-right"></i></a>
				</div>
				<div class="col-xs-6">
					<select {if isset($input.size)}size="{$input.size|escape:'html':'UTF-8'}"{/if}{if isset($input.onchange)} onchange="{$input.onchange|escape:'html':'UTF-8'}"{/if} class="{if isset($input.class)}{$input.class|escape:'html':'UTF-8'}{/if} selectedSwap" id="selectedSwap" name="{$input.name|escape:'html':'UTF-8'}" multiple="multiple">
					{foreach $input.options.query AS $option}
						{if is_object($option)}
							{if isset($fields_value[$input.name]) && $fields_value[$input.name] && is_array($fields_value[$input.name]) && in_array($option->$input.options.id, $fields_value[$input.name])}
								<option value="{$option->$input.options.id|escape:'htmlall':'UTF-8'}">{$option->$input.options.name|escape:'htmlall':'UTF-8'}</option>
							{/if}
						{elseif $option == "-"}
							<option value="">-</option>
						{else}
							{if isset($fields_value[$input.name]) && $fields_value[$input.name] && is_array($fields_value[$input.name]) && in_array($option[$input.options.id], $fields_value[$input.name])}
								<option value="{$option[$input.options.id]|escape:'htmlall':'UTF-8'}">{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>
							{/if}
						{/if}
					{/foreach}
					</select>
					<a href="#" id="removeSwap" class="btn btn-default btn-block removeSwap"><i class="icon-arrow-left"></i> {l s='Remove'  mod='b2bregistration'}</a>
				</div>
			</div>
		</div>
	</div>
	{else if $input.type == 'name_suffix'}
		<div class="col-lg-4" style="margin-left: -0.8%;">
			<select name="name_suffix" class="form-control">
				{foreach from=$name_suffix item=suffix}
					<option value="{$suffix|escape:'htmlall':'UTF-8'}" {if isset($selected_name_sufix) AND !empty($selected_name_sufix)}selected{/if}>{$suffix|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
		</div>
	{else if $input.name == 'custom_fields'}
		{if !empty($custom_fields) AND $enable_custom}
			{if !empty($id_customer) AND $id_customer}
				</div>
				{foreach from=$fields_custom item=fields}
					<input type="hidden" name="id_fields[]" value="{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">
					<input type="hidden" name="label_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" value="{if !empty($fields.b2b_field_title)}{$fields.b2b_field_title|escape:'htmlall':'UTF-8'} {/if}">
					
					{if $fields.b2b_field_type == 'text'}
						<div class="form-group">
							<label class="control-label col-lg-3" for="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{$fields.b2b_field_title|escape:'htmlall':'UTF-8'}</label>
							<div class="col-lg-4">
								<input type="text" name="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" value="{if !empty($fields.b2b_field_name)}{$fields.b2b_field_name|escape:'htmlall':'UTF-8'} {/if}" id="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" class="form-control" {if $fields.field_required}required="required"{/if}>
							</div>
						</div>
					{else}
						<div class="form-group">
							<label class="control-label col-lg-3" for="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{$fields.b2b_field_title|escape:'htmlall':'UTF-8'}</label>
							<div class="col-lg-4">
								<textarea name="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" class="form-control" {if $fields.field_required}required=""{/if} id="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{if !empty($fields.b2b_field_title)}{$fields.b2b_field_name|escape:'htmlall':'UTF-8'} {/if}</textarea>
							</div>
						</div>
					{/if}
				{/foreach}
				{else}
					</div>
					{foreach from=$custom_fields item=fields}
						<input type="hidden" name="id_fields[]" value="{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">
						<input type="hidden" name="label_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" value="{$fields.b2b_field_name|escape:'htmlall':'UTF-8'}">
						
						{if $fields.b2b_field_type == 'text'}
							<div class="form-group">
								<label class="control-label col-lg-3" for="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{$fields.b2b_field_name|escape:'htmlall':'UTF-8'}</label>
								<div class="col-lg-4">
									<input type="text" name="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" id="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" class="form-control" {if $fields.field_required}required="required"{/if}>
								</div>
							</div>
						{else}
							<div class="form-group">
								<label class="control-label col-lg-3" for="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}">{$fields.b2b_field_name|escape:'htmlall':'UTF-8'}</label>
								<div class="col-lg-4">
									<textarea name="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}" class="form-control" {if $fields.field_required}required=""{/if} id="field_{$fields.id_b2b_custom_fields|escape:'htmlall':'UTF-8'}"></textarea>
								</div>
							</div>
						{/if}
					{/foreach}
				{/if}
			<div class="form-group">
		{/if}
	{else}
    	{$smarty.block.parent}
	{/if}
{/block}