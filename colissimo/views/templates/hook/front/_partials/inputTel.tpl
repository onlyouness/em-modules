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

<p class="colissimo-pickup-point-phone">
<span>
  {l s='Please confirm your mobile phone number.' mod='colissimo'}<br>
</span>
</p>

<input type="tel"
       class="fixed-width-md colissimo-mobile-phone"
       id="colissimo-pickup-mobile-phone"
       value="{$mobile_phone|escape:'htmlall':'UTF-8'}"
       name="colissimo_mobile_phone[main]"
       onkeyup="blockFixedNumber()"/>
<img src="{$colissimo_img_path|escape:'htmlall':'UTF-8'}icons/icon_valid.png"
     class="colissimo-mobile-valid js-colissimo-mobile-valid"/>
<img src="{$colissimo_img_path|escape:'htmlall':'UTF-8'}icons/icon_invalid.png"
     class="colissimo-mobile-invalid js-colissimo-mobile-invalid"/>
<input type="hidden" class="js-colissimo-is-mobile-valid" name="colissimo_is_mobile_valid"
       value=""/>


<script type="text/javascript">
    if (typeof colissimoDeliveryAddressDDP === 'undefined') {
        var colissimoDeliveryAddressDDP = {
            address: '{$delivery_addr.address|strip_tags|addslashes nofilter}',
            zipcode: '{$delivery_addr.zipcode|escape:'htmlall':'UTF-8'}',
            city: '{$delivery_addr.city|strip_tags|addslashes nofilter}',
            isoCountry: '{$delivery_addr.iso_country|escape:'htmlall':'UTF-8'}'
        };
    }

    function initMobileFieldDDP() {
        $(".colissimo-mobile-phone").each(function (index, inputtel) {
            var iti;
            var elm = $(this);
            var allowDropDown;
            var isoDelivery;
            if (typeof colissimoDeliveryAddressDDP === 'undefined') {
                return
            }
            isoDelivery = colissimoDeliveryAddressDDP['isoCountry'];
            if (isoDelivery == 'MC') {
                isoDelivery = 'FR';
            }
            if (isoDelivery == 'BE') {
                onlyCountries = ['BE'];
                allowDropDown = false;
            } else {
                onlyCountries = [];
                allowDropDown = true;
            }
            if (inputtel !== null) {
                iti = window.intlTelInput(inputtel, {
                    utilsScript: colissimoAjaxWidget + 'modules/colissimo/views/js/utils.js',
                    initialCountry: isoDelivery,
                    nationalMode: true,
                    separateDialCode: true,
                    hiddenInput: 'full',
                    preferredCountries: [],
                    onlyCountries: onlyCountries,
                    allowDropdown: allowDropDown,
                    customPlaceholder: typeof fctCustomPlaceholder === 'function' ? function (selectedCountryPlaceholder, selectedCountryData) {
                        return fctCustomPlaceholder(selectedCountryPlaceholder, selectedCountryData)
                    } : '',
                });
                var handleChangeTel = function () {
                    if (iti.isValidNumber()) {
                        jQuery('.js-colissimo-mobile-valid').show();
                        jQuery('.js-colissimo-mobile-invalid').hide();
                        jQuery('.js-colissimo-is-mobile-valid').val('1');
                    } else {
                        jQuery('.js-colissimo-mobile-valid').hide();
                        jQuery('.js-colissimo-mobile-invalid').show();
                        jQuery('.js-colissimo-is-mobile-valid').val('0');
                    }
                    $('.colissimo-mobile-phone').val(elm.val());
                };
                inputtel.addEventListener('change', handleChangeTel);
                inputtel.addEventListener('keyup', handleChangeTel);
                inputtel.addEventListener('countrychange', function () {
                    handleChangeTel();
                });
                iti.promise.then(function () {
                    handleChangeTel();
                });

                return iti;
            }
        });
    };

</script>
