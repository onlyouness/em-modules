/*
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2024 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

$(document).ready(function () {
    $('#submit-create-labels').off('click').on('click', function (e) {
        e.preventDefault();
        startCreateLabels();
    });
    $(document).on('click', '.btn-download', function (e) {
        e.preventDefault();

        $('#colissimo-form-documents').submit();
    });

    $(document).on('click', '.btn-print', function (e) {
        e.preventDefault();

        var form = $('#colissimo-form-documents');

        $(this).toggleClass('disabled');
        $(this).find('i').toggleClass('icon-print icon-spin icon-spinner');
        printAllDocuments(form, $(this));
    });

    $(document).on('click', '.btn-print-thermal', function (e) {
        e.preventDefault();

        var form = $('#colissimo-form-documents');

        $(this).toggleClass('disabled');
        $(this).find('i').toggleClass('icon-print icon-spin icon-spinner');
        printAllThermalDocuments(form, $(this));
    });

});

function startCreateLabels() {
    var ordersList = [];
    var files = [];
    $('#table-colissimo_order input[type=checkbox]').each(function () {
        var idColissimoOrder = $(this).val();
        if (this.checked) {
            ordersList.push(idColissimoOrder);
        }
    });
    ordersList.reverse();
    if (ordersList.length !== 0) {
        $('#submit-create-labels').toggleClass('disabled');
        $('html, body').animate({scrollTop: 0}, 'slow');
        $('#colissimo-process').show(200);
        processColissimoOrdersLabels(ordersList, files, ordersList.length);
    } else {
        showErrorMessage(noOrdersMessage);
    }
}

function processColissimoOrdersLabels(ordersList, files, ordersCount, form) {
    var idColissimoOrder = parseInt(ordersList.pop());
    if (idColissimoOrder) {
        console.log(idColissimoOrder);
        var data = {
            controller: 'AdminColissimoOrders',
            ajax: 1,
            token: token,
            action: 'createColissimoLabels',
            id_colissimo_order: idColissimoOrder
        };
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php?' + $.param(data),
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).done(function (data) {
            if (data.id_label) {
                if (data.id_label) {
                    files.push(data.id_label);
                }
                if (data.id_return_label) {
                    files.push(data.id_return_label);
                }
            }
            processColissimoOrdersLabels(ordersList, files, ordersCount, form);
        });
    } else {
        $('html, body').animate({scrollTop: 0}, 'slow');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoOrders',
                ajax: 1,
                token: token,
                action: 'displayHeaderResult',
                label_ids: JSON.stringify(files)
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).done(function (data) {
            if ($('#colissimo-form-documents').length) {
                var ids = $('input[name=colissimo_label_ids]').val();
                var jsonIds = JSON.parse(ids);
                console.debug(jsonIds);
                jsonIds = jsonIds.concat(JSON.parse(data.labels_ids));
                var jsonIdsUnique = jsonIds.filter(removeDupplicates);
                console.debug(jsonIds);
                console.debug(JSON.stringify(jsonIdsUnique));
                $('input[name=colissimo_label_ids]').val(JSON.stringify(jsonIdsUnique));
            } else {
                $('#colissimo-process-result').html(data.result_html);
            }
        }).always(function (data) {
            $('#colissimo-process').hide(200);
            $('#submit-create-labels').toggleClass('disabled');
        });
    }
}