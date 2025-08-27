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
    $('#fieldset_0 .panel-heading').click(function() {
        $('#fieldset_0 .form-wrapper').slideToggle();
    });
    $('#fieldset_1_1 .panel-heading').click(function() {
        $('#fieldset_1_1 .form-wrapper').slideToggle();
    });
    $('#fieldset_2_2 .panel-heading').click(function() {
        $('#fieldset_2_2 .form-wrapper').slideToggle();
    });
    $('#fieldset_3_3 .panel-heading').click(function() {
        $('#fieldset_3_3 .form-wrapper').slideToggle();
    });
    $('#fieldset_4_4 .panel-heading').click(function() {
        $('#fieldset_4_4 .form-wrapper').slideToggle();
    });
    $('#fieldset_5_5 .panel-heading').click(function() {
        $('#fieldset_5_5 .form-wrapper').slideToggle();
    });
    $('#fieldset_6_6 .panel-heading').click(function() {
        $('#fieldset_6_6 .form-wrapper').slideToggle();
    });
    id_country=$('#b2bregistration_country').val();
    
    if (typeof id_country !== 'undefined') {
        getStatesByCountry(id_country)
    } else {
        // var formGroup_find_product = $(".form-group").filter(function () {
        //     var labelText = $(this).find("label").text().replace(/\s+/g, '');
        //     // alert(labelText);
        //     return labelText.includes("State");
        // });
        // // if (form)
        // formGroup_find_product.hide();
    }

   
    //Add more prefixes  
    $('#add-prefixes').on('click', function(event) {
        event.preventDefault();
        $.fancybox.open({
            closeClick: false, // prevents closing when clicking INSIDE fancybox 
            href: config_url,
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
                    ajax_token: ajax_token,
                }
            }
        });
    });
});

function getStatesByCountry(id_country) {
    var formGroup_find_product = $(".form-group").filter(function () {
        var labelText = $(this).find("label").text().replace(/\s+/g, '');
        return labelText.includes("State");
    });
    $.ajax({
        url: admin_url,
        type: 'POST',
        dataType:'json',
        data: {
            ajax: true, 
            action: 'getStatesByCountry', 
            id_country: id_country,
        },
        success: function(response) {
            console.log(response);
            var states = response;
            
            var stateSelect = $('#b2bregistration_state');
           

            stateSelect.empty();
            
            if (states.length > 0) {
                stateSelect.append('<option value="' + 0 + '">Please Choose</option>');
                $.each(states, function(index, state) {
                    stateSelect.append('<option value="' + state.id_state + '">' + state.name + '</option>');
                    formGroup_find_product.show();
                });
                if (typeof id_state !== 'undefined') {
                    stateSelect.val(id_state);
                } else {
                    stateSelect.val(0);
                }
            } else {
                stateSelect.append('<option value="">' + '--' + '</option>');
                formGroup_find_product.hide();
            }
        },
        error: function() {
            console.error('Failed to fetch states.');
            formGroup_find_product.hide();
        }
    });
}
// Save new prefixes
$(document).on('submit', "#prefix-form", function(event) {
    event.preventDefault(); //prevent default action 
    var post_url = $(this).attr("action"); //get form action url
    var request_method = $(this).attr("method"); //get form GET/POST method
    var form_data = new FormData(this); //Creates new FormData object
    $.ajax({
        url: post_url,
        type: request_method,
        dataType: "json",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false
    }).done(function(response) {
        showSuccessMessage('Prefix Successfully Added');
        $.fancybox.close();
        location.reload();
    });
});

// Delete prefixes
function deletePrefix(id) {
    $.ajax({
        url: config_url, //controller url
        type: "POST",
        dataType: "json",
        data: {
            ajax: true,
            action: 'deletePrefix', // action catch by controller 
            id_prefix: id,
            ajax_token: ajax_token,
        },
        success: function(result) {
            if (result != '') {
                showSuccessMessage("Prefix Successfully Deleted");
                $("#delete_" + id).remove();
            } else {
                showErrorMessage('Not Deleted');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorMessage('Not Deleted');
        }
    });
}

function deleteCustomer(id) {
    $.ajax({
        url: admin_url, //controller url
        type: "POST",
        dataType: "json",
        data: {
            ajax: true,
            action: 'deletCustomer', // action catch by controller 
            id_customer: id,
            ajax_token: ajax_token,
        },
        success: function(result) {
            if (result != '') {
                showSuccessMessage("Customer Successfully Deleted");
                $("#delete_" + id).remove();
            } else {
                showErrorMessage('Not Deleted');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            showErrorMessage('Not Deleted');
        }
    });
}