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

{extends file="helpers/form/form.tpl"}


{block name="fieldset"}
		    {include file='../../../menu.tpl'}
    <div class="col-lg-10 panel">
    
        {include file='./fieldinfo.tpl'}
        
        {block name="footer"}
        
        {if $version >= 1.6}
            <div class="panel-footer">
                <a href="{$link->getAdminLink('AdminB2BCustomFields')|escape:'html':'UTF-8'}" class="btn btn-default">
                    <i class="process-icon-cancel"></i> {l s='Cancel' mod='b2bregistration'}
                </a>
                <button type="submit" name="submitAddbb_registration_fields" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> {l s='Save' mod='b2bregistration'}
                </button>
                <button type="submit" name="submitAddbb_registration_fieldsAndStay" class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> {l s='Save and stay' mod='b2bregistration'}
                </button>
            </div>
        {else}
            <div style="text-align:center">
                <input type="submit"
                value="{l s='Save' mod='b2bregistration'}"
                class="button"
                name="submitAddfmm_registration_fields"
                id="{$table|escape:'htmlall':'UTF-8'}_form_submit_btn" />
            </div>
        {/if}

        {/block}

    </div>

{/block}

{block name="script"}
    var editableFields = ['message'];
    var currentToken = "{$currentToken|escape:'javascript':'UTF-8'}";
    var currentFormTab = "{if isset($smarty.post.currentFormTab)}{$smarty.post.currentFormTab|escape:'htmlall':'UTF-8'}{else}informations{/if}";
    var mod_url = "{$action_url|escape:'javascript':'UTF-8'}";
    var selected_shops = "{$selected_shops|escape:'javascript':'UTF-8'}";

    $(document).ready(function()
    {
        // $('.displayed_flag').addClass('col-lg-3');
        $('.language_flags').css('float','left').hide();
        $(".pointer").addClass("btn btn-default dropdown-toggle");

        // shop association
        $(".tree-item-name input[type=checkbox]").each(function() {
            $(this).prop("checked", false);
            $(this).removeClass("tree-selected");
            $(this).parent().removeClass("tree-selected");
            if ($.inArray($(this).val(), selected_shops) != -1) {
                $(this).prop("checked", true);
                $(this).parent().addClass("tree-selected");
                $(this).parents("ul.tree").each(function() {
                        $(this).children().children().children(".icon-folder-close")
                            .removeClass("icon-folder-close")
                            .addClass("icon-folder-open");
                        $(this).show();
                    }
                );
            }
        });
    });

    var languages = new Array();
    {foreach from=$languages item=language key=k}
        languages[{$k|escape:'htmlall':'UTF-8'}] = {
            id_lang: "{$language.id_lang|escape:'htmlall':'UTF-8'}",
            iso_code: "{$language.iso_code|escape:'htmlall':'UTF-8'}",
            name: "{$language.name|escape:'htmlall':'UTF-8'}"
        };
    {/foreach}
    displayFlags(languages, {$id_lang_default|escape:'htmlall':'UTF-8'});

    function displayCartRuleTab(tab) {
        $('.cart_rule_tab').hide();
        $('.tab-page').removeClass('selected');
        $('#advance_blog_' + tab).show();
        $('#advance_blog_link_' + tab).addClass('selected');
        $('#currentFormTab').val(tab);
    }

    $('.cart_rule_tab').hide();
    $('.tab-page').removeClass('selected');
    $('#advance_blog_' + currentFormTab).show();
    $('#advance_blog_link_' + currentFormTab).addClass('selected'); 

    function checkOptions(){
        var field_type = $('#field_type').val();

        if (jQuery.inArray(field_type, editableFields) === -1) {
            $('#field-editable').show();
        } else {
            $('#field-editable').hide();
        }

        if (field_type == 'image' || field_type == 'attachment') {
            $('#setting-attachment').show();
        } else {
            $('#setting-attachment').hide();
        }

        if (field_type == 'message') {
            $('#alert-types').show();
        } else {
            $('#alert-types').hide();
        }

        if ( field_type == "multiselect" || field_type == "select" ||  field_type == "checkbox" || field_type == "radio"){
            $("#option_container").show();
            //$("#show_options").show();
        } else {
            $("#option_container").hide();
            //$("#show_options").hide();
        }

        if ( field_type == "text" || field_type == "textarea" ||  field_type == "message"){
            $("#default_value_holder").show();
        } else {
            $("#default_value_holder").hide();
        }

        if ( field_type == "text" || field_type == "textarea"){
            $("#field_validation_holder").show();
        } else {
            $("#field_validation_holder").hide();
        }
    }

    $("#field_type").change(function() {
        checkOptions();
        if($('.option_field').length > 1) {
            $('.remove_option').show();
        }
    });
    checkOptions();

    function appendField(curr, id_lang) {
        $('.remove_option').show();
        $('#options_' + id_lang).parent().parent().parent().clone(true).insertBefore(curr.parent().parent());
    }

    function rfCheckDepend(el) {
        _value = parseInt($(el).val());
        if (_value > 0) {
            $('#rf_dependant_field').show();
        }
        else {
            $('#rf_dependant_field').hide();
        }
    }

    function rfGetRelativeVals(_el) {
        _val_f = parseInt($(_el).val());
        var _list = '';
        if (_val_f > 0) {
            $('#dependant_value').removeAttr('disabled');
            $.ajax({
                type: 'GET',
                dataType: 'json',
                url: mod_url+'&ajax=1&id_dep='+_val_f,
                success: function(data)
                {
                    var _count = parseInt(data.exist);
                    if (_count > 0) {
                        var _raw = data.vals;
                        $('#dependant_value').removeAttr('disabled');
                        $.each(_raw, function(index,value){
                            _list += '<option value="'+_raw[index]['field_value_id']+'">'+_raw[index]['field_value']+'</option>';
                        });
                        $('#dependant_value').html(_list);
                        console.log('count '+_count);
                    }
                    else {
                        $('#dependant_value').attr('disabled', true);
                    }
                },
                error : function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });
        }
        else {
            $('#dependant_value').attr('disabled', true);
        }
    }
{/block}
