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
var iti;

function initMobileField() {
    var input = document.querySelector("#colissimo-pickup-mobile-phone");
    var onlyCountries;
    var allowDropDown;
    var isoDelivery;
    if (typeof colissimoDeliveryAddress === 'undefined') {

        return;
    }
    isoDelivery = colissimoDeliveryAddress['isoCountry'];
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
    if (input !== null) {
        iti = window.intlTelInput(input, {
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
        var handleChange = function () {
           // if (isoDelivery !== 'FR' && isoDelivery !== 'BE') {
                if (iti.isValidNumber()) {
                    jQuery('.js-colissimo-mobile-valid').show();
                    jQuery('.js-colissimo-mobile-invalid').hide();
                    jQuery('.js-colissimo-is-mobile-valid').val('1');
                } else {
                    jQuery('.js-colissimo-mobile-valid').hide();
                    jQuery('.js-colissimo-mobile-invalid').show();
                    jQuery('.js-colissimo-is-mobile-valid').val('0');
                }
          //  }
        };

        input.addEventListener('change', handleChange);
        input.addEventListener('keyup', handleChange);
        input.addEventListener('countrychange', function () {
            handleChange();
        });
        iti.promise.then(function () {
            handleChange();
        });

        return iti;
    }
}

function callBackFrame(point) {
    var mobilePhoneToStore;
    var mobilePhoneToDisplay;

    if (iti === undefined) {
        mobilePhoneToStore = mobilePhone;
    } else {
        mobilePhoneToStore = iti.getNumber();
    }
    mobilePhoneToDisplay = mobilePhoneToStore;
    var infoPoint = new Object({
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
        network: point['reseau'],
        mobilePhone: mobilePhoneToStore
    });

    jQuery('.colissimo-front-widget').modal('hide');
    jQuery('#colissimo-widget-container').frameColissimoClose();
    jQuery('#checkout-delivery-step').addClass('-current js-current-step');
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: colissimoAjaxWidget + 'index.php',
        data: {
            fc: 'module',
            module: 'colissimo',
            controller: 'widget',
            ajax: 1,
            action: 'selectPickupPoint',
            infoPoint: JSON.stringify(infoPoint),
        }
    }).fail(function (jqXHR, textStatus) {
    }).done(function (data) {
        jQuery('.colissimo-pickup-point-address').html(data.html_result);
        initMobileField();
        blockFixedNumber();
        iti.setNumber(mobilePhoneToDisplay);
    }).always(function (data) {
    });
}

function callBackFrameOSM(point, modal) {
    let mobilePhoneToStore;
    let mobilePhoneToDisplay;

    if (iti === undefined) {
        mobilePhoneToStore = mobilePhone;
    } else {
        mobilePhoneToStore = iti.getNumber();
    }
    mobilePhoneToDisplay = mobilePhoneToStore;
    let infoPoint = new Object({
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
        network: point['reseau'],
        mobilePhone: mobilePhoneToStore
    });
    jQuery('#checkout-delivery-step').addClass('-current js-current-step');
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: colissimoAjaxWidget + 'index.php',
        data: {
            fc: 'module',
            module: 'colissimo',
            controller: 'widget',
            ajax: 1,
            action: 'selectPickupPoint',
            infoPoint: JSON.stringify(infoPoint),
        }
    }).fail(function (jqXHR, textStatus) {
    }).done(function (data) {
        if (modal) {
            data.html_result = data.html_result.replace('colissimo-edit-point', 'colissimo-edit-point-osm-modal');
        } else {
            data.html_result = data.html_result.replace('colissimo-edit-point', 'colissimo-edit-point-osm');
        }
        data.html_result = data.html_result.replace('id="colissimo-select-pickup-point"', '');
        jQuery('.colissimo-pickup-point-address').html(data.html_result);
        initMobileField();
        blockFixedNumber();
        if (!modal) {
            $('#osmDiv').hide()
        } else {
            $('.colissimo-osm-front-widget.modal').modal('hide');
        }
        iti.setNumber(mobilePhoneToDisplay);
    }).always(function (data) {
    });
}

jQuery(document).on('click', 'a.colissimo_widget_liste-points', function (e) {
    e.stopPropagation();
    e.preventDefault();
});

jQuery(document).on('click', '.colissimo-edit-point-osm', function (e) {
    e.stopPropagation();
    e.preventDefault();
    if (checkIsMobile()) {
        jQuery('.osm-mobile').show()
    } else {
        jQuery('.osm-desktop').show()
    }
    jQuery('.colissimo-front-widget').frameColissimoOpenStreetMapReload();
});

jQuery(document).on('click', '.colissimo-edit-point-osm-modal', function (e) {
    e.stopPropagation();
    e.preventDefault();
    let countryList = defineCountryList(colissimoDeliveryAddress)
    $('#colissimo-widget-container').frameColissimoOpenStreetMap({
        "URLColissimo": "https://ws.colissimo.fr",
        "ceAddress": colissimoDeliveryAddress['address'].toUpperCase(),
        "ceZipCode": colissimoDeliveryAddress['zipcode'],
        "ceTown": colissimoDeliveryAddress['city'],
        "ceCountry": countryList,
        "ceCountryList": countryList,
        "dyPreparationTime": colissimoPreparationTime,
        "token": colissimoToken,
    }, true);
});

jQuery(document).on('click', '.colissimo-front-widget', function (e) {
    const popupQuerySelector = "#colissimo-widget-container";
    const isClosest = e.target.closest(popupQuerySelector);
    if (isClosest == null && jQuery('#colissimo-widget-container').html().length != 0) {
        jQuery('#colissimo-widget-container').frameColissimoClose();
        jQuery('.colissimo-front-widget').modal('hide');
    }
});

jQuery(document).on('click', '#colissimo-select-pickup-point', function () {
    let countryList = defineCountryList(colissimoDeliveryAddress)
    if (checkIsMobile()) {
        if (typeof widgetNativeMobile !== "undefined") {
            jQuery('#colissimo-widget-container').frameColissimoOpen({
                "ceLang": widgetLang,
                "callBackFrame": 'callBackFrame',
                "URLColissimo": "https://ws.colissimo.fr",
                "ceCountryList": countryList,
                "ceCountry": countryList,
                "dyPreparationTime": colissimoPreparationTime,
                "ceAddress": colissimoDeliveryAddress['address'].toUpperCase(),
                "ceZipCode": colissimoDeliveryAddress['zipcode'],
                "ceTown": colissimoDeliveryAddress['city'],
                "token": colissimoToken,
                "couleur1": couleur1Mobile,
                "couleur2": couleur2Mobile,
                "font": policeMobile,
            });
            jQuery('.colissimo-front-widget').modal('show');
            setTimeout(function () {
                colissimo_widget_map.resize();
            }, 500);
            jQuery('#widget_colissimo_adresse').keyup(function () {
                jQuery(this).val(jQuery(this).val().toUpperCase());
            });
        } else {
            jQuery('#colissimo-widget-container').frameColissimoOpenStreetMapMobile({
                "URLColissimo": "https://ws.colissimo.fr",
                "ceAddress": colissimoDeliveryAddress['address'].toUpperCase(),
                "ceZipCode": colissimoDeliveryAddress['zipcode'],
                "ceTown": colissimoDeliveryAddress['city'],
                "ceCountry": countryList,
                "ceCountryList": countryList,
                "dyPreparationTime": colissimoPreparationTime,
                "token": colissimoToken,
            }, true, widgetOMSDisplayMapMobile);
        }
    } else {
        if (typeof widgetNative !== "undefined") {
            jQuery('#colissimo-widget-container').frameColissimoOpen({
                "ceLang": widgetLang,
                "callBackFrame": 'callBackFrame',
                "URLColissimo": "https://ws.colissimo.fr",
                "ceCountryList": countryList,
                "ceCountry": countryList,
                "dyPreparationTime": colissimoPreparationTime,
                "ceAddress": colissimoDeliveryAddress['address'].toUpperCase(),
                "ceZipCode": colissimoDeliveryAddress['zipcode'],
                "ceTown": colissimoDeliveryAddress['city'],
                "token": colissimoToken,
                "couleur1": couleur1,
                "couleur2": couleur2,
                "font": police,
            });
            jQuery('.colissimo-front-widget').modal('show');
            setTimeout(function () {
                colissimo_widget_map.resize();
            }, 500);
            jQuery('#widget_colissimo_adresse').keyup(function () {
                jQuery(this).val(jQuery(this).val().toUpperCase());
            });
        } else {
            jQuery('#colissimo-widget-container').frameColissimoOpenStreetMap({
                "URLColissimo": "https://ws.colissimo.fr",
                "ceAddress": colissimoDeliveryAddress['address'].toUpperCase(),
                "ceZipCode": colissimoDeliveryAddress['zipcode'],
                "ceTown": colissimoDeliveryAddress['city'],
                "ceCountry": countryList,
                "ceCountryList": countryList,
                "dyPreparationTime": colissimoPreparationTime,
                "token": colissimoToken,
            }, true);
        }
    }
});

jQuery(document).on('click', '.widget_colissimo_close', function () {
    jQuery('#colissimo-widget-container').frameColissimoClose();
    jQuery('.colissimo-front-widget').modal('hide');
});

jQuery(document).on('click', '#colissimo-opc-phone-validation', function () {
    var mobilePhoneSave = iti.getNumber();
    var isMobileValid = iti.isValidNumber();
    var btnValidation = jQuery('#colissimo-opc-phone-validation');
    var result = jQuery('.js-colissimo-mobile-validation');
    var iso = colissimoDeliveryAddress['isoCountry'];
    if (iso == 'FR' || iso == 'BE') {
        if (jQuery("input[name=colissimo_is_mobile_valid]").val() == 0) {
            isMobileValid = 0;
        } else {
            isMobileValid = 1;
        }
    }
    if (mobilePhoneSave === undefined) {
        mobilePhoneSave = '';
    }

    btnValidation.find('i').removeClass('icon-check').addClass('icon-spinner icon-spin');
    result.removeClass('colissimo-mobile-validation-success').removeClass('colissimo-mobile-validation-error').text('');

    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: colissimoAjaxWidget + 'index.php',
        data: {
            fc: 'module',
            module: 'colissimo',
            controller: 'widget',
            ajax: 1,
            action: 'saveMobilePhoneOpc',
            mobilePhone: mobilePhoneSave,
            isMobileValid: isMobileValid ? 1 : 0,
        }
    }).fail(function (jqXHR, textStatus) {
    }).done(function (data) {
        result.text(data.text_result);
        if (!data.errors) {
            result.addClass('colissimo-mobile-validation-success');
            //location.reload(true);
        } else {
            result.addClass('colissimo-mobile-validation-error');
        }
    }).always(function (data) {
        btnValidation.find('i').addClass('icon-check').removeClass('icon-spinner icon-spin');
    });

});

function defineCountryList(colissimoDeliveryAddress) {
    if (colissimoDeliveryAddress['isoCountry'] == 'MC') {
        return 'FR';
    } else {
        return colissimoDeliveryAddress['isoCountry'];
    }
}

function blockFixedNumber() {
    if (typeof colissimoDeliveryAddress === 'undefined') {
        return;
    }
    if (iti !== undefined) {
        var iso = iti.getSelectedCountryData().iso2.toUpperCase;
    } else {
        var iso = colissimoDeliveryAddress['isoCountry'];
    }
    var inputMobile = jQuery("#colissimo-pickup-mobile-phone");
    if (inputMobile.length) {
        if (iso == 'FR' || iso == 'BE') {
            var value = inputMobile.val();
            if (value.length !== 0) {
                value = inputMobile.val().replace(/ /g, '');
            }
            CheckInputMobile(value);
            inputMobile.keyup(function (e) {
                CheckInputMobile(value);
            });
        }
    }
}

function CheckInputMobile(value) {
    var iso = colissimoDeliveryAddress['isoCountry'];
    var validformat = 0;
    var length = 9;
    if (iso == 'FR') {
        value = value.replace('+33', '');
        if (value.substr(0, 1) == 7 || value.substr(0, 1) == 6) {
            validformat = 1;
        }
        if (value.substr(0, 2) == 07 || value.substr(0, 2) == 06) {
            validformat = 1;
            length = 10;
        }
    }
    if (iso == 'BE') {
        var list = ['11111111', '22222222', '33333333', '44444444', '55555555', '66666666', '77777777', '88888888', '99999999', '12345678', '23456789'];
        value = value.replace('+32', '');
        if (value.substr(0, 1) == 4 && list.indexOf(value.substr(1, 8)) == -1) {
            validformat = 1;
        }
        if (value.substr(0, 2) == 04 && list.indexOf(value.substr(1, 8)) == -1) {
            validformat = 1;
            length = 10;
        }
    }
    if (validformat == 1 && value.length == length) {
        jQuery('.js-colissimo-mobile-valid').show();
        jQuery('.js-colissimo-mobile-invalid').hide();
        jQuery('.js-colissimo-is-mobile-valid').val('1');
    } else {
        jQuery('.js-colissimo-mobile-valid').hide();
        jQuery('.js-colissimo-mobile-invalid').show();
        jQuery('.js-colissimo-is-mobile-valid').val('0');
    }
}

function checkIsMobile() {
    let isMobile = false; //initiate as false
    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {
        isMobile = true;
    }
    return isMobile;
}

$(document).ready(function () {
    var colissimoFrontWidget17 = jQuery('.colissimo-front-widget-17');
    colissimoFrontWidget17.appendTo('body');
    if (colissimoFrontWidget17.size() > 0) {
       iti = initMobileField();
    }
    if (typeof colissimoDeliveryAddressDDP !== 'undefined') {
        initMobileFieldDDP();
    }
    blockFixedNumber();
    if (typeof widgetInModalMobile !== 'undefined' && !widgetInModalMobile && checkIsMobile()) {
        var countryList = defineCountryList(colissimoDeliveryAddress);
        jQuery('#colissimo-widget-container').frameColissimoOpenStreetMapMobile({
            "URLColissimo": "https://ws.colissimo.fr",
            "ceAddress": colissimoDeliveryAddress['address'].toUpperCase(),
            "ceZipCode": colissimoDeliveryAddress['zipcode'],
            "ceTown": colissimoDeliveryAddress['city'],
            "ceCountry": countryList,
            "ceCountryList": countryList,
            "dyPreparationTime": colissimoPreparationTime,
            "token": colissimoToken,
        }, false, widgetOMSDisplayMapMobile);
        jQuery('#colissimo-select-pickup-point').hide()
    }
    if (typeof widgetInModal !== 'undefined' && !widgetInModal && !checkIsMobile()) {
        var countryList = defineCountryList(colissimoDeliveryAddress);
        jQuery('#colissimo-widget-container').frameColissimoOpenStreetMap({
            "URLColissimo": "https://ws.colissimo.fr",
            "ceAddress": colissimoDeliveryAddress['address'].toUpperCase(),
            "ceZipCode": colissimoDeliveryAddress['zipcode'],
            "ceTown": colissimoDeliveryAddress['city'],
            "ceCountry": countryList,
            "ceCountryList": countryList,
            "dyPreparationTime": colissimoPreparationTime,
            "token": colissimoToken,
        }, false);
        jQuery('#colissimo-select-pickup-point').hide()
    }

    if (checkIsMobile()) {
        jQuery('.logoColissimo').hide()
    }

    if (!jQuery('#osmDiv').hasClass('osm-desktop') || !jQuery('#osmDiv').hasClass('osm-mobile')) {
        if (checkIsMobile()) {
            jQuery('#osmDiv.osm-desktop').remove()
        } else {
            jQuery('#osmDiv.osm-mobile').remove()
        }
    }

    if (!jQuery('#osmDivModal').hasClass('osm-desktop') || !jQuery('#osmDivModal').hasClass('osm-mobile')) {
        if (checkIsMobile()) {
            jQuery('#osmDivModal.osm-desktop').remove()
        } else {
            jQuery('#osmDivModal.osm-mobile').remove()
        }
    }

    if (jQuery('#osmDiv').hasClass('osm-desktop') && jQuery('#osmDiv').hasClass('osm-mobile')) {
        if (checkIsMobile()) {
            jQuery('#osmDiv').removeClass('osm-desktop')
            jQuery('.osmContent').remove()
        } else {
            jQuery('#osmDiv').removeClass('osm-mobile')
            jQuery('.osmContentMobile').remove()
        }
    }

    if (jQuery('#osmDivModal').hasClass('osm-desktop') && jQuery('#osmDivModal').hasClass('osm-mobile')) {
        if (checkIsMobile()) {
            jQuery('#osmDivModal').removeClass('osm-desktop')
            jQuery('.osmContent').remove()
        } else {
            jQuery('#osmDivModal').removeClass('osm-mobile')
            jQuery('.osmContentMobile').remove()
        }
    }
});
