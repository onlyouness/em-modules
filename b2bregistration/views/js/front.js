/**
 * 2007-2023 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
$(document).ready(function() {
    var terms = $("#terms").is(':checked');
    if (terms == false) {
        $("#b2b_add_data").attr("disabled", true);
        $("#b2b_data").attr("disabled", true);
    }
    $('.read-b2b-conditions').fancybox({
        'hideOnContentClick': false
    });
    $('#terms').click(function() {
        if ($(this).is(':checked')) {
            $('#b2b_add_data').attr('disabled', false);
            $('#b2b_data').attr('disabled', false);
        } else {
            $('#b2b_add_data').attr('disabled', 'true');
            $('#b2b_data').attr('disabled', 'true');
        }
    });

    $('#newsletter').click(function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    $('#partner-option').click(function() {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });
    $('#b2bregistration_id_country').on('change', function() {
        var id_country = $(this).val();
        if (id_country) {
            $.ajax({
                type: 'POST',
                url: b2bregisteration_action_url, 
                data: { 
                    ajax: true, 
                    action: 'getStatesByCountry', 
                    id_country: id_country 
                },
                dataType: 'json',
                success: function(response) {
                    var state_select = $('#id_state');
                    var state_container = $('#id_state_container');
                    state_select.empty();
                    if (response.length > 0 && (state_select) ) {
                        state_select.append('<option value="0">' + state_selection_text + '</option>');
                        $.each(response, function(index, state) {
                            state_select.append('<option value="' + state.id_state + '">' + state.name + '</option>');
                        });
                        state_select.prop('disabled', false);
                        state_container.show();
                    }
                    else {
                        state_container.hide();
                    }
                }
            });
        } 
    });

});
//Captcha
var onloadCallback = function() {
    grecaptcha.render('Gcaptcha', {
        'sitekey': site_key,
        'callback': VerifyCallback
    });
}

function VerifyCallback() {
    $('#b2b_add_data').slideDown();
    $('#b2b_data').slideDown();
}