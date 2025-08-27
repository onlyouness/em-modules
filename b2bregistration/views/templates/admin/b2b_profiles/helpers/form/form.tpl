{*
* Registration
*
* NOTICE OF LICENSE
*
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FME Modules
*  @copyright 2022 fmemodules All right reserved
*  @license   FMM Modules
*}

{extends file="helpers/form/form.tpl"}
{block name="fieldset"}
	{include file='../../../menu.tpl'}
	<div class = "col-lg-10">
	 {$smarty.block.parent}
	</div>
{/block}
{block name='input'}
    {if $input.type == 'B2BREGISTRATION_NAME_PREFIX_OPTIONS'}
        <input type="hidden" name="ajax_token" id="prefix-ajax-token" value="{$ajax_token|escape:'htmlall':'UTF-8'}">
        <div class="col-lg-5" style="margin-left: -0.8%;" id="auto-scroll">
          <table cellspacing="0" cellpadding="0" class="table std panel">
              <thead>
                  <tr>
                    <th></th>
                    <th>{l s='ID' mod='b2bregistration'}</th>
                    <th>{l s='Name Prefix' mod='b2bregistration'}</th>
                    <th>{l s='Action' mod='b2bregistration'}</th>
                  </tr>
              </thead>
              <tbody>
                {foreach from=$genders item=gender}
                    <tr id ="delete_{$gender.id_gender|escape:'htmlall':'UTF-8'}">
                        <td>
                          <input type="checkbox" class="cpgender" name="{$input.name|escape:'htmlall':'UTF-8'}[]" id="gender_{$gender.id_gender|escape:'htmlall':'UTF-8'}" value="{$gender.id_gender|escape:'htmlall':'UTF-8'}" {if isset($cpGender) AND $cpGender AND in_array($gender.id_gender, $cpGender)}checked="checked"{/if}/>
                        </td>
                        <td>{$gender.id_gender|escape:'htmlall':'UTF-8'}</td>
                        <td>
                          <label for="gender_{$gender.id_gender|escape:'htmlall':'UTF-8'}">{$gender.name|escape:'htmlall':'UTF-8'}</label>
                        </td>
                        <td>
                            <i id="delete-prefix" class="icon icon-delete" style="color:red;" onclick="deletePrefix({$gender.id_gender|escape:'htmlall':'UTF-8'});">Delete</i>
                        </td>
                    </tr>
              {/foreach}
              </tbody>
          </table>
           <p class="help-block hint-block">{l s='Selected customer pefix dropdown options.' mod='b2bregistration'}</p>
        </div>  
        <div class="col-lg-2">
            <button type="submit" class="btn btn-primary" id="add-prefixes">
                {l s='Add More Prefixes' mod='b2bregistration'}
            </button>
        </div>

        {* {if isset($shops) AND $shops}
            <label class="col-lg-3 control-label">{l s='Shop Association' mod='b2bregistration'}</label>
            <div class="margin-form form-group">
              <div class="col-lg-6">{$shops nofilter}html content</div>
            </div>
            <div class="clearfix"></div>
        {/if}  *}
    {elseif $input.type == 'B2BREGISTRATION_SHOPS'}
        <div class="col-lg-5" style="margin-left: -0.8%;" id="auto-scroll">
        <table cellspacing="0" cellpadding="0" class="table std panel">
            <thead>
                <tr>
                  <th></th>
                  <th>{l s='ID' mod='b2bregistration'}</th>
                  <th>{l s='Name' mod='b2bregistration'}</th>
                </tr>
            </thead>
            <tbody>
              {foreach from=$shops item=shop}
                  <tr id ="delete_{$gender.id_shop|escape:'htmlall':'UTF-8'}">
                      <td>
                        <input type="checkbox" class="cpgender" name="{$input.name|escape:'htmlall':'UTF-8'}[]" id="shop_{$shop.id_shop|escape:'htmlall':'UTF-8'}" value="{$gender.id_gender|escape:'htmlall':'UTF-8'}" {if isset($cpGender) AND $cpGender AND in_array($gender.id_gender, $cpGender)}checked="checked"{/if}/>
                      </td>
                      <td>{$gender.id_gender|escape:'htmlall':'UTF-8'}</td>
                      <td>
                        <label for="gender_{$gender.id_gender|escape:'htmlall':'UTF-8'}">{$gender.name|escape:'htmlall':'UTF-8'}</label>
                      </td>
                      <td>
                          <i id="delete-prefix" class="icon icon-delete" style="color:red;" onclick="deletePrefix({$gender.id_gender|escape:'htmlall':'UTF-8'});">Delete</i>
                      </td>
                  </tr>
            {/foreach}
            </tbody>
        </table>
         <p class="help-block hint-block">{l s='Selected customer pefix dropdown options.' mod='b2bregistration'}</p>
      </div>  
      <div class="col-lg-2">
          <button type="submit" class="btn btn-primary" id="add-prefixes">
              {l s='Add More Prefixes' mod='b2bregistration'}
          </button>
      </div>

    {else}
        {$smarty.block.parent}
    {/if}
{/block}
{block name="script"}
var PS_ALLOW_ACCENTED_CHARS_URL = parseInt("{$PS_ALLOW_ACCENTED_CHARS_URL|escape:'javascript':'UTF-8'}");
var success_msg = "{l s='Prefix Successfully Added.' mod='b2bregistration' js=1}";
var delete_msg = "{l s='Prefix deleted Successfully.' mod='b2bregistration' js=1}";
var delete_error = "{l s='Can not deleted prefix.' mod='b2bregistration' js=1}";
var confirm_deletion = "{l s='Are you sure you want to delete prefix?.' mod='b2bregistration' js=1}";

$('#add-prefixes').on('click', function(event) {
    var ajax_token = $('#prefix-ajax-token').val();
    event.preventDefault();
    $.fancybox.open({
        closeClick: false, // prevents closing when clicking INSIDE fancybox 
        href: "{$config_url|escape:'javascript':'UTF-8'}",
        type: "ajax",
        openEffect: 'none',
        closeEffect: 'none',
        autoSize: false,
        width: "40%",
        height: "auto",
        helpers: {
            overlay: { closeClick: false } // prevents closing when clicking OUTSIDE fancybox 
        },
        ajax: {
            type: "POST",
            dataType: "json",
            data: {
                ajax: true,
                action: 'openPrefixesDialog',
                ajax_token : ajax_token,
            }
        }
    });
});

$(document).on('submit', "#prefix-form", function(event) {
    event.preventDefault(); //prevent default action 
    var post_url = $(this).attr("action"); //get form action url
    var request_method = $(this).attr("method"); //get form GET/POST method
    var form_data = new FormData(this); //Creates new FormData object
    console.log(form_data);
    $.ajax({
        url: post_url,
        type: request_method,
        dataType: "json",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false
    }).done(function(response) {
        showSuccessMessage(success_msg);
        $.fancybox.close();
        location.reload();
    });
});

function deletePrefix(id) {
    if (confirm(confirm_deletion)) {
        var ajax_token = $('#prefix-ajax-token').val();
        $.ajax({
            url: "{$config_url|escape:'javascript':'UTF-8'}", //controller url
            type: "POST",
            dataType: "json",
            data: {
                ajax: 1,
                action: 'deletePrefix', // action catch by controller 
                id_prefix: id,
                ajax_token: ajax_token,
            },
            success: function(result) {
                if (result != '') {
                    showSuccessMessage(delete_msg);
                    $("#delete_" + id).remove();
                } else {
                    showErrorMessage(delete_error);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                showErrorMessage('Not Deleted');
            }
        });
    }
    return false;
}

$(document).on('keyup', '#b2b_link_rewrite', function(e) {
    $(this).val(str2url($(this).val(), 'UTF-8'));
});
{/block}