{*
* Registration
*
* NOTICE OF LICENSE
*
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FME Modules
*  @copyright 2021 fmemodules All right reserved
*  @license   FMM Modules
*}

{foreach from=$summary_fields item=field name=bb_fields}
    <div class="col-md-6">
        <div class="rf_input_wrapper rf_only_f_{$field['dependant_field']|escape:'htmlall':'UTF-8'} required form-group text form-group{if $field['dependant'] > 0} rf_no_display rf_no_display_{$field['dependant_field']|escape:'htmlall':'UTF-8'}_{$field['dependant_value']|escape:'htmlall':'UTF-8'}{/if}"
            data-id="{$field.id_bb_registration_fields|escape:'htmlall':'UTF-8'}"
            data-f="{$field['dependant_field']|escape:'htmlall':'UTF-8'}"
            data-v="{$field['dependant_value']|escape:'htmlall':'UTF-8'}">
            <label class="rf_input_label {if $field['value_required']} required {/if}form-control-label">
                {$field['field_name']|escape:'htmlall':'UTF-8'}
                {if $field['value_required'] AND $version >= 1.7}<sup class="required" style="color: red!important">*</sup>{/if}
            </label>
            <div>
                {assign var='field_value' value=BToBCustomFields::getFormatedValue($field, null, $id_customer, $id_guest)}
                {if $field['field_type'] eq 'text'}
                    {assign var="text_default_value" value=$field['default_value']}
                    {if $field.editable == 0}
                        {if isset($field_value) AND $field_value}
                            <span class="form-control">{$field_value|escape:'htmlall':'UTF-8'}</span>
                        {else}
                            <input type="text"
                            name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]"
                            data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                            class="text {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if} form-control"
                            {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if}/>
                        {/if}
                    {else}
                        <input type="text"
                        name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]"
                        data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                        value="{if !empty($field_value) AND $field_value}{$field_value|escape:'htmlall':'UTF-8'}{elseif !empty($text_default_value) AND $text_default_value}{$text_default_value|escape:'htmlall':'UTF-8'}{elseif !empty($smarty.post.fields[$field.id_bb_registration_fields])}{$smarty.post.fields[$field.id_bb_registration_fields]|escape:'htmlall':'UTF-8'}{/if}"
                        class="text {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if} form-control" {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if}/>
                    {/if}

                {elseif $field['field_type'] eq 'textarea'}
                    {assign var="texta_default_value" value=$field['default_value']}
                    {if $field.editable == 0}

                            {if isset($field_value) AND $field_value}
                                <span class="form-control">{$field_value|escape:'htmlall':'UTF-8'}</span>
                            {else}
                                <textarea name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]"
                                class="form-control {if $field['value_required']}is_required{/if}"
                                data-type="{$field.field_type|escape:'htmlall':'UTF-8'}" rows="5"
                                {if isset($field['field_validation']) AND $field['field_validation']}data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if}></textarea>
                            
                            {/if}
                    {else}
                        <textarea name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]"
                        class="form-control {if $field['value_required']}is_required{/if}"
                        data-type="{$field.field_type|escape:'htmlall':'UTF-8'}" rows="5"
                        {if isset($field['field_validation']) AND $field['field_validation']}data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if}>{if !empty($field_value) AND $field_value}{$field_value|escape:'htmlall':'UTF-8'}{elseif !empty($text_default_value) AND $text_default_value}{$text_default_value|escape:'htmlall':'UTF-8'}{elseif !empty($smarty.post.fields[$field.id_bb_registration_fields])}{$smarty.post.fields[$field.id_bb_registration_fields]|escape:'htmlall':'UTF-8'}{/if}</textarea>
                    {/if}

                {elseif $field['field_type'] eq 'date'}
                    {if $field.editable == 0}

                        {if isset($field_value) AND $field_value}
                            <span class="form-control">{$field_value|escape:'htmlall':'UTF-8'}</span>
                        {else}
                            <input type="text"
                            class="fields_datapicker form-control {if $field['value_required']} is_required {/if} validate_field"
                            data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                            name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]"
                            data-validate="isDate"/>
                        {/if}
                    {else}
                        <input class="fields_datapicker form-control {if $field['value_required']} is_required {/if} validate_field"
                        type="text"
                        data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                        name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]"
                        value="{if !empty($field_value) AND $field_value}{$field_value|escape:'htmlall':'UTF-8'}{elseif !empty($smarty.post.fields[$field.id_bb_registration_fields])}{$smarty.post.fields[$field.id_bb_registration_fields]|escape:'htmlall':'UTF-8'}{/if}"
                        data-validate="isDate"/>
                    {/if}

                {elseif $field['field_type'] eq 'boolean'}

                    {if $field.editable == 0}
                        {if isset($field_value) AND $field_value}
                            <span class="form-control">{$field_value|escape:'htmlall':'UTF-8'}</span>
                        {else}
                            <select class="select form-control {if $field['value_required']}is_required {/if}"
                            data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                            name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]"
                            data-field="{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}">
                                <option value="0">{l s='No' mod='b2bregistration'}</option>
                                <option value="1">{l s='Yes' mod='b2bregistration'}</option>
                            </select>
                        {/if}
                    {else}
                        <select class="select form-control {if $field['value_required']}is_required {/if}"
                        name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]"
                        data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                        data-field="{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}">
                            <option value="0" {if !empty($field_value) AND $field_value == '0'}selected="selected"{/if}>{l s='No' mod='b2bregistration'}</option>
                            <option value="1" {if !empty($field_value) AND $field_value == '1'}selected="selected"{/if}>{l s='Yes' mod='b2bregistration'}</option>
                        </select>
                    {/if}

                {elseif $field.field_type eq 'select'}

                    {if $field.editable == 0}
                        {if isset($field_value) AND $field_value}
                            {$field_value = BToBCustomFields::getFieldsValueById($field_value)}
                            <span class="form-control">{$field_value|escape:'htmlall':'UTF-8'}</span>
                        {else}
                            <select class="select form-control {if $field['value_required']}is_required {/if}"
                            name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]"
                            data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                            data-field="{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}">
                            <option value="">{l s='Select Option' mod='b2bregistration'}</option>
                            {foreach from=$summary_fields_values[$field['id_bb_registration_fields']] item=summary_fields_value}
                                <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>
                        {/if}
                    {else}
                        <select class="select form-control {if $field['value_required']}is_required {/if}"
                        name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]"
                        data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                        data-field="{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}">
                            <option value="">{l s='Select Option' mod='b2bregistration'}</option>
                            {foreach from=$summary_fields_values[$field['id_bb_registration_fields']] item=summary_fields_value}
                                <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                {if isset($field_value) AND $summary_fields_value.field_value_id == $field_value}selected="selected"{/if}>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>
                    {/if}

                {elseif $field['field_type'] eq 'radio'}
                    <input class="rf_checkboxes" type="hidden" data-required="{$field['value_required']|escape:'htmlall':'UTF-8'}" value="{if $field['dependant'] > 0}1{else}{count($field_value)|escape:'htmlall':'UTF-8'}{/if}"{if $field['dependant'] > 0} data-depend="1"{else} data-depend="0"{/if}>
                    {if $field.editable == 0}
                        {if isset($field_value) AND $field_value}
                            <span class="form-control">
                                {if isset($field_value) AND is_array($field_value)}
                                    {BToBCustomFields::getOptionValue(implode(',',$field_value))|escape:'htmlall':'UTF-8'}
                                {/if}
                            </span>
                        {else}
                            {foreach from=$summary_fields_values[$field['id_bb_registration_fields']] item=summary_fields_value}
                                <div class="type_multiboxes" id="uniform-{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                    <input type="radio"
                                    data-field="{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}"
                                    data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                                    id="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                    class="form-control {if $field['value_required']}is_required {/if}"
                                    name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}][]"
                                    value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"/>
                                    <label class="type_multiboxes top" for="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                        <span><span></span></span>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                    </label>
                                </div>
                            {/foreach}
                        {/if}
                    {else}
                        {foreach from=$summary_fields_values[$field['id_bb_registration_fields']] item=summary_fields_value}
                            <div class="type_multiboxes" id="uniform-{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                <input type="radio"
                                data-field="{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}"
                                data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                                id="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                class="form-control {if $field['value_required']}is_required {/if}"
                                name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}][]"
                                value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                {if isset($field_value) AND is_array($field_value) && in_array($summary_fields_value.field_value_id, $field_value)}checked="checked"{/if}
                                />
                                <label class="type_multiboxes top" for="radio_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                    <span><span></span></span>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                </label>
                            </div>
                        {/foreach}
                    {/if}

                {elseif $field['field_type'] eq 'checkbox'}
                    <input class="rf_checkboxes" type="hidden" data-required="{$field['value_required']|escape:'htmlall':'UTF-8'}" value="{if $field['dependant'] > 0}1{else}{count($field_value)|escape:'htmlall':'UTF-8'}{/if}"{if $field['dependant'] > 0} data-depend="1"{else} data-depend="0"{/if}>
                    {if $field.editable == 0}
                        {if isset($field_value) AND $field_value}
                            <span class="form-control">
                                {if isset($field_value) AND is_array($field_value)}
                                    {BToBCustomFields::getOptionValue(implode(',',$field_value))|escape:'htmlall':'UTF-8'}
                                {/if}
                            </span>
                        {else}
                            {foreach from=$summary_fields_values[$field['id_bb_registration_fields']] item=summary_fields_value}
                                <div class="type_multiboxes checker" id="uniform-{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                    <input type="checkbox"
                                    data-field="{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}"
                                    data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                                    value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                    name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}][]" id="checkbox_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                    class="{if $field['value_required']}is_required{/if}"/>
                                    <label class="type_multiboxes" for="checkbox_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                        <span></span>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                    </label>
                                </div>
                            {/foreach}
                        {/if}
                    {else}
                        {foreach from=$summary_fields_values[$field['id_bb_registration_fields']] item=summary_fields_value}

                                <div class="type_multiboxes" id="uniform-{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                    <input type="checkbox"
                                    data-field="{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}"
                                    data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                                    value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                    name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}][]" id="checkbox_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}"
                                    class="{if $field['value_required']}is_required{/if} form-control"
                                    {if isset($field_value) AND is_array($field_value) AND in_array($summary_fields_value.field_value_id, $field_value)}checked="checked"{/if}
                                    />
                                    <label class="type_multiboxes" for="checkbox_{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">
                                        <span></span>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                    </label>
                                </div>

                        {/foreach}
                    {/if}

                {elseif $field['field_type'] eq 'multiselect'}
                    <input class="rf_checkboxes" type="hidden" data-required="{$field['value_required']|escape:'htmlall':'UTF-8'}" value="{count($field_value)|escape:'htmlall':'UTF-8'}">
                    {if $field.editable == 0}
                        {if isset($field_value) AND $field_value}
                            <span class="form-control">
                                {if isset($field_value) AND is_array($field_value)}
                                    {BToBCustomFields::getOptionValue(implode(',',$field_value))|escape:'htmlall':'UTF-8'}
                                {/if}
                            </span>
                        {else}
                            <select name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}][]"
                            multiple="multiple" 
                            data-type="{$field.field_type|escape:'htmlall':'UTF-8'}"
                            class="type_multiboxes multiselect form-control {if $field['value_required']}is_required {/if}">
                                {foreach from=$summary_fields_values[$field['id_bb_registration_fields']] item=summary_fields_value}
                                    <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}">{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                    </option>
                                {/foreach}
                            </select>
                            <p><small>{l s='Hold CTRL/Command key to select multiple values.' mod='b2bregistration'}</small></p>
                        {/if}
                    {else}
                        <select name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}][]"
                        multiple="multiple"
                        class="type_multiboxes multiselect form-control {if $field['value_required']}is_required {/if}">
                            {foreach from=$summary_fields_values[$field['id_bb_registration_fields']] item=summary_fields_value}
                                <option value="{$summary_fields_value['field_value_id']|escape:'htmlall':'UTF-8'}" {if isset($field_value) AND is_array($field_value) AND in_array($summary_fields_value.field_value_id, $field_value)}selected="selected"{/if}>{$summary_fields_value['field_value']|escape:'htmlall':'UTF-8'}
                                </option>
                            {/foreach}
                        </select>
                        <p><small>{l s='Hold CTRL/Command key to select multiple values.' mod='b2bregistration'}</small></p>
                    {/if}

                {elseif $field['field_type'] eq 'image'}
                    <div id="field_image_{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}">
                        {assign var='root_dir' value=($smarty.const._PS_ROOT_DIR_|cat:'/')}
                        {if $field.editable == 0}
                            {assign var='field_value' value=''}
                            {if isset($value_reg_fields) AND $value_reg_fields}
                                {foreach from=$value_reg_fields item=field_edit}
                                
                                    {if !empty($field_edit) AND $field_edit AND $field_edit['id_bb_registration_fields'] == $field['id_bb_registration_fields'] AND !empty($field_edit['value'])}
                                        {assign var='field_value' value=$field_edit['value']|replace:$root_dir:''}
                                    {/if}
                                
                                {/foreach}
                            {else}
                                {assign var='field_value' value=''}
                            {/if}

                            {if isset($field_value) AND $field_value}
                                <img id="preview-image-{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}" class="image_container" src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}{$field_value|escape:'htmlall':'UTF-8'}">
                            {else}
                                <img id="preview-{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}" class="image_container" src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/b2bregistration/views/img/empty.png">
                                <input type="file" name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]" id="image_{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}" class="image_input {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if}" {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if} {if isset($field.extensions) AND $field.extensions} data-extensions="{$field.extensions|escape:'htmlall':'UTF-8'}"{/if} data-id="{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}">
                                <p class="alert alert-danger error extension_error">{l s='Image type not allowed.' mod='b2bregistration'}</p>
                            {/if}
                        {else}
                            <img id="preview-{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}" class="image_container" src="{if !empty($value_reg_fields) AND $value_reg_fields}{foreach from=$value_reg_fields item=field_edit}{if !empty($field_edit) AND $field_edit AND $field_edit['id_bb_registration_fields'] == $field['id_bb_registration_fields'] AND !empty($field_edit['value'])}{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}{$field_edit.value|replace:$root_dir:''|escape:'htmlall':'UTF-8'}{/if}{/foreach}{else}{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/b2bregistration/views/img/empty.png{/if}" title="{l s='Click here to upload image' mod='b2bregistration'}">
                            <input type="file" name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}]" id="image_{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}" class="image_input {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if}" {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if} {if isset($field.extensions) AND $field.extensions} data-extensions="{$field.extensions|escape:'htmlall':'UTF-8'}"{/if} data-id="{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}">
                            {if isset($field.extensions) AND $field.extensions} <p>{l s='Allowed file types' mod='b2bregistration'}: {$field.extensions|escape:'htmlall':'UTF-8'}</p>{/if}
                            <p class="alert alert-danger error extension_error">{l s='Image type not allowed.' mod='b2bregistration'}</p>
                        {/if}
                    </div>

                {elseif $field['field_type'] eq 'attachment'}
                        <div id="field_attachment_{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}">
                        {assign var='root_dir' value=($smarty.const._PS_ROOT_DIR_|cat:'/')}
                        {if $field.editable == 0}
                            {assign var='field_value' value=''}
                            {if isset($value_reg_fields) AND $value_reg_fields}
                                {foreach from=$value_reg_fields item=field_edit}
                                
                                    {if !empty($field_edit) AND $field_edit AND $field_edit['id_bb_registration_fields'] == $field['id_bb_registration_fields'] AND !empty($field_edit['value'])}
                                        {assign var='field_value' value=$field_edit['value']|replace:$root_dir:''}
                                    {/if}
                                
                                {/foreach}
                            {else}
                                {assign var='field_value' value=''}
                            {/if}

                            {if isset($field_value) AND $field_value}
                                <a class="btn button btn-primary" href="{$actionLink|escape:'htmlall':'UTF-8'}&field={base64_encode({$field.id_bb_registration_fields|escape:'htmlall':'UTF-8'})|escape:'htmlall':'UTF-8'}">{pathinfo($field_value|escape:'htmlall':'UTF-8', $smarty.const.PATHINFO_FILENAME)|escape:'htmlall':'UTF-8'}
                                </a>
                                <br>
                            {else}
                                <img id="preview-{$field.id_bb_registration_fields|escape:'htmlall':'UTF-8'}" class="image_container" src="{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/b2bregistration/views/img/empty.png">
                                <input type="file" name="fields[{$field.id_bb_registration_fields|escape:'htmlall':'UTF-8'}]" value="{if !empty($value_reg_fields) AND $value_reg_fields}{foreach from=$value_reg_fields item=field_edit}{if !empty($field_edit) AND $field_edit AND $field_edit.id_bb_registration_fields == $field.id_bb_registration_fields AND !empty($field_edit['value'])}{$field_edit['value']|escape:'htmlall':'UTF-8'}{/if}{/foreach}{elseif empty($value_reg_fields) AND !empty($text_default_value) AND $text_default_value}{$text_default_value|escape:'htmlall':'UTF-8'}{/if}"  class="form-control attachment {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if}" {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if} {if isset($field.extensions) AND $field.extensions} data-extensions="{$field.extensions|escape:'htmlall':'UTF-8'}"{/if}>
                                {if isset($field.extensions) AND $field.extensions} <p>{l s='Allowed file types' mod='b2bregistration'}: {$field.extensions|escape:'htmlall':'UTF-8'}</p>{/if}
                                <p class="alert alert-danger error extension_error">{l s='Image type not allowed.' mod='b2bregistration'}</p>
                            {/if}
                        {else}
                            {if !empty($value_reg_fields) AND $value_reg_fields}
                                {foreach from=$value_reg_fields item=field_edit}
                                    {if !empty($field_edit) AND $field_edit AND $field_edit['id_bb_registration_fields'] == $field['id_bb_registration_fields'] AND !empty($field_edit['value'])}
                                        <a class="btn button btn-primary" href="{$actionLink|escape:'htmlall':'UTF-8'}&field={base64_encode({$field.id_bb_registration_fields|escape:'htmlall':'UTF-8'})|escape:'htmlall':'UTF-8'}">{pathinfo($field_edit.value|replace:$root_dir:''|escape:'htmlall':'UTF-8', $smarty.const.PATHINFO_FILENAME)|escape:'htmlall':'UTF-8'}</a>
                                        <br>
                                    {/if}
                                {/foreach}
                            {/if}
                            <input type="file" name="fields[{$field.id_bb_registration_fields|escape:'htmlall':'UTF-8'}]" value="{if !empty($value_reg_fields) AND $value_reg_fields}{foreach from=$value_reg_fields item=field_edit}{if !empty($field_edit) AND $field_edit AND $field_edit.id_bb_registration_fields == $field.id_bb_registration_fields AND !empty($field_edit['value'])}{$field_edit['value']|escape:'htmlall':'UTF-8'}{/if}{/foreach}{elseif empty($value_reg_fields) AND !empty($text_default_value) AND $text_default_value}{$text_default_value|escape:'htmlall':'UTF-8'}{/if}"  class="form-control attachment {if $field['value_required']}is_required {/if}{if isset($field['field_validation']) AND $field['field_validation'] }validate_field{/if}" {if isset($field['field_validation']) AND $field['field_validation']} data-validate="{$field['field_validation']|escape:'htmlall':'UTF-8'}"{/if} {if isset($field.extensions) AND $field.extensions} data-extensions="{$field.extensions|escape:'htmlall':'UTF-8'}"{/if}>
                            {if isset($field.extensions) AND $field.extensions} <p>{l s='Allowed file types' mod='b2bregistration'}: {$field.extensions|escape:'htmlall':'UTF-8'}</p>{/if}
                            <p class="alert alert-danger error extension_error">{l s='Image type not allowed.' mod='b2bregistration'}</p>
                            {/if}
                        </div>

                {elseif $field['field_type'] eq 'message'}
                    <div class="alert alert-{if isset($field['alert_type']) && $field['alert_type'] && $field['alert_type'] == 'error'}danger {$field['alert_type']|escape:'htmlall':'UTF-8'}{else}{$field['alert_type']|escape:'htmlall':'UTF-8'}{/if}">
                        {$field['default_value']|escape:'htmlall':'UTF-8'}
                    </div>
                    <input type="hidden" name="fields[{$field['id_bb_registration_fields']|escape:'htmlall':'UTF-8'}][]" value="{$field['default_value']|escape:'htmlall':'UTF-8'}" />
                {/if}
            </div>
        </div>
    </div>
{/foreach}

{if $is_psgdpr}
    <div class="form-group row ">
        <label class="col-md-3 form-control-label required"></label>
        <div class="col-md-6">
            {hook h='displayGDPRConsent' mod='psgdpr' id_module=$id_module}
        </div>
    </div>
{/if}

{literal}
<style>
.rf_no_display { display: none;}
#registration_fields .radio-inline, #registration_fields .checkbox { display: inline-block; margin-right: 3%}
#registration_fields .radio-inline .radio, #registration_fields .checkbox .checker { display:inline-block; padding-right: 3px; vertical-align: middle}
</style>
{/literal}
