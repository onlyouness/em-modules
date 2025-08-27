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
<script type="text/javascript">
    if (typeof colissimoAjaxWidget === 'undefined') {
        var colissimoAjaxWidget = prestashop.urls.base_url;
    }
</script>
{if isset($colissimo_service) && $colissimo_service == "RELAIS"}
    <script type="text/javascript">
        const colissimoToken = '{$colissimo_widget_token|escape:'htmlall':'UTF-8'}';
        const colissimoPreparationTime = {$preparation_time|intval};
        const widgetLang = "{$colissimo_widget_lang|escape:'htmlall':'UTF-8'}";
        const colissimoDeliveryAddress = {
              address: '{$delivery_addr.address|strip_tags|addslashes nofilter}',
            zipcode: '{$delivery_addr.zipcode|escape:'htmlall':'UTF-8'}',
            city: '{$delivery_addr.city|strip_tags|addslashes nofilter}',
            isoCountry: '{$delivery_addr.iso_country|escape:'htmlall':'UTF-8'}'
        };
        const mobilePhone = "{$mobile_phone|escape:'htmlall':'UTF-8'}";
        {if isset($widget_native)}
        const widgetNative = {$widget_native|intval};
        const couleur1 = '{$widget_color_1|escape:'htmlall':'UTF-8'}';
        const couleur2 = '{$widget_color_2|escape:'htmlall':'UTF-8'}';
        const police = '{$widget_police|escape:'htmlall':'UTF-8'}';
        {else}
        const widgetOSM = {$widget_osm|intval};
        const widgetInModal = {$widget_osm_in_modal|intval};
        const widgetMaxPoint = {$widget_osm_max_point|intval};
        {/if}
        {if isset($widget_native_mobile)}
        const widgetNativeMobile = {$widget_native_mobile|intval};
        const couleur1Mobile = '{$widget_color_1_mobile|escape:'htmlall':'UTF-8'}';
        const couleur2Mobile = '{$widget_color_2_mobile|escape:'htmlall':'UTF-8'}';
        const policeMobile = '{$widget_police_mobile|escape:'htmlall':'UTF-8'}';
        {else}
        const widgetOSMMobile = {$widget_osm_mobile|intval};
        const widgetInModalMobile = {$widget_osm_in_modal_mobile|intval};
        const widgetMaxPointMobile = {$widget_osm_max_point_mobile|intval};
        const widgetOMSDisplayMapMobile = {$widget_osm_display_map_mobile|intval};
        const widgetOSMDisplaySuperposed = {$widget_osm_display_superposed|intval};
        {/if}
    </script>
    {if isset($widget_osm_type_point)}
        <input type="hidden" id="typeOfPointDesktop" value="{$widget_osm_type_point|escape:'htmlall':'UTF-8'}"/>
    {/if}
    {if isset($widget_osm_type_point_mobile)}
        <input type="hidden" id="typeOfPointMobile" value="{$widget_osm_type_point_mobile|escape:'htmlall':'UTF-8'}"/>
    {/if}
    {if isset($widget_native_mobile) || isset($widget_native)}
        <div class="colissimo-front-widget colissimo-front-widget-17 modal fade" style="display:none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div id="colissimo-widget-container"></div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
    <div class="colissimo-pickup-point-address">
        {include file="./_partials/pickup-point-address.tpl"}
    </div>
    {if isset($widget_osm) || isset($widget_osm_mobile)}
        {include file="./_partials/widget-osm.tpl"}
    {/if}
{else}
    {if isset($display_door_codes)}
    <div class="colissimo-address-fields"
         {if !$display_door_codes}style="display:none;"{/if}>
        {include file="./_partials/address-fields.tpl"}
    </div>
    {/if}
    {if (isset($colissimo_ddp) && $colissimo_ddp)}
        <div class="colissimo-tel-field">
            {include file="./_partials/inputTel.tpl"}
        </div>
    {/if}
{/if}
