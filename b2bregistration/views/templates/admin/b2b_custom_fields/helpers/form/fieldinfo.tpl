{*
* Registration
*
* NOTICE OF LICENSE
*
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FME Modules
*  @copyright 2023 fmemodules All right reserved
*  @license   FMM Modules
*}

<fieldset id="top">
    {if $version < 1.6}<legend>{else}<h3 class="panel-heading">{/if}
        {l s='B2b Custom Field' mod='b2bregistration'}
    {if $version < 1.6}</legend>{else}</h3>{/if}
    {if $currentObject->id}<input type="hidden" name="id_bb_registration_fields" value="{$currentObject->id|intval}" />{/if}
    <label class="col-lg-3 control-label">{l s='Field Name ' mod='b2bregistration'} <sup style="color:red;">*</sup></label>
    <div class="margin-form form-group">
      <div class="col-lg-8">
        <div class="translatable">
        {foreach from=$languages item=language}
            <div class="lang_{$language.id_lang|intval} col-lg-9" style="display:{if $language.id_lang == $id_lang_default}block{else}none{/if};float:left;margin-bottom:10px;">
                <input type="text" id="field_name_{$language.id_lang|intval}" name="field_name_{$language.id_lang|intval}" value="{$currentTab->getFieldValue($currentObject, 'field_name', $language.id_lang)|escape:'htmlall':'UTF-8'}" />
            </div>
        {/foreach}
        </div>
        <p class="preference_description"></p>
      </div>
    </div>
    <div class="clearfix"></div>

    <label class="col-lg-3 control-label">{l s='Field Size ' mod='b2bregistration'}</label>
    <div class="margin-form form-group">
        <div class="col-lg-8">
            {assign var=var_field_limit value=$currentTab->getFieldValue($currentObject, 'limit')}
            <input type="text" value="{$var_field_limit|escape:'htmlall':'UTF-8'}" name="limit" />
            <p class="help-block">{l s='Only used for text and textarea types, Use zero to disable any limit on this field.' mod='b2bregistration'}</p>
        </div>
    </div>
    <div class="clearfix"></div>
    
    <label class="col-lg-3 control-label">{l s='Profile' mod='b2bregistration'}</label>
    <div class="margin-form form-group">
        <div class="col-lg-8">
            {*headings_collection*}
            <div class="col-lg-4">
                <select id="id_b2b_profile" name="id_b2b_profile">
                    {assign var=var_field_heading value=$currentTab->getFieldValue($currentObject, 'id_b2b_profile')}
                    {foreach from=$headings_collection item=heading}
                        <option value="{$heading.id_b2b_profile|escape:'htmlall':'UTF-8'}" {if $var_field_heading == $heading.id_b2b_profile}selected="selected"{/if}>{$heading.b2b_profile_name|escape:'htmlall':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div><div class="clearfix"></div>
            <p class="help-block">{l s='Associate your custom field with a profile. Custom field will be dsaplyed only on respective profile form.' mod='b2bregistration'}</p>
        </div>
    </div>
    
    <div class="clearfix"></div>
    <label class="col-lg-3 control-label">{l s='Field Type' mod='b2bregistration'}</label>
    <div class="margin-form form-group">
      <div class="col-lg-4">
        {assign var=var_field_type value=$currentTab->getFieldValue($currentObject, 'field_type')}
        <select id="field_type" name="field_type">
            {foreach from=$customFieldTypes key=fieldk item=fieldv}
            <option value="{$fieldk|escape:'htmlall':'UTF-8'}" {if $var_field_type eq $fieldk}selected="selected"{/if}>{l s=$fieldv mod='b2bregistration'}</option>
            {/foreach}
        </select>
        <p class="preference_description"></p>
      </div>
    </div>
    <div class="clearfix"></div>

    <div class="form-wrapper" id="setting-attachment" style="{if isset($var_field_type) AND $var_field_type AND $var_field_type == 'image' OR $var_field_type == 'attachment'}display:block;{else}display:none;{/if}">
        <div class="form-group">
            <label class="control-label col-lg-3">
                <span class="label-tooltip">{l s='Maximum size' mod='b2bregistration'}</span>
            </label>
            <div class="col-lg-2">
                <div class="input-group">
                    <input type="text" value="{$currentTab->getFieldValue($currentObject, 'attachment_size')|escape:'htmlall':'UTF-8'}" name="attachment_size" size="5" class="form-control">
                    <span class="input-group-addon">MB</span>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="form-group">
            <label class="control-label col-lg-3">
                <span class="label-tooltip">{l s='File Types' mod='b2bregistration'}</span>
            </label>
            <div class="col-lg-6">
                <input type="text" value="{$currentTab->getFieldValue($currentObject, 'extensions')|escape:'htmlall':'UTF-8'}" name="extensions" class="form-control">
                <p class="help-block hint-block">{l s='Enter comma(,) separated values.' mod='b2bregistration'}</p>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div id="option_container"  >
        <label class='col-lg-3 control-label' id='show_label'>{l s='Field Option(s)' mod='b2bregistration'} <sup style="color:red;">*</sup></label>
        <!-- <div class="margin-form"> -->
        {if $id_bb_registration_fields == 0 || !isset($list_options)}
            <div class="option_field col-lg-10 col-lg-push-3">
                <div class="translatable margin-form">
                    {foreach from=$languages item=language}
                        <div class="lang_{$language.id_lang|escape:'htmlall':'UTF-8'} col-lg-7" style="display:{if $language.id_lang == $id_lang_default}block{else}none{/if};float:left;margin-bottom:10px;">
                            <input type="text" id="options_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="options_{$language.id_lang|escape:'htmlall':'UTF-8'}[]" value="" />
                        </div>
                    {/foreach}
                </div>
                <div id="valuesOptions" class="form-group margin-form">
                    <div class='col-lg-8'>
                        <a href='javascript:;' class='remove_option btn btn-danger button' onclick="return ($('.option_field').length <= 1)? false : $(this).parent().parent().parent().remove();" style="display:none;">
                            {if $ps_17 > 0}<img src='../img/admin/disabled.gif'/>{else}<img src='../img/admin/forbbiden.gif'/>{/if}{l s='Remove' mod='b2bregistration'}
                        </a>
                    </div>
                </div>
            </div>
        {else}
            {if isset($list_options) AND $list_options}
                {foreach item=option from=$list_options name=option}

                <div class="option_field col-lg-10 col-lg-push-3">
                    <div class="translatable margin-form">
                        {foreach from=$option item=value key=id_lang}
                            <div class="lang_{$id_lang|escape:'htmlall':'UTF-8'} col-lg-7" style="display:{if $id_lang == $id_lang_default}block{else}none{/if};float:left;margin-bottom:10px;">
                                <input type="text" id="options_{$id_lang|escape:'htmlall':'UTF-8'}" name="options_{$id_lang|escape:'htmlall':'UTF-8'}[]" value="{if isset($value) AND $value}{$value|escape:'htmlall':'UTF-8'}{/if}" />
                            </div>
                        {/foreach}
                    </div>
                    <div id="valuesOptions" class="form-group margin-form margin-form">
                        <div class='col-lg-8'>
                            <a href='javascript:;' class='remove_option btn btn-danger button' onclick="return ($('.option_field').length <= 1)? false : $(this).parent().parent().parent().remove();">
                                {if $ps_17 > 0}<img src='../img/admin/disabled.gif'/>{else}<img src='../img/admin/forbbiden.gif'/>{/if}{l s='Remove' mod='b2bregistration'}
                            </a>
                        </div>
                    </div>
                </div>
                {/foreach}
            {else}
                <div class="option_field col-lg-10 col-lg-push-3">
                    <div class="translatable margin-form">
                        {foreach from=$languages item=language}
                            <div class="lang_{$language.id_lang|escape:'htmlall':'UTF-8'} col-lg-7" style="display:{if $language.id_lang == $id_lang_default}block{else}none{/if};float:left;margin-bottom:10px;">
                                <input type="text" id="options_{$language.id_lang|escape:'htmlall':'UTF-8'}" name="options_{$language.id_lang|escape:'htmlall':'UTF-8'}[]" value="" />
                            </div>
                        {/foreach}
                    </div>
                    <div id="valuesOptions" class="form-group margin-form">
                        <div class='col-lg-8'>
                            <a href='javascript:;' class='remove_option btn btn-danger button' onclick="return ($('.option_field').length <= 1)? false : $(this).parent().parent().parent().remove();" style="display:none;">
                                {if $ps_17 > 0}<img src='../img/admin/disabled.gif'/>{else}<img src='../img/admin/forbbiden.gif'/>{/if}{l s='Remove' mod='b2bregistration'}
                            </a>
                        </div>
                    </div>
                </div>
            {/if}

        {/if}

        <div class='clearfix'></div>

        <div class="form-group margin-form">
            <div class='col-lg-9 col-lg-push-3' id='show_options'>
                <a id='new_option'class='btn btn-default button' href='javascript:;' onclick="appendField($(this), {$id_lang_default|escape:'htmlall':'UTF-8'});">
                <img src='../img/admin/add.gif'>{l s='Add Option' mod='b2bregistration'}</a>
            </div>
        </div>
    </div>
    <div class='clearfix'></div>

    <div id="default_value_holder">
        <label class="col-lg-3 control-label">{l s='Default Value' mod='b2bregistration'}</label>
        <div class="margin-form form-group">
          <div class="col-lg-8">    
            <div class="translatable">
            {foreach from=$languages item=language}
                <div class="lang_{$language.id_lang|intval} col-lg-9" style="display:{if $language.id_lang == $id_lang_default}block{else}none{/if};float:left;margin-bottom:10px;">
                    <input type="text" id="default_value_{$language.id_lang|intval}" name="default_value_{$language.id_lang|intval}" value="{$currentTab->getFieldValue($currentObject, 'default_value', $language.id_lang)|escape:'htmlall':'UTF-8'}" />
                </div>
            {/foreach}
            </div>
            <p class="preference_description"></p>
          </div>
        </div>
    </div>
    <div class="clearfix"></div>

    <div id="alert-types" style="{if isset($var_field_type) AND $var_field_type AND $var_field_type == 'message'}display:block;{else}display:none;{/if}">
        <label class="col-lg-3 control-label">{l s='Notice Type' mod='b2bregistration'}</label>
        <div class="col-lg-6 t">
            <div class="danger">
                <input id="type-error" type="radio" name="alert_type" value="error" {if $currentTab->getFieldValue($currentObject, 'alert_type') AND $currentTab->getFieldValue($currentObject, 'alert_type') == 'error'}checked="checked"{/if}>
                <label for="type-error" class="alert alert-danger error">{l s='Error' mod='b2bregistration'}</label>
            </div>
            <div class="warning">
                <input id="type-warning" type="radio" name="alert_type" value="warning" {if $currentTab->getFieldValue($currentObject, 'alert_type') AND $currentTab->getFieldValue($currentObject, 'alert_type') == 'warning'}checked="checked"{/if}>
                <label for="type-warning" class="alert alert-warning warning">{l s='Warning' mod='b2bregistration'}</label>
            </div>
            <div class="info">
                <input id="type-info" type="radio" name="alert_type" value="info" {if $currentTab->getFieldValue($currentObject, 'alert_type') AND $currentTab->getFieldValue($currentObject, 'alert_type') == 'info'}checked="checked"{/if}>
                <label for="type-info" class="alert alert-info info">{l s='Info' mod='b2bregistration'}</label>
            </div>
            <div class="success">
                <input id="type-success" type="radio" name="alert_type" value="success" {if $currentTab->getFieldValue($currentObject, 'alert_type') AND $currentTab->getFieldValue($currentObject, 'alert_type') == 'success'}checked="checked"{/if}>
                <label for="type-success" class="alert alert-success conf">{l s='Success' mod='b2bregistration'}</label>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div id="field_validation_holder">
        <label class="col-lg-3 control-label">{l s='Field Validation' mod='b2bregistration'}</label>
        <div class="margin-form form-group">
          <div class="col-lg-4">    
            {assign var=var_field_validation value=$currentTab->getFieldValue($currentObject, 'field_validation')}
            <select id="field_validation" name="field_validation">
                <option value="" {if $var_field_validation eq ''}selected="selected"{/if}>{l s='None' mod='b2bregistration'}</option>
                <option value="isFloat" {if $var_field_validation eq 'isFloat'}selected="selected"{/if}>{l s='Decimal Number' mod='b2bregistration'}</option>
                <option value="isInt" {if $var_field_validation eq 'isInt'}selected="selected"{/if}>{l s='Integer Number' mod='b2bregistration'}</option>
                <option value="isEmail" {if $var_field_validation eq 'isEmail'}selected="selected"{/if}>{l s='Email Address' mod='b2bregistration'}</option>
                <option value="isAbsoluteUrl" {if $var_field_validation eq 'isAbsoluteUrl'}selected="selected"{/if}>{l s='Website Url Address' mod='b2bregistration'}</option>
                <option value="isName" {if $var_field_validation eq 'isName'}selected="selected"{/if}>{l s='Letters Only' mod='b2bregistration'}</option>
                <option value="isString" {if $var_field_validation eq 'isString'}selected="selected"{/if}>{l s='Letters and/or Numbers' mod='b2bregistration'}</option>
                <option value="isDate" {if $var_field_validation eq 'isDate'}selected="selected"{/if}>{l s='Date' mod='b2bregistration'}</option>
            </select>
            <p class="preference_description"></p>
          </div>
        </div>
    </div>
    <div class="clearfix"></div>

    <!-- Multishop -->
    {if isset($shops) AND $shops}
        <label class="col-lg-3 control-label">{l s='Shop Association' mod='b2bregistration'}</label>
        <div class="margin-form form-group">
          <div class="col-lg-6">{$shops nofilter}{* html content *}</div>
        </div>
        <div class="clearfix"></div>
    {/if}

    <label class="col-lg-3 control-label">{l s='Values Required' mod='b2bregistration'}</label>
    <div class="margin-form form-group col-lg-9">
        {if $version < 1.6}
          <div class="col-lg-4">
                <input type="radio" name="value_required" id="value_required_on" value="1" {if $currentTab->getFieldValue($currentObject, 'value_required')|escape:'htmlall':'UTF-8'}checked="checked"{/if} />
                <label class="t" for="value_required_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='b2bregistration'}" title="{l s='Enabled' mod='b2bregistration'}" style="cursor:pointer" /></label>
                <input type="radio" name="value_required" id="value_required_off" value="0" {if !$currentTab->getFieldValue($currentObject, 'value_required')|escape:'htmlall':'UTF-8'}checked="checked"{/if} />
                <label class="t" for="value_required_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='b2bregistration'}" title="{l s='Disabled' mod='b2bregistration'}" style="cursor:pointer" /></label>
          </div>
         {else}
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input id="value_required_on" type="radio" {if $currentTab->getFieldValue($currentObject, 'value_required') == 1}checked="checked"{/if} value="1" name="value_required" class="form-control">
                    <label class="t" for="value_required_on">{l s='Yes' mod='b2bregistration'}</label>
                    <input id="value_required_off" type="radio" {if $currentTab->getFieldValue($currentObject, 'value_required') == 0}checked="checked"{/if} value="0" name="value_required" class="form-control">
                    <label class="t" for="value_required_off">{l s='No' mod='b2bregistration'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        {/if}
    </div>
    <div class="clearfix"></div>

    <label class="col-lg-3 control-label">{l s='Field Status' mod='b2bregistration'}</label>
    <div class="margin-form form-group col-lg-9">
    {if $version < 1.6}
      <div class="col-lg-4">
            <input type="radio" name="active" id="active_on" value="1" {if $currentTab->getFieldValue($currentObject, 'active')|escape:'htmlall':'UTF-8'}checked="checked"{/if} />
            <label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='b2bregistration'}" title="{l s='Enabled' mod='b2bregistration'}" style="cursor:pointer" /></label>
            <input type="radio" name="active" id="active_off" value="0"  {if !$currentTab->getFieldValue($currentObject, 'active')|escape:'htmlall':'UTF-8'}checked="checked"{/if} />
            <label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='b2bregistration'}" title="{l s='Disabled' mod='b2bregistration'}" style="cursor:pointer" /></label>
        </div>
    {else}
        <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
                <input id="active_on" type="radio" {if $currentTab->getFieldValue($currentObject, 'active') == 1}checked="checked"{/if} value="1" name="active" class="form-control">
                <label class="t" for="active_on">{l s='Yes' mod='b2bregistration'}</label>
                <input id="active_off" type="radio" {if $currentTab->getFieldValue($currentObject, 'active') == 0}checked="checked"{/if} value="0" name="active" class="form-control">
                <label class="t" for="active_off">{l s='No' mod='b2bregistration'}</label>
                <a class="slide-button btn"></a>
            </span>
        </div>
    {/if}
    </div>
    <div class="clearfix"></div>
            <label class="col-lg-3 control-label">{l s='Dependant Field' mod='b2bregistration'}</label>
            <div class="col-lg-9">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input id="dependant_on" onclick="rfCheckDepend(this)" type="radio" {if $currentTab->getFieldValue($currentObject, 'dependant') == 1}checked="checked"{/if} value="1" name="dependant" class="form-control">
                    <label class="t" for="dependant_on">{l s='Yes' mod='b2bregistration'}</label>
                    <input id="dependant_off" onclick="rfCheckDepend(this)" type="radio" {if $currentTab->getFieldValue($currentObject, 'dependant') == 0}checked="checked"{/if} value="0" name="dependant" class="form-control">
                    <label class="t" for="dependant_off">{l s='No' mod='b2bregistration'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
            <div class="clearfix"></div>
            <div id="rf_dependant_field" style="{if $currentTab->getFieldValue($currentObject, 'dependant') > 0}display: block;{else}display: none;{/if} padding-top: 15px;">
                <label class="col-lg-3 control-label">{l s='Select Dependant Field' mod='b2bregistration'}</label>
                <div class="col-lg-9">
                    <select name="dependant_field" class="col-lg-8" onchange="rfGetRelativeVals(this);">
                        <option value="0">-</option>
                        {if !empty($fields_collection)}
                            {foreach from=$fields_collection item=field}
                                {if $field.id_bb_registration_fields != $id_bb_registration_fields && (in_array($field.field_type, ['select','checkbox', 'radio', 'boolean']))}<option value="{$field.id_bb_registration_fields|escape:'htmlall':'UTF-8'}"{if isset($field.dep_check) && $field.dep_check == $field.id_bb_registration_fields} selected="selected"{/if}>{$field.id_bb_registration_fields|escape:'htmlall':'UTF-8'} - {$field.field_name|escape:'htmlall':'UTF-8'}</option>{/if}
                            {/foreach}
                        {/if}
                    </select>
                </div>
                <div class="clearfix" style="clear: both;padding-top: 15px;"></div>
                <label class="col-lg-3 control-label">{l s='Select Dependant Value' mod='b2bregistration'}</label>
                <div class="col-lg-9">
                    <select name="dependant_value" class="col-lg-8" id="dependant_value">
                        <option value="0">-</option>
                        {if !empty($field_values)}
                            {foreach from=$field_values item=field}
                                <option value="{$field.field_value_id|escape:'htmlall':'UTF-8'}"{if $field.check && $field.check == $field.field_value_id} selected="selected"{/if}>{$field.field_value|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        {/if}
                    </select>
                    <small class="form-text help-block" style="display: block; clear: both; padding-top: 5px;">{l s='Choose dependant field first so this option is populated.' mod='b2bregistration'}</small>
                </div>
            </div>
    <div class="clearfix"></div>
</fieldset>