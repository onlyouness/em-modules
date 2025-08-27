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
jQuery.extend(jQuery.fn, {
    frameColissimoOpenStreetMap: async function (paramsFromPS, isModal) {
        modal = isModal
        map = await initMap(paramsFromPS);
        if (modal) {
            $('.colissimo-osm-front-widget.modal').modal('show');
        }
    },

    frameColissimoOpenStreetMapMobile: async function (paramsFromPS, isModal, displayMap) {
        modal = isModal
        displayMapPDR = displayMap
        map = await initMap(paramsFromPS);
        if (modal) {
            $('.colissimo-osm-front-widget.modal').modal('show');
        }
    },

    frameColissimoOpenStreetMapReload: async function () {
        await reloadMap(params);
    }
});

$(document).on('click', '#buttonSearchOsm', async function (e) {
    const recherche = $('#divInput input').val();
    let tab = recherche.split(',');
    if (tab != null && tab.length > 0) {
        params.ceAddress = tab[0].trim();
    }
    if (tab != null && tab.length > 1) {
        if (tab[1].match("[0-9]{4,5}") != null) {
            params.ceZipCode = tab[1].match("[0-9]{4,5}")[0].trim();
        }
        if (tab[1].match("[A-Za-z -]+[0-9]*$") != null) {
            params.ceTown = tab[1].match("[A-Za-z -]+[0-9]*$")[0].trim();
        }
    }
    if (tab != null && tab.length > 2) {
        params.ceCountry = tab[2].trim();
    }
    $('#divInput').removeClass('active')
    await reloadMap(params);
});

$(document).on('click', '#resultAddress li', function (e) {
    setAutocomplete(e.target.innerText);
    $('#divInput').removeClass('active')
});

$('.colissimo-osm-front-widget').on('hidden.bs.modal', function (e) {
    deleteMap();
    $('#divInput').removeClass('active')
    $('#resultAddress').empty();
})

let map = null;
let resizeObserver;
let markerGroup;
let params;
let modal;
let activePDR = null;
let markerArray = [];
let pdrArray = [];
let displayMapPDR = true;

function resetActivePDR() {
    let li = $('#listPDR > li[data-idnumber=' + activePDR + ']')
    li.find(".btnMoreDetails").text(jsTranslations.moreDetails)
    li.toggleClass("activePDR")
    li.find('.hiddenPDRContent').toggle()
    li.find('.widget_colissimo_icone_coche').toggleClass('rotate')
}

function handlerInputAddress(params) {
    $('#divInput input')
        .val(params.ceAddress + ', ' + params.ceZipCode + ' ' + params.ceTown + ', ' + params.ceCountry)
        .on('input', async function (e) {
            if (e.target.value.length > 2) {
                let addresses = await autocompleteAddress(params.URLColissimo, params.token, e.target.value);
                let resultAddress = $('#resultAddress');
                resultAddress.empty();
                for (let i = 0; i < addresses.length; i++) {
                    let adr = addresses[i].voie + ', ' + addresses[i].cp + ' ' + addresses[i].ville
                    resultAddress.append('<li>' + adr + '</li>');
                }
            } else if (e.target.value.length === 0) {
                $('#resultAddress').empty();
            }
        })
        .on('click', function (e) {
            $('#divInput').addClass('active')
        })
}

function setAutocomplete(adr) {
    $('#divInput input').val(adr);
}

async function reloadMap(params) {
    deleteMap();
    await initMap(params);
}

function deleteMap() {
    if (displayMapPDR) {
        resizeObserver.disconnect();
        map.off()
        map.remove()

    }
    $('#listPDR').empty();
    params = null
    activePDR = null
    map = null;
    resizeObserver = null
    markerGroup = null
    markerArray = []
    pdrArray = []
}

async function initMap(paramsFromPS) {
    const coords = await getLocation(paramsFromPS);
    pdrArray = await getPDR(paramsFromPS);
    const markerStyle = initMarkerStyle();
    initListPDR(pdrArray)
    params = paramsFromPS;
    handlerInputAddress(paramsFromPS);
    markerArray = [];
    if (displayMapPDR) {
        if (map === null) {
            if (modal) {
                map = L.map('map_modal', {
                    center: coords,
                    zoom: 14,
                });
            } else {
                map = L.map('map', {
                    center: coords,
                    zoom: 14,
                });
            }
        }
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);
        markerArray.push(L.marker(coords, {icon: markerStyle[0]}).addTo(map));
        for (let i = 0; i < pdrArray.length; i++) {
            let marker = L.marker([pdrArray[i].coordGeolocalisationLatitude, pdrArray[i].coordGeolocalisationLongitude], {icon: markerStyle[1]}).addTo(map);
            marker.bindPopup("<input type='hidden' class='mapPDRID' value='" + pdrArray[i].identifiant + "'/>" + pdrArray[i].nom);
            marker.on('mouseover', function (e) {
                e.target.setIcon(markerStyle[2])
            });
            marker.on('mouseout', function (e) {
                e.target.setIcon(markerStyle[1])
            });
            marker.on('click', clickMarker);
            markerArray.push(marker);
        }
        initObserver();
        return map;
    }
}

function checkHoursMore7PM(pdr) {
    let regex = /2[0-3]h[0-5][0-9]|19h[0-5][0-9]/

    let hours = hoursToArray(pdr)
    if (hours.length > 0) {
        for (let i = 0; i < hours.length; i++) {
            if (regex.test(hours[i])) {
                return true
            }
        }
    }
}

function generateLiHTML(pdr) {
    let liElement = $('<li data-idnumber="' + pdr.identifiant + '"></li>');

    let pdrContentDiv = $('<div class="PDRContent"></div>');

    let listAddressPDRDiv = $('<div class="listAddressPDR"></div>');
    listAddressPDRDiv.append('<span class="namePDR">' + pdr.nom + '</span>');
    listAddressPDRDiv.append('<span>' + pdr.adresse1 + '</span>');
    if (pdr.adresse2 !== "") {
        listAddressPDRDiv.append('<span>' + pdr.adresse2 + '</span>');
    }
    if (pdr.adresse3 !== "") {
        listAddressPDRDiv.append('<span>' + pdr.adresse3 + '</span>');
    }
    listAddressPDRDiv.append('<span>' + pdr.codePostal + " " + pdr.localite + '</span>');

    pdrContentDiv.append(listAddressPDRDiv);

    if (pdr.distanceEnMetre > 1000) {
        pdrContentDiv.append('<div class="display-flex-end">' + (pdr.distanceEnMetre / 1000).toFixed(2).toString() + ' km</div>');
    } else {
        pdrContentDiv.append('<div class="display-flex-end">' + pdr.distanceEnMetre.toString() + ' m</div>');
    }
    pdrContentDiv.append('<div class="groupMoreDetails">' +
        '<div class="btnMoreDetails">' + jsTranslations.moreDetails + '</div>' +
        '<img class="widget_colissimo_icone_coche" src="https://ws.colissimo.fr/widget-colissimo/images/coche.svg" style="padding-bottom: 5px" alt="">' +
        '</div>');
    let type;
    switch (pdr.typeDePoint) {
        case "A2P":
            type = jsTranslations.relay
            break
        case "BPR":
            type = jsTranslations.postOffice
            break
        case "CMT":
            type = jsTranslations.relay
            break
        default:
            type = pdr.typeDePoint
            break
    }
    pdrContentDiv.append('<div class="display-flex-end">' + type + '</div>');

    liElement.append(pdrContentDiv);

    let hiddenPDRContentDiv = $('<div class="hiddenPDRContent" style="display: none;"></div>');
    let accessiblitySection = null
    let openingHoursSection = $('<span>' + jsTranslations.openingHours + ':</span>');
    let accessibilityList = null
    let openingHoursList = $('<ul id="listHours"></ul>');


    if (pdr.accesPersonneMobiliteReduite || pdr.horairesOuvertureDimanche !== ' ' || pdr.horairesOuvertureSamedi !== ' ' || pdr.loanOfHandlingTool || checkHoursMore7PM(pdr)) {
        accessiblitySection = $('<span>' + jsTranslations.accessibility + ':</span>');
        accessibilityList = $('<ul class="listAccessibility"></ul>');

        if (pdr.accesPersonneMobiliteReduite) {
            accessibilityList.append('<li><svg id="Icon_awesome-wheelchair" data-name="Icon awesome-wheelchair" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24">\n' +
                '\t\t<path id="Icon_awesome-wheelchair-2" data-name="Icon awesome-wheelchair" d="M23.255,18.078l.667,1.344a.75.75,0,0,1-.338,1.005l-3.069,1.542a1.5,1.5,0,0,1-2.024-.706L15.547,15H9a1.5,1.5,0,0,1-1.485-1.288C5.927,2.593,6.018,3.283,6,3A3,3,0,1,1,9.439,5.968L9.658,7.5H15.75a.75.75,0,0,1,.75.75v1.5a.75.75,0,0,1-.75.75H10.087L10.3,12h6.2a1.5,1.5,0,0,1,1.358.862l2.7,5.738,1.7-.86a.75.75,0,0,1,1.005.338ZM14.595,16.5H13.446A5.25,5.25,0,1,1,5.64,11.2L5.2,8.086A8.25,8.25,0,1,0,15.8,19.069Z" fill="#EA690A"></path>\n' +
                '\t<title>' + jsTranslations.disabledAccess + '</title></svg></li>');
        }

        if (pdr.horairesOuvertureDimanche !== ' ') {
            accessibilityList.append('<li><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 22.7 25">\n' +
                '\t <g id="Composant_12_2" data-name="Composant 12 – 2" transform="translate(1 1)">\n' +
                '\t  <g id="Icon_feather-calendar" data-name="Icon feather-calendar" transform="translate(-3 -2)">\n' +
                '\t    <path id="Tracé_846" data-name="Tracé 846" d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z" transform="translate(0 0.3)" fill="none" stroke="#EA690A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>\n' +
                '\t    <path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6" transform="translate(1.95 0)" fill="none" stroke="#EA690A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>\n' +
                '\t     <path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6" transform="translate(0.75 0)" fill="none" stroke="#EA690A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>\n' +
                '\t     <path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7" transform="translate(0 1.2)" fill="none" stroke="#EA690A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>\n' +
                '\t   </g>\n' +
                '\t   <path id="Tracé_852" data-name="Tracé 852" d="M.8,0A.127.127,0,0,1,.71-.038.127.127,0,0,1,.671-.132V-7.568A.127.127,0,0,1,.71-7.661.127.127,0,0,1,.8-7.7h2.75A3.373,3.373,0,0,1,5-7.409a2.252,2.252,0,0,1,.963.82A2.2,2.2,0,0,1,6.3-5.368v3.036a2.2,2.2,0,0,1-.341,1.221A2.252,2.252,0,0,1,5-.292,3.373,3.373,0,0,1,3.553,0ZM2.222-1.386a.049.049,0,0,0,.055.055H3.608a1.061,1.061,0,0,0,.82-.341,1.336,1.336,0,0,0,.325-.913v-2.53a1.3,1.3,0,0,0-.313-.913A1.087,1.087,0,0,0,3.6-6.369H2.277a.049.049,0,0,0-.055.055Z" transform="translate(7 20)" fill="#EA690A"></path>\n' +
                '\t  </g>\n' +
                '\t<title>' + jsTranslations.sunday + '</title></svg></li>')
        }

        if (pdr.horairesOuvertureSamedi !== ' ') {
            accessibilityList.append('<li><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 22.7 25">\n' +
                '\t\t<g id="Composant_11_2" data-name="Composant 11 – 2" transform="translate(1 1)">\n' +
                '\t\t<g id="Groupe_1437" data-name="Groupe 1437" transform="translate(-201.383 -168)">\n' +
                '\t\t<g id="Icon_feather-calendar" data-name="Icon feather-calendar" transform="translate(198.383 166)">\n' +
                '\t\t\t<path id="Tracé_846" data-name="Tracé 846" d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z" transform="translate(0 0.3)" fill="none" stroke="#EA690A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>\n' +
                '\t\t\t<path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6" transform="translate(1.95 0)" fill="none" stroke="#EA690A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>\n' +
                '\t\t\t<path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6" transform="translate(0.75 0)" fill="none" stroke="#EA690A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>\n' +
                '\t\t\t<path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7" transform="translate(0 1.2)" fill="none" stroke="#EA690A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>\n' +
                '\t\t</g>\n' +
                '\t\t\t<path id="Tracé_853" data-name="Tracé 853" d="M3.311.088a3.913,3.913,0,0,1-1.54-.281A2.271,2.271,0,0,1,.754-1,2.1,2.1,0,0,1,.4-2.211v-.242a.127.127,0,0,1,.038-.094.127.127,0,0,1,.093-.038H1.782a.127.127,0,0,1,.094.038.127.127,0,0,1,.039.094v.165a.9.9,0,0,0,.407.743,1.8,1.8,0,0,0,1.1.3,1.3,1.3,0,0,0,.869-.248.776.776,0,0,0,.286-.61A.616.616,0,0,0,4.4-2.547a1.554,1.554,0,0,0-.478-.319q-.3-.137-.962-.379a9.426,9.426,0,0,1-1.249-.517,2.507,2.507,0,0,1-.858-.72A1.811,1.811,0,0,1,.506-5.61,1.986,1.986,0,0,1,.847-6.765a2.171,2.171,0,0,1,.946-.759,3.465,3.465,0,0,1,1.4-.264,3.57,3.57,0,0,1,1.49.3A2.43,2.43,0,0,1,5.7-6.661a2.124,2.124,0,0,1,.369,1.238v.165a.127.127,0,0,1-.039.094.127.127,0,0,1-.094.038H4.675a.127.127,0,0,1-.093-.038.127.127,0,0,1-.039-.094v-.088a1,1,0,0,0-.379-.786,1.548,1.548,0,0,0-1.04-.325,1.316,1.316,0,0,0-.808.22.716.716,0,0,0-.292.605.66.66,0,0,0,.171.462,1.5,1.5,0,0,0,.506.335q.336.148,1.039.4a12.948,12.948,0,0,1,1.227.506,2.444,2.444,0,0,1,.8.655,1.717,1.717,0,0,1,.358,1.128A1.97,1.97,0,0,1,5.368-.512,3.231,3.231,0,0,1,3.311.088Z" transform="translate(208.383 188)" fill="#EA690A"></path>\n' +
                '\t\t</g>\n' +
                '\t\t</g>\n' +
                '\t<title>' + jsTranslations.saturday + '</title></svg></li>')
        }

        if (checkHoursMore7PM(pdr)) {
            accessibilityList.append('<li><svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24">\n' +
                '\t  <g id="Icon_feather-clock" data-name="Icon feather-clock" transform="translate(1 1)">\n' +
                '\t    <path id="Tracé_850" data-name="Tracé 850" d="M24,13A11,11,0,1,1,13,2,11,11,0,0,1,24,13Z" transform="translate(-2 -2)" fill="none" stroke="#EA690A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>\n' +
                '\t    <path id="Tracé_851" data-name="Tracé 851" d="M12,6v6.6l4.4,2.2" transform="translate(-1 -1.6)" fill="none" stroke="#EA690A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>\n' +
                '\t  </g>\n' +
                '\t<title>' + jsTranslations.after7pm + '</title></svg></li>')
        }

        if (pdr.parking) {
            accessibilityList.append('<li><svg id="Composant_10_2" data-name="Composant 10 – 2" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24">\n' +
                '\t\t<g id="Rectangle_563" data-name="Rectangle 563" fill="none" stroke="#EA690A" stroke-width="2">\n' +
                '\t\t\t<rect width="24" height="24" rx="3" stroke="none"></rect>\n' +
                '\t\t\t<rect x="1" y="1" width="22" height="22" rx="2" fill="none"></rect>\n' +
                '\t\t</g>\n' +
                '\t\t<g id="Tracé_845" data-name="Tracé 845" transform="translate(7.048 17.918)" fill="none" stroke-linecap="round">\n' +
                '\t\t\t<path d="M5.95-11.917a4.087,4.087,0,0,1,1.972.459,3.235,3.235,0,0,1,1.317,1.3,3.906,3.906,0,0,1,.468,1.929,3.626,3.626,0,0,1-.485,1.887,3.3,3.3,0,0,1-1.36,1.267,4.342,4.342,0,0,1-2.015.451H3.434a.075.075,0,0,0-.085.085V-.2A.2.2,0,0,1,3.29-.06.2.2,0,0,1,3.145,0H1.156a.2.2,0,0,1-.145-.06A.2.2,0,0,1,.952-.2V-11.713a.2.2,0,0,1,.059-.145.2.2,0,0,1,.145-.059ZM5.593-6.545A1.755,1.755,0,0,0,6.851-7a1.552,1.552,0,0,0,.476-1.182,1.6,1.6,0,0,0-.476-1.207,1.736,1.736,0,0,0-1.258-.459H3.434a.075.075,0,0,0-.085.085V-6.63a.075.075,0,0,0,.085.085Z" stroke="none"></path>\n' +
                '\t\t\t<path d="M 1.156001091003418 -11.91700077056885 C 1.099330902099609 -11.91700077056885 1.051170349121094 -11.8971700668335 1.011500358581543 -11.85750007629395 C 0.9718303680419922 -11.81783008575439 0.952000617980957 -11.7696704864502 0.952000617980957 -11.71300029754639 L 0.952000617980957 -0.2040004730224609 C 0.952000617980957 -0.1473302841186523 0.9718303680419922 -0.09917068481445312 1.011500358581543 -0.05950069427490234 C 1.051170349121094 -0.01983070373535156 1.099330902099609 0 1.156001091003418 0 L 3.145000457763672 0 C 3.20167064666748 0 3.249830722808838 -0.01983070373535156 3.289500713348389 -0.05950069427490234 C 3.329170703887939 -0.09917068481445312 3.349000453948975 -0.1473302841186523 3.349000453948975 -0.2040004730224609 L 3.349000453948975 -4.539000511169434 C 3.349000453948975 -4.595670223236084 3.377330780029297 -4.624000549316406 3.434000492095947 -4.624000549316406 L 5.848000526428223 -4.624000549316406 C 6.607330799102783 -4.624000549316406 7.278830528259277 -4.774170398712158 7.862500667572021 -5.074500560760498 C 8.446170806884766 -5.37483024597168 8.899500846862793 -5.797000408172607 9.222500801086426 -6.341000556945801 C 9.545500755310059 -6.885000228881836 9.707000732421875 -7.514000415802002 9.707000732421875 -8.228000640869141 C 9.707000732421875 -8.953330039978027 9.551170349121094 -9.596500396728516 9.239500999450684 -10.15750026702881 C 8.927830696105957 -10.7185001373291 8.488670349121094 -11.15200042724609 7.922000885009766 -11.45800018310547 C 7.355330467224121 -11.76399993896484 6.698000907897949 -11.91700077056885 5.950000762939453 -11.91700077056885 L 1.156001091003418 -11.91700077056885 M 5.593000888824463 -6.545000553131104 L 3.434000492095947 -6.545000553131104 C 3.377330780029297 -6.545000553131104 3.349000453948975 -6.573330402374268 3.349000453948975 -6.630000591278076 L 3.349000453948975 -9.758000373840332 C 3.349000453948975 -9.814670562744141 3.377330780029297 -9.843000411987305 3.434000492095947 -9.843000411987305 L 5.593000888824463 -9.843000411987305 C 6.114330768585205 -9.843000411987305 6.533670902252197 -9.690000534057617 6.851000785827637 -9.384000778198242 C 7.168330669403076 -9.078000068664551 7.327000617980957 -8.675670623779297 7.327000617980957 -8.177000045776367 C 7.327000617980957 -7.689670562744141 7.168330669403076 -7.295830249786377 6.851000785827637 -6.995500564575195 C 6.533670902252197 -6.695170402526855 6.114330768585205 -6.545000553131104 5.593000888824463 -6.545000553131104 M 1.156001091003418 -13.91700077056885 L 5.950000762939453 -13.91700077056885 C 7.029930591583252 -13.91700077056885 8.013130187988281 -13.68176078796387 8.87229061126709 -13.21781063079834 C 9.777510643005371 -12.72900009155273 10.48927021026611 -12.02615070343018 10.98781108856201 -11.1287899017334 C 11.46503067016602 -10.26981067657471 11.70700073242188 -9.293840408325195 11.70700073242188 -8.228000640869141 C 11.70700073242188 -7.153040409088135 11.44969081878662 -6.174620151519775 10.94221115112305 -5.319920539855957 C 10.42597103118896 -4.450470447540283 9.697680473327637 -3.769570350646973 8.777570724487305 -3.296120643615723 C 7.910830497741699 -2.850130081176758 6.925180435180664 -2.624000549316406 5.848000526428223 -2.624000549316406 L 5.349000453948975 -2.624000549316406 L 5.349000453948975 -0.2040004730224609 C 5.349000453948975 0.3850297927856445 5.11983060836792 0.9385900497436523 4.703710556030273 1.354709625244141 C 4.287590503692627 1.770829200744629 3.734030723571777 2 3.145000457763672 2 L 1.156001091003418 2 C 0.5669708251953125 2 0.01341056823730469 1.770829200744629 -0.4027090072631836 1.354709625244141 C -0.8188295364379883 0.9385900497436523 -1.047999382019043 0.3850297927856445 -1.047999382019043 -0.2040004730224609 L -1.047999382019043 -11.71300029754639 C -1.047999382019043 -12.30203056335449 -0.8188295364379883 -12.8555908203125 -0.4027090072631836 -13.27171039581299 C 0.01341056823730469 -13.68782997131348 0.5669708251953125 -13.91700077056885 1.156001091003418 -13.91700077056885 Z" stroke="none" fill="#EA690A"></path>\n' +
                '\t\t</g>\n' +
                '\t<title>' + jsTranslations.parking + '</title></svg></li>')
        }
    }

    let openingHours = hoursToArray(pdr)

    for (let [key, value] of Object.entries(openingHours)) {
        let liElement = $('<li></li>');
        liElement.append('<div class="dayHours">' + key + '</div>');

        let hoursFlexDiv = $('<div class="hoursFlex"></div>');
        if (value === " ") {
            value = jsTranslations.closed
        }
        hoursFlexDiv.append('<span>' + value + '</span>');


        liElement.append(hoursFlexDiv);
        openingHoursList.append(liElement);
    }

    let btnSelectPDR = $('<button type="button" onclick="selectPDR(\'' + pdr.identifiant + '\')" class="btnSelectPDR">' + jsTranslations.select + '</button>');

    hiddenPDRContentDiv.append(accessiblitySection);
    hiddenPDRContentDiv.append(accessibilityList);
    hiddenPDRContentDiv.append(openingHoursSection);
    hiddenPDRContentDiv.append(openingHoursList);
    hiddenPDRContentDiv.append(btnSelectPDR);

    liElement.append(hiddenPDRContentDiv);

    return liElement
}

function initListPDR(pdr) {
    let listPDR = $('#listPDR')
    pdr.forEach(el => {
        let liElement = generateLiHTML(el)
        listPDR.append(liElement)
    })
}

function selectPDR(pdr) {
    pdrArray.forEach(el => {
        if (el.identifiant === pdr) {
            callBackFrameOSM(el, modal)
        }
    })
}

function hoursToArray(pdr) {
    let res = []
    res[jsTranslations.monday] = pdr.horairesOuvertureLundi
    res[jsTranslations.tuesday] = pdr.horairesOuvertureMardi
    res[jsTranslations.wednesday] = pdr.horairesOuvertureMercredi
    res[jsTranslations.thursday] = pdr.horairesOuvertureJeudi
    res[jsTranslations.friday] = pdr.horairesOuvertureVendredi
    res[jsTranslations.saturday] = pdr.horairesOuvertureSamedi
    res[jsTranslations.sunday] = pdr.horairesOuvertureDimanche

    return res
}

function autocompleteAddress(urlColissimo, token, value) {
    return $.ajax({
        type: "GET",
        encoding: "UTF-8",
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        dataType: "json",
        url: urlColissimo + '/widget-colissimo/rest/GetAddresses.rest',
        data: "chaineRecherche=" + value.toUpperCase() + '&token=' + token,
        error: function (resultat, statut, erreur) {
            console.log(statut);
            console.log(erreur);
        }
    });
}

function clickMarker(e) {
    let id = e.target._popup._content.match(/<input type='hidden' class='mapPDRID' value='(.*)'\/>/)[1]
    setActivePDR(id, "marker")
    clickZoom(e)
}

function clickZoom(e) {
    map.setView(e.target.getLatLng(), 16);
}

function setActiveMarker(id) {
    let marker = markerArray.find(el => {
        return el.options.icon.options.iconUrl === "https://ws.colissimo.fr/widget-colissimo/images/markerPDR.svg" && el.getPopup().getContent().includes(id)
    })
    marker.openPopup()
    clickZoom({
        target: marker
    })
}

function setActiveLi(id) {
    let li = $('#listPDR').find('li[data-idnumber="' + id + '"]')
    li.find(".btnMoreDetails").text(jsTranslations.lessDetails)
    li.toggleClass("activePDR")
    li.find('.hiddenPDRContent').toggle()
    li.find('.widget_colissimo_icone_coche').toggleClass('rotate')
}

function setActivePDR(id, trigger) {
    resetActivePDR();
    if (activePDR === id) {
        if (typeof map !== 'undefined') {
            if (trigger === "list") {
                let marker = markerArray.find(el => {
                    return el.options.icon.options.iconUrl === "https://ws.colissimo.fr/widget-colissimo/images/markerPDR.svg" && el.getPopup().getContent().includes(id)
                })
                marker.closePopup()
            }
            unzoomMap()
        }
        activePDR = null;
        return
    }
    switch (trigger) {
        case "marker":
            if (typeof widgetOSMDisplaySuperposed !== 'undefined' && widgetOSMDisplaySuperposed && checkIsMobile()) {
                clickToggleMap()
            }
            setActiveLi(id);
            scrollToLi(id);
            activePDR = id;
            break;
        case "list":
            setActiveLi(id);
            if (typeof map !== 'undefined') {
                setActiveMarker(id);
            }
            scrollToLi(id)
            activePDR = id;
            break;
    }
}

function unzoomMap() {
    map.invalidateSize();
    markerGroup = new L.featureGroup(markerArray);
    map.fitBounds(markerGroup.getBounds());
}

function scrollToLi(id) {
    let listPDR = $('#listPDR')
    let li = listPDR.find('li[data-idnumber="' + id + '"]')
    let position = li.position().top - listPDR.position().top + listPDR.scrollTop() - 10;
    listPDR.animate({
        scrollTop: position
    }, 500);
}

function initMarkerStyle() {
    let markerStyle = []
    markerStyle.push(L.icon({
        iconUrl: 'https://ws.colissimo.fr/widget-colissimo/images/ionic-md-locate.svg',

        iconSize: [25, 25], // size of the icon
        iconAnchor: [10.5, 10.5], // point of the icon which will correspond to marker's location
        popupAnchor: [0, -10.5] // point from which the popup should open relative to the iconAnchor
    }));
    markerStyle.push(L.icon({
        iconUrl: 'https://ws.colissimo.fr/widget-colissimo/images/markerPDR.svg',

        iconSize: [25, 40], // size of the icon
        iconAnchor: [12.5, 40], // point of the icon which will correspond to marker's location
        popupAnchor: [0, -40] // point from which the popup should open relative to the iconAnchor
    }));
    markerStyle.push(L.icon({
        iconUrl: 'https://ws.colissimo.fr/widget-colissimo/images/markerPDRS.svg',

        iconSize: [25, 40], // size of the icon
        iconAnchor: [12.5, 40], // point of the icon which will correspond to marker's location
        popupAnchor: [0, -40] // point from which the popup should open relative to the iconAnchor
    }));
    return markerStyle;
}

function getLocation(params) {
    const adr = params.ceAddress.replace("’", "'") + ', ' + params.ceZipCode + ' ' + params.ceTown.replace("’", "'") + ', ' + params.ceCountry;
    return $.ajax({
        type: "GET",
        async: false, // Mode synchrone
        encoding: "UTF-8",
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        dataType: "json",
        url: params.URLColissimo + '/widget-colissimo/rest/GetLocation.rest',
        data: "data=" + window.btoa(JSON.stringify({addresse: adr, token: params.token})),
        success: function (msg) {
            if (msg.lng == null) {
                return [48.8579, 2.3446]
            } else {
                return [msg.lat, msg.lng]
            }

        },
        error: function (resultat, statut, erreur) {
            console.log(statut);
            console.log(erreur);
        }
    });
}

function initObserver() {
    resizeObserver = new ResizeObserver(() => {
        unzoomMap();
    });
    if (modal) {
        resizeObserver.observe(document.getElementById('map_modal'));
    } else {
        resizeObserver.observe(document.getElementById('map'));
    }
}

async function getPDR(params) {
    let typeOfPoint;
    let maxPoint;
    let res = [];
    if (checkIsMobile()) {
        typeOfPoint = $('#typeOfPointMobile').val()
        maxPoint = widgetMaxPointMobile;
    } else {
        typeOfPoint = $('#typeOfPointDesktop').val()
        maxPoint = widgetMaxPoint;
    }
    await $.ajax({
        type: 'GET',
        encoding: "UTF-8",
        url: params.URLColissimo + '/widget-colissimo/rest/GetPointsRetraitGET.rest',
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        dataType: 'json',
        data: "data=" + window.btoa(JSON.stringify({
            addresse: params.ceAddress.replace("’", "'"),
            codePostal: params.ceZipCode,
            ville: params.ceTown.replace("’", "'"),
            pays: params.ceCountry,
            dyPreparationTime: params.dyPreparationTime,
            dyWeight: params.dyWeight,
            token: params.token
        })),
        success: function (data) {
            data.every(el => {
                if (res.length === maxPoint) {
                    return false
                }
                if (typeOfPoint.includes(el.typeDePoint)) {
                    res.push(el)
                }
                return true
            })
            return res
        },
        error: function (resultat, statut, erreur) {
            console.log(statut);
            console.log(erreur);
        }
    });
    return res
}

$(document).on('click', '#listPDR > li', function () {
    let li = $(this)
    let idNumber = li.attr('data-idnumber')
    setActivePDR(idNumber, "list")
});

function clickToggleMap() {
    $('#listPDR').toggle()
    if (modal) {
        $('#map_modal').toggle()
    } else {
        $('#map').toggle()
    }
    $('.widget_colissimo_map_img').toggle()
}

$(document).on('click', '#widget_colissimo_map_liste', function () {
    clickToggleMap()
})


