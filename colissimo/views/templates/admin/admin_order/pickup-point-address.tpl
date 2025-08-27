{*
* 2007-2024 PrestaShop
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
*  @author     PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2024 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="colissimo-select-pickup-bloc">
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet'/>
    {if isset($colissimo_pickup_point) && $colissimo_pickup_point->id}
        <div id="selected-address" class="col offset-sm-4">
            <button type="button" class="btn btn-primary colissimo-select-pickup-point"
                    id="colissimo-select-pickup-point">{l s='Choose another pickup point' mod='colissimo'}</button>
            <br/><br/>
            <span class="h4">{l s='Pickup-point address' mod='colissimo'}</span>
            <br/>
            <div class="colissimo-pickup-point-address">
                {$colissimo_pickup_point->company_name|escape:'htmlall':'UTF-8'}<br>
                {$colissimo_pickup_point->address1|escape:'htmlall':'UTF-8'}<br>
                {if $colissimo_pickup_point->address2}{$colissimo_pickup_point->address2|escape:'htmlall':'UTF-8'}
                    <br>
                {/if}
                {if $colissimo_pickup_point->address3}{$colissimo_pickup_point->address3|escape:'htmlall':'UTF-8'}
                    <br>
                {/if}
                {$colissimo_pickup_point->zipcode} {$colissimo_pickup_point->city|escape:'htmlall':'UTF-8'}<br>
                {$colissimo_pickup_point->country|escape:'htmlall':'UTF-8'}<br>
            </div>
        </div>
    {else}
        <div class="select-pickup-point col offset-sm-4">
            <button type="button" class="btn btn-primary colissimo-select-pickup-point"
                    id="colissimo-select-pickup-point">{l s='Select a pickup point' mod='colissimo'}</button>

        </div>
    {/if}
</div>

<script type="text/javascript">
    {literal}
    var colissimoToken = '{/literal}{$colissimo_widget_token|escape:'htmlall':'UTF-8'}{literal}';
    var colissimoPreparationTime = {/literal}{$preparation_time|intval}{literal};
    var widgetLang = "{/literal}{$colissimo_widget_lang|escape:'htmlall':'UTF-8'}{literal}";
    var colissimoDeliveryAddress = {
        address: "{/literal}{$delivery_address.address|strip_tags|addslashes}{literal}",
        zipcode: "{/literal}{$delivery_address.zipcode|escape:'htmlall':'UTF-8'}{literal}",
        city: "{/literal}{$delivery_address.city|strip_tags|addslashes}{literal}",
        isoCountry: "{/literal}{$delivery_address.iso_country|escape:'htmlall':'UTF-8'}{literal}"
    };
    $('.colissimo-select-pickup-point').off('click').on('click', function (e) {
        e.preventDefault();
        $('#colissimo-widget-container').frameColissimoOpen({
            "ceLang": widgetLang,
            "callBackFrame": 'callBackFrame',
            "URLColissimo": "https://ws.colissimo.fr",
            "ceCountryList": colissimoDeliveryAddress['isoCountry'],
            "ceCountry": colissimoDeliveryAddress['isoCountry'],
            "dyPreparationTime": colissimoPreparationTime,
            "ceAddress": colissimoDeliveryAddress['address'],
            "ceZipCode": colissimoDeliveryAddress['zipcode'],
            "ceTown": colissimoDeliveryAddress['city'],
            "token": colissimoToken
        });
        $('.colissimo-back-widget').modal('show');
        setTimeout(function () {
            colissimo_widget_map.resize();
        }, 500);
    });
    function callBackFrame(point) {
        let cartId = $('input[id=cart_summary_cart_id], input[id=id_cart]').val()
        let infoSelectedPoint = new Object({
            colissimo_id: point['identifiant'],
            company_name: point['nom'],
            address1: point['adresse1'],
            address2: point['adresse2'],
            address3: point['adress3'],
            city: point['localite'],
            zipcode: point['codePostal'],
            country: point['libellePays'],
            iso_country: point['codePays'],
            product_code: point['typeDePoint'],
            network: point['reseau']
        });
        $('#colissimo-widget-container').frameColissimoClose();
        $('.colissimo-back-widget').modal('hide');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminModules',
                ajax: 1,
                configure: 'colissimo',
                token: adminToken,
                action: 'backOrderSelectPickupPoint',
                infoPoint: JSON.stringify(infoSelectedPoint),
                cartId: cartId,
            }
        }).fail(function (jqXHR, textStatus) {
        }).done(function (data) {
            let carrierForm = $('.js-shipping-form, #carrier_form');
            $(".colissimo-select-pickup-bloc").remove();
            $(data.html_result).insertAfter(carrierForm);
        }).always(function (data) {
        });
    }
    $(document).on('click', '.widget_colissimo_close', function () {
        $('#colissimo-widget-container').frameColissimoClose();
        $('.colissimo-back-widget').modal('hide');
    });
    $(document).on('click', '.colissimo-back-widget', function (e) {
        const popupQuerySelector = "#colissimo-widget-container";
        const isClosest = e.target.closest(popupQuerySelector);
        if (isClosest == null && $('#colissimo-widget-container').html().length != 0) {
            $('#colissimo-widget-container').frameColissimoClose();
            $('.colissimo-back-widget').modal('hide');
        }
    });
    {/literal}
</script>