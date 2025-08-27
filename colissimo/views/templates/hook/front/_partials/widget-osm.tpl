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

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<style>
    .osm-mobile #map, .osm-mobile #map_modal {
        height: 600px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
    }

    #closeBtnOsm button {
        background: none;
        border: none;
        font-size: 30px;
        font-weight: 700;
        color: #000;
        cursor: pointer;
    }

    .modal-body {
        display: flex;
    }

    #closeBtnOsm {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .logoColissimo {
        display: flex;
        align-items: center;
    }

    #searchInputOsm {
        border: none;
        width: 100%;
        outline: none;
    }

    #divInput {
        border-top: 1px solid #CDCDCD;
        border-left: 1px solid #CDCDCD;
        border-bottom: 1px solid #CDCDCD;
        border-bottom-left-radius: 4px;
        border-top-left-radius: 4px;
        width: 75%;
        padding: 5px;
        background-color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
    }

    #divInput i {
        padding: 0 10px;
    }

    #buttonSearchOsm {
        border: 1px solid #CDCDCD;
        border-bottom-right-radius: 4px;
        border-top-right-radius: 4px;
        cursor: pointer;
        background-color: white;
        padding: 0 10px;
    }

    #divInput #resultAddress {
        padding: 0;
        opacity: 0;
        pointer-events: none;
        overflow-y: auto;
    }

    #divInput.active #resultAddress {
        opacity: 1;
        pointer-events: auto;
    }

    #divInput.active #resultAddress:not(:empty) {
        padding: 5px 10px;
    }

    #divInput.active {
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    #resultAddress li {
        list-style: none;
        padding: 10px 15px;
        cursor: pointer;
        border-radius: 5px;
    }

    #resultAddress li:hover {
        background-color: #F5F5F5;
    }

    #resultAddress {
        position: absolute;
        top: 100%;
        left: -1px;
        right: -1px;
        background-color: white;
        box-sizing: border-box;
        border-bottom-right-radius: 5px;
        border-bottom-left-radius: 5px;
        z-index: 1001;
        border-bottom: 1px solid #CDCDCD;
        border-left: 1px solid #CDCDCD;
        border-right: 1px solid #CDCDCD;
    }

    #osmDiv {
        margin-bottom: 15px;
        background-color: white;
        border-radius: 5px;
        border: #c3c3c3 solid;
        position: relative;
        z-index: 50;
    }

    #osmDiv.osm-mobile {
        width: 100%;
    }

    #osmDiv.osm-desktop {
        height: 672px;
        width: 150%;
    }

    #listPDR {
        display: flex;
        flex-direction: column;
        padding: 10px;
        overflow: auto;
        height: 520px;
    }

    #osmDiv.osm-mobile {
        width: 100%;
    }

    .PDRContent {
        display: grid;
        grid-template-columns: 10fr 2fr;
        padding-bottom: 5px;
    }

    .display-flex-end {
        display: flex;
        justify-content: end;
        align-items: end;
        text-align: center;
    }

    .btnMoreDetails {
        display: flex;
        align-items: end;
        font-weight: lighter;
    }

    .listAddressPDR {
        display: flex;
        flex-direction: column
    }

    #osm-desktop #map {
        width: 58%;
    }

    #listPDR > li {
        border-bottom: #c3c3c3 solid 1px;
        border-left: white solid 5px;
        padding-left: 5px;
        cursor: pointer;
    }

    #osmDivModal
    #listHours > li {
        display: flex;
        justify-content: space-between;
        padding: 5px;
    }

    .osmContentMobile {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .leaflet-control-attribution {
        display: none;
    }

    .osmContentMobile.mapFirst {
        display: flex;
        flex-direction: column-reverse;
        justify-content: space-between;
        height: 100%;
    }

    .osmContent #map, .osmContent #map_modal {
        width: 58%;
    }

    .hoursFlex {
        display: flex;
        flex-direction: column;
        font-weight: lighter;
    }

    .dayHours {
        display: flex;
        align-items: center;
        font-weight: bold;
        text-transform: capitalize;
    }

    .groupMoreDetails {
        display: flex;
        gap: 5px;
        align-items: flex-end;
    }

    .rotate {
        transform: rotate(180deg);
    }

    .btnSelectPDR {
        padding: 5px;
        border-radius: 5px;
        border: none;
        background-color: #f89622;
        width: 50%;
        align-self: center;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .btnSelectPDR:hover {
        background-color: #e18d28;
    }

    .btnSelectPDR:focus {
        outline: none;
    }


    .hiddenPDRContent {
        display: flex;
        padding-top: 10px;
        flex-direction: column;
        gap: 5px;
    }

    .leftColumnOsm {
        display: flex;
        width: 40%;
        flex-direction: column;
        gap: 5px;
    }

    .activePDR {
        border-left: #F89622 solid 5px !important;
        background-color: #f3f3f3;
    }

    .namePDR {
        font-weight: bold;
    }

    .mapFirst > .listAccessibilityLogo {
        order: 1;
    }

    .mapFirst > #map, .mapFirst > #map_modal {
        order: 2;
    }

    .listAccessibilityLogo {
        display: flex;
        gap: 5px;
        justify-content: center;
        padding-left: 5px;
        padding-right: 5px;
        padding-top: 5px;
        border-bottom: 1px solid #CDCDCD;
        font-weight: lighter;
        font-size: 12px;
    }

    .listAccessibilityLogo > div {
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        align-items: center;
        text-align: center;
    }

    .listAccessibility {
        display: flex;
        gap: 5px;
    }

    #widget_colissimo_map_liste {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background-color: white;
        box-shadow: 0 0 5px #c3c3c3;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        position: absolute;
        right: 0;
        bottom: 0;
    }

</style>
<script>
    let jsTranslations = {
        "moreDetails": "{l s='More details' mod='colissimo'}",
        "lessDetails": "{l s='Less details' mod='colissimo'}",
        "relay": "{l s='Relay' mod='colissimo'}",
        "postOffice": "{l s='Post office' mod='colissimo'}",
        "closed": "{l s='Closed' mod='colissimo'}",
        "openingHours": "{l s='Opening hours' mod='colissimo'}",
        "monday": "{l s='Monday' mod='colissimo'}",
        "tuesday": "{l s='Tuesday' mod='colissimo'}",
        "wednesday": "{l s='Wednesday' mod='colissimo'}",
        "thursday": "{l s='Thursday' mod='colissimo'}",
        "friday": "{l s='Friday' mod='colissimo'}",
        "saturday": "{l s='Saturday' mod='colissimo'}",
        "sunday": "{l s='Sunday' mod='colissimo'}",
        "select": "{l s='Select' mod='colissimo'}",
        "accessibility": "{l s='Accessibility' mod='colissimo'}",
        "parking": "{l s='Parking' mod='colissimo'}",
        "disabledAccess": "{l s='Disabled access' mod='colissimo'}",
        "after7pm": "{l s='After 7pm' mod='colissimo'}",
    };
</script>
{if (isset($widget_osm_in_modal_mobile) && $widget_osm_in_modal_mobile) || (isset($widget_osm_in_modal) && $widget_osm_in_modal)}
    <div class="colissimo-osm-front-widget modal fade {if isset($widget_osm_in_modal_mobile) && $widget_osm_in_modal_mobile}osm-mobile{/if} {if isset($widget_osm_in_modal) && $widget_osm_in_modal}osm-desktop{/if}"
         style="display:none;"
         id="osmDivModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="logoColissimo"><img src="https://ws.colissimo.fr/widget-colissimo/images/Colissimo.svg"
                                                    alt="logo colissimo"></div>
                    <div style="flex-grow: 1; display: flex; justify-content: center">
                        <div id="divInput">
                            <div style="display: flex; align-items: center; width: 100%">
                                <i><img src="https://ws.colissimo.fr/widget-colissimo/images/pictoAdresse.svg"
                                        alt="picto adresse"></i>
                                <input type="text" id="searchInputOsm"
                                       placeholder="23 rue de Rivoli, 75004 Paris">
                            </div>
                            <div id="resultAddress"></div>
                        </div>
                        <button id="buttonSearchOsm" type="button"><img
                                    src="https://ws.colissimo.fr/widget-colissimo/images/loupe.svg" alt="loupe">
                        </button>
                    </div>
                    <div id="closeBtnOsm">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                {if isset($widget_osm_in_modal) && $widget_osm_in_modal}
                    <div class="osmContent" style="display: flex">
                        <div class="leftColumnOsm">
                            <div class="listAccessibilityLogo">
                                <div>
                                    <svg id="Icon_awesome-wheelchair" data-name="Icon awesome-wheelchair"
                                         xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                         viewBox="0 0 24 24">
                                        <path id="Icon_awesome-wheelchair-2" data-name="Icon awesome-wheelchair"
                                              d="M23.255,18.078l.667,1.344a.75.75,0,0,1-.338,1.005l-3.069,1.542a1.5,1.5,0,0,1-2.024-.706L15.547,15H9a1.5,1.5,0,0,1-1.485-1.288C5.927,2.593,6.018,3.283,6,3A3,3,0,1,1,9.439,5.968L9.658,7.5H15.75a.75.75,0,0,1,.75.75v1.5a.75.75,0,0,1-.75.75H10.087L10.3,12h6.2a1.5,1.5,0,0,1,1.358.862l2.7,5.738,1.7-.86a.75.75,0,0,1,1.005.338ZM14.595,16.5H13.446A5.25,5.25,0,1,1,5.64,11.2L5.2,8.086A8.25,8.25,0,1,0,15.8,19.069Z"
                                              fill="#EA690A"></path>
                                    </svg>
                                    <span>{l s='Disabled access' mod='colissimo'}</span>
                                </div>
                                <div>
                                    <svg id="Composant_10_2" data-name="Composant 10 – 2"
                                         xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                         viewBox="0 0 24 24">
                                        <g id="Rectangle_563" data-name="Rectangle 563" fill="none" stroke="#EA690A"
                                           stroke-width="2">
                                            <rect width="24" height="24" rx="3" stroke="none"></rect>
                                            <rect x="1" y="1" width="22" height="22" rx="2" fill="none"></rect>
                                        </g>
                                        <g id="Tracé_845" data-name="Tracé 845" transform="translate(7.048 17.918)"
                                           fill="none" stroke-linecap="round">
                                            <path d="M5.95-11.917a4.087,4.087,0,0,1,1.972.459,3.235,3.235,0,0,1,1.317,1.3,3.906,3.906,0,0,1,.468,1.929,3.626,3.626,0,0,1-.485,1.887,3.3,3.3,0,0,1-1.36,1.267,4.342,4.342,0,0,1-2.015.451H3.434a.075.075,0,0,0-.085.085V-.2A.2.2,0,0,1,3.29-.06.2.2,0,0,1,3.145,0H1.156a.2.2,0,0,1-.145-.06A.2.2,0,0,1,.952-.2V-11.713a.2.2,0,0,1,.059-.145.2.2,0,0,1,.145-.059ZM5.593-6.545A1.755,1.755,0,0,0,6.851-7a1.552,1.552,0,0,0,.476-1.182,1.6,1.6,0,0,0-.476-1.207,1.736,1.736,0,0,0-1.258-.459H3.434a.075.075,0,0,0-.085.085V-6.63a.075.075,0,0,0,.085.085Z"
                                                  stroke="none"></path>
                                            <path d="M 1.156001091003418 -11.91700077056885 C 1.099330902099609 -11.91700077056885 1.051170349121094 -11.8971700668335 1.011500358581543 -11.85750007629395 C 0.9718303680419922 -11.81783008575439 0.952000617980957 -11.7696704864502 0.952000617980957 -11.71300029754639 L 0.952000617980957 -0.2040004730224609 C 0.952000617980957 -0.1473302841186523 0.9718303680419922 -0.09917068481445312 1.011500358581543 -0.05950069427490234 C 1.051170349121094 -0.01983070373535156 1.099330902099609 0 1.156001091003418 0 L 3.145000457763672 0 C 3.20167064666748 0 3.249830722808838 -0.01983070373535156 3.289500713348389 -0.05950069427490234 C 3.329170703887939 -0.09917068481445312 3.349000453948975 -0.1473302841186523 3.349000453948975 -0.2040004730224609 L 3.349000453948975 -4.539000511169434 C 3.349000453948975 -4.595670223236084 3.377330780029297 -4.624000549316406 3.434000492095947 -4.624000549316406 L 5.848000526428223 -4.624000549316406 C 6.607330799102783 -4.624000549316406 7.278830528259277 -4.774170398712158 7.862500667572021 -5.074500560760498 C 8.446170806884766 -5.37483024597168 8.899500846862793 -5.797000408172607 9.222500801086426 -6.341000556945801 C 9.545500755310059 -6.885000228881836 9.707000732421875 -7.514000415802002 9.707000732421875 -8.228000640869141 C 9.707000732421875 -8.953330039978027 9.551170349121094 -9.596500396728516 9.239500999450684 -10.15750026702881 C 8.927830696105957 -10.7185001373291 8.488670349121094 -11.15200042724609 7.922000885009766 -11.45800018310547 C 7.355330467224121 -11.76399993896484 6.698000907897949 -11.91700077056885 5.950000762939453 -11.91700077056885 L 1.156001091003418 -11.91700077056885 M 5.593000888824463 -6.545000553131104 L 3.434000492095947 -6.545000553131104 C 3.377330780029297 -6.545000553131104 3.349000453948975 -6.573330402374268 3.349000453948975 -6.630000591278076 L 3.349000453948975 -9.758000373840332 C 3.349000453948975 -9.814670562744141 3.377330780029297 -9.843000411987305 3.434000492095947 -9.843000411987305 L 5.593000888824463 -9.843000411987305 C 6.114330768585205 -9.843000411987305 6.533670902252197 -9.690000534057617 6.851000785827637 -9.384000778198242 C 7.168330669403076 -9.078000068664551 7.327000617980957 -8.675670623779297 7.327000617980957 -8.177000045776367 C 7.327000617980957 -7.689670562744141 7.168330669403076 -7.295830249786377 6.851000785827637 -6.995500564575195 C 6.533670902252197 -6.695170402526855 6.114330768585205 -6.545000553131104 5.593000888824463 -6.545000553131104 M 1.156001091003418 -13.91700077056885 L 5.950000762939453 -13.91700077056885 C 7.029930591583252 -13.91700077056885 8.013130187988281 -13.68176078796387 8.87229061126709 -13.21781063079834 C 9.777510643005371 -12.72900009155273 10.48927021026611 -12.02615070343018 10.98781108856201 -11.1287899017334 C 11.46503067016602 -10.26981067657471 11.70700073242188 -9.293840408325195 11.70700073242188 -8.228000640869141 C 11.70700073242188 -7.153040409088135 11.44969081878662 -6.174620151519775 10.94221115112305 -5.319920539855957 C 10.42597103118896 -4.450470447540283 9.697680473327637 -3.769570350646973 8.777570724487305 -3.296120643615723 C 7.910830497741699 -2.850130081176758 6.925180435180664 -2.624000549316406 5.848000526428223 -2.624000549316406 L 5.349000453948975 -2.624000549316406 L 5.349000453948975 -0.2040004730224609 C 5.349000453948975 0.3850297927856445 5.11983060836792 0.9385900497436523 4.703710556030273 1.354709625244141 C 4.287590503692627 1.770829200744629 3.734030723571777 2 3.145000457763672 2 L 1.156001091003418 2 C 0.5669708251953125 2 0.01341056823730469 1.770829200744629 -0.4027090072631836 1.354709625244141 C -0.8188295364379883 0.9385900497436523 -1.047999382019043 0.3850297927856445 -1.047999382019043 -0.2040004730224609 L -1.047999382019043 -11.71300029754639 C -1.047999382019043 -12.30203056335449 -0.8188295364379883 -12.8555908203125 -0.4027090072631836 -13.27171039581299 C 0.01341056823730469 -13.68782997131348 0.5669708251953125 -13.91700077056885 1.156001091003418 -13.91700077056885 Z"
                                                  stroke="none" fill="#EA690A"></path>
                                        </g>
                                    </svg>
                                    <span>{l s='Parking' mod='colissimo'}</span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                         viewBox="0 0 22.7 25">
                                        <g id="Composant_11_2" data-name="Composant 11 – 2"
                                           transform="translate(1 1)">
                                            <g id="Groupe_1437" data-name="Groupe 1437"
                                               transform="translate(-201.383 -168)">
                                                <g id="Icon_feather-calendar" data-name="Icon feather-calendar"
                                                   transform="translate(198.383 166)">
                                                    <path id="Tracé_846" data-name="Tracé 846"
                                                          d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z"
                                                          transform="translate(0 0.3)" fill="none" stroke="#EA690A"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"></path>
                                                    <path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6"
                                                          transform="translate(1.95 0)" fill="none" stroke="#EA690A"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"></path>
                                                    <path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6"
                                                          transform="translate(0.75 0)" fill="none" stroke="#EA690A"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"></path>
                                                    <path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7"
                                                          transform="translate(0 1.2)" fill="none" stroke="#EA690A"
                                                          stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"></path>
                                                </g>
                                                <path id="Tracé_853" data-name="Tracé 853"
                                                      d="M3.311.088a3.913,3.913,0,0,1-1.54-.281A2.271,2.271,0,0,1,.754-1,2.1,2.1,0,0,1,.4-2.211v-.242a.127.127,0,0,1,.038-.094.127.127,0,0,1,.093-.038H1.782a.127.127,0,0,1,.094.038.127.127,0,0,1,.039.094v.165a.9.9,0,0,0,.407.743,1.8,1.8,0,0,0,1.1.3,1.3,1.3,0,0,0,.869-.248.776.776,0,0,0,.286-.61A.616.616,0,0,0,4.4-2.547a1.554,1.554,0,0,0-.478-.319q-.3-.137-.962-.379a9.426,9.426,0,0,1-1.249-.517,2.507,2.507,0,0,1-.858-.72A1.811,1.811,0,0,1,.506-5.61,1.986,1.986,0,0,1,.847-6.765a2.171,2.171,0,0,1,.946-.759,3.465,3.465,0,0,1,1.4-.264,3.57,3.57,0,0,1,1.49.3A2.43,2.43,0,0,1,5.7-6.661a2.124,2.124,0,0,1,.369,1.238v.165a.127.127,0,0,1-.039.094.127.127,0,0,1-.094.038H4.675a.127.127,0,0,1-.093-.038.127.127,0,0,1-.039-.094v-.088a1,1,0,0,0-.379-.786,1.548,1.548,0,0,0-1.04-.325,1.316,1.316,0,0,0-.808.22.716.716,0,0,0-.292.605.66.66,0,0,0,.171.462,1.5,1.5,0,0,0,.506.335q.336.148,1.039.4a12.948,12.948,0,0,1,1.227.506,2.444,2.444,0,0,1,.8.655,1.717,1.717,0,0,1,.358,1.128A1.97,1.97,0,0,1,5.368-.512,3.231,3.231,0,0,1,3.311.088Z"
                                                      transform="translate(208.383 188)" fill="#EA690A"></path>
                                            </g>
                                        </g>
                                    </svg>
                                    <span>{l s='Saturday' mod='colissimo'}</span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                         viewBox="0 0 22.7 25">
                                        <g id="Composant_12_2" data-name="Composant 12 – 2"
                                           transform="translate(1 1)">
                                            <g id="Icon_feather-calendar" data-name="Icon feather-calendar"
                                               transform="translate(-3 -2)">
                                                <path id="Tracé_846" data-name="Tracé 846"
                                                      d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z"
                                                      transform="translate(0 0.3)" fill="none" stroke="#EA690A"
                                                      stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"></path>
                                                <path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6"
                                                      transform="translate(1.95 0)" fill="none" stroke="#EA690A"
                                                      stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"></path>
                                                <path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6"
                                                      transform="translate(0.75 0)" fill="none" stroke="#EA690A"
                                                      stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"></path>
                                                <path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7"
                                                      transform="translate(0 1.2)" fill="none" stroke="#EA690A"
                                                      stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"></path>
                                            </g>
                                            <path id="Tracé_852" data-name="Tracé 852"
                                                  d="M.8,0A.127.127,0,0,1,.71-.038.127.127,0,0,1,.671-.132V-7.568A.127.127,0,0,1,.71-7.661.127.127,0,0,1,.8-7.7h2.75A3.373,3.373,0,0,1,5-7.409a2.252,2.252,0,0,1,.963.82A2.2,2.2,0,0,1,6.3-5.368v3.036a2.2,2.2,0,0,1-.341,1.221A2.252,2.252,0,0,1,5-.292,3.373,3.373,0,0,1,3.553,0ZM2.222-1.386a.049.049,0,0,0,.055.055H3.608a1.061,1.061,0,0,0,.82-.341,1.336,1.336,0,0,0,.325-.913v-2.53a1.3,1.3,0,0,0-.313-.913A1.087,1.087,0,0,0,3.6-6.369H2.277a.049.049,0,0,0-.055.055Z"
                                                  transform="translate(7 20)" fill="#EA690A"></path>
                                        </g>
                                    </svg>
                                    <span>{l s='Sunday' mod='colissimo'}</span>
                                </div>
                                <div class="widget_colissimo_padding0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                         viewBox="0 0 24 24">
                                        <g id="Icon_feather-clock" data-name="Icon feather-clock"
                                           transform="translate(1 1)">
                                            <path id="Tracé_850" data-name="Tracé 850"
                                                  d="M24,13A11,11,0,1,1,13,2,11,11,0,0,1,24,13Z"
                                                  transform="translate(-2 -2)" fill="none" stroke="#EA690A"
                                                  stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"></path>
                                            <path id="Tracé_851" data-name="Tracé 851" d="M12,6v6.6l4.4,2.2"
                                                  transform="translate(-1 -1.6)" fill="none" stroke="#EA690A"
                                                  stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"></path>
                                        </g>
                                    </svg>
                                    <span>{l s='After 7pm' mod='colissimo'}</span>
                                </div>
                            </div>
                            <ul id="listPDR"></ul>
                        </div>
                        <div id='map_modal'></div>
                    </div>
                {/if}
                {if isset($widget_osm_in_modal_mobile) && $widget_osm_in_modal_mobile}
                    <div class="osmContentMobile {if isset($widget_osm_display_first_mobile) && !$widget_osm_display_first_mobile}mapFirst{/if}">
                        <div class="listAccessibilityLogo">
                            <div>
                                <svg id="Icon_awesome-wheelchair" data-name="Icon awesome-wheelchair"
                                     xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                     viewBox="0 0 24 24">
                                    <path id="Icon_awesome-wheelchair-2" data-name="Icon awesome-wheelchair"
                                          d="M23.255,18.078l.667,1.344a.75.75,0,0,1-.338,1.005l-3.069,1.542a1.5,1.5,0,0,1-2.024-.706L15.547,15H9a1.5,1.5,0,0,1-1.485-1.288C5.927,2.593,6.018,3.283,6,3A3,3,0,1,1,9.439,5.968L9.658,7.5H15.75a.75.75,0,0,1,.75.75v1.5a.75.75,0,0,1-.75.75H10.087L10.3,12h6.2a1.5,1.5,0,0,1,1.358.862l2.7,5.738,1.7-.86a.75.75,0,0,1,1.005.338ZM14.595,16.5H13.446A5.25,5.25,0,1,1,5.64,11.2L5.2,8.086A8.25,8.25,0,1,0,15.8,19.069Z"
                                          fill="#EA690A"></path>
                                </svg>
                                <span>{l s='Disabled access' mod='colissimo'}</span>
                            </div>
                            <div>
                                <svg id="Composant_10_2" data-name="Composant 10 – 2"
                                     xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                     viewBox="0 0 24 24">
                                    <g id="Rectangle_563" data-name="Rectangle 563" fill="none" stroke="#EA690A"
                                       stroke-width="2">
                                        <rect width="24" height="24" rx="3" stroke="none"></rect>
                                        <rect x="1" y="1" width="22" height="22" rx="2" fill="none"></rect>
                                    </g>
                                    <g id="Tracé_845" data-name="Tracé 845" transform="translate(7.048 17.918)"
                                       fill="none" stroke-linecap="round">
                                        <path d="M5.95-11.917a4.087,4.087,0,0,1,1.972.459,3.235,3.235,0,0,1,1.317,1.3,3.906,3.906,0,0,1,.468,1.929,3.626,3.626,0,0,1-.485,1.887,3.3,3.3,0,0,1-1.36,1.267,4.342,4.342,0,0,1-2.015.451H3.434a.075.075,0,0,0-.085.085V-.2A.2.2,0,0,1,3.29-.06.2.2,0,0,1,3.145,0H1.156a.2.2,0,0,1-.145-.06A.2.2,0,0,1,.952-.2V-11.713a.2.2,0,0,1,.059-.145.2.2,0,0,1,.145-.059ZM5.593-6.545A1.755,1.755,0,0,0,6.851-7a1.552,1.552,0,0,0,.476-1.182,1.6,1.6,0,0,0-.476-1.207,1.736,1.736,0,0,0-1.258-.459H3.434a.075.075,0,0,0-.085.085V-6.63a.075.075,0,0,0,.085.085Z"
                                              stroke="none"></path>
                                        <path d="M 1.156001091003418 -11.91700077056885 C 1.099330902099609 -11.91700077056885 1.051170349121094 -11.8971700668335 1.011500358581543 -11.85750007629395 C 0.9718303680419922 -11.81783008575439 0.952000617980957 -11.7696704864502 0.952000617980957 -11.71300029754639 L 0.952000617980957 -0.2040004730224609 C 0.952000617980957 -0.1473302841186523 0.9718303680419922 -0.09917068481445312 1.011500358581543 -0.05950069427490234 C 1.051170349121094 -0.01983070373535156 1.099330902099609 0 1.156001091003418 0 L 3.145000457763672 0 C 3.20167064666748 0 3.249830722808838 -0.01983070373535156 3.289500713348389 -0.05950069427490234 C 3.329170703887939 -0.09917068481445312 3.349000453948975 -0.1473302841186523 3.349000453948975 -0.2040004730224609 L 3.349000453948975 -4.539000511169434 C 3.349000453948975 -4.595670223236084 3.377330780029297 -4.624000549316406 3.434000492095947 -4.624000549316406 L 5.848000526428223 -4.624000549316406 C 6.607330799102783 -4.624000549316406 7.278830528259277 -4.774170398712158 7.862500667572021 -5.074500560760498 C 8.446170806884766 -5.37483024597168 8.899500846862793 -5.797000408172607 9.222500801086426 -6.341000556945801 C 9.545500755310059 -6.885000228881836 9.707000732421875 -7.514000415802002 9.707000732421875 -8.228000640869141 C 9.707000732421875 -8.953330039978027 9.551170349121094 -9.596500396728516 9.239500999450684 -10.15750026702881 C 8.927830696105957 -10.7185001373291 8.488670349121094 -11.15200042724609 7.922000885009766 -11.45800018310547 C 7.355330467224121 -11.76399993896484 6.698000907897949 -11.91700077056885 5.950000762939453 -11.91700077056885 L 1.156001091003418 -11.91700077056885 M 5.593000888824463 -6.545000553131104 L 3.434000492095947 -6.545000553131104 C 3.377330780029297 -6.545000553131104 3.349000453948975 -6.573330402374268 3.349000453948975 -6.630000591278076 L 3.349000453948975 -9.758000373840332 C 3.349000453948975 -9.814670562744141 3.377330780029297 -9.843000411987305 3.434000492095947 -9.843000411987305 L 5.593000888824463 -9.843000411987305 C 6.114330768585205 -9.843000411987305 6.533670902252197 -9.690000534057617 6.851000785827637 -9.384000778198242 C 7.168330669403076 -9.078000068664551 7.327000617980957 -8.675670623779297 7.327000617980957 -8.177000045776367 C 7.327000617980957 -7.689670562744141 7.168330669403076 -7.295830249786377 6.851000785827637 -6.995500564575195 C 6.533670902252197 -6.695170402526855 6.114330768585205 -6.545000553131104 5.593000888824463 -6.545000553131104 M 1.156001091003418 -13.91700077056885 L 5.950000762939453 -13.91700077056885 C 7.029930591583252 -13.91700077056885 8.013130187988281 -13.68176078796387 8.87229061126709 -13.21781063079834 C 9.777510643005371 -12.72900009155273 10.48927021026611 -12.02615070343018 10.98781108856201 -11.1287899017334 C 11.46503067016602 -10.26981067657471 11.70700073242188 -9.293840408325195 11.70700073242188 -8.228000640869141 C 11.70700073242188 -7.153040409088135 11.44969081878662 -6.174620151519775 10.94221115112305 -5.319920539855957 C 10.42597103118896 -4.450470447540283 9.697680473327637 -3.769570350646973 8.777570724487305 -3.296120643615723 C 7.910830497741699 -2.850130081176758 6.925180435180664 -2.624000549316406 5.848000526428223 -2.624000549316406 L 5.349000453948975 -2.624000549316406 L 5.349000453948975 -0.2040004730224609 C 5.349000453948975 0.3850297927856445 5.11983060836792 0.9385900497436523 4.703710556030273 1.354709625244141 C 4.287590503692627 1.770829200744629 3.734030723571777 2 3.145000457763672 2 L 1.156001091003418 2 C 0.5669708251953125 2 0.01341056823730469 1.770829200744629 -0.4027090072631836 1.354709625244141 C -0.8188295364379883 0.9385900497436523 -1.047999382019043 0.3850297927856445 -1.047999382019043 -0.2040004730224609 L -1.047999382019043 -11.71300029754639 C -1.047999382019043 -12.30203056335449 -0.8188295364379883 -12.8555908203125 -0.4027090072631836 -13.27171039581299 C 0.01341056823730469 -13.68782997131348 0.5669708251953125 -13.91700077056885 1.156001091003418 -13.91700077056885 Z"
                                              stroke="none" fill="#EA690A"></path>
                                    </g>
                                </svg>
                                <span>{l s='Parking' mod='colissimo'}</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                     viewBox="0 0 22.7 25">
                                    <g id="Composant_11_2" data-name="Composant 11 – 2"
                                       transform="translate(1 1)">
                                        <g id="Groupe_1437" data-name="Groupe 1437"
                                           transform="translate(-201.383 -168)">
                                            <g id="Icon_feather-calendar" data-name="Icon feather-calendar"
                                               transform="translate(198.383 166)">
                                                <path id="Tracé_846" data-name="Tracé 846"
                                                      d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z"
                                                      transform="translate(0 0.3)" fill="none" stroke="#EA690A"
                                                      stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"></path>
                                                <path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6"
                                                      transform="translate(1.95 0)" fill="none" stroke="#EA690A"
                                                      stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"></path>
                                                <path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6"
                                                      transform="translate(0.75 0)" fill="none" stroke="#EA690A"
                                                      stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"></path>
                                                <path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7"
                                                      transform="translate(0 1.2)" fill="none" stroke="#EA690A"
                                                      stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2"></path>
                                            </g>
                                            <path id="Tracé_853" data-name="Tracé 853"
                                                  d="M3.311.088a3.913,3.913,0,0,1-1.54-.281A2.271,2.271,0,0,1,.754-1,2.1,2.1,0,0,1,.4-2.211v-.242a.127.127,0,0,1,.038-.094.127.127,0,0,1,.093-.038H1.782a.127.127,0,0,1,.094.038.127.127,0,0,1,.039.094v.165a.9.9,0,0,0,.407.743,1.8,1.8,0,0,0,1.1.3,1.3,1.3,0,0,0,.869-.248.776.776,0,0,0,.286-.61A.616.616,0,0,0,4.4-2.547a1.554,1.554,0,0,0-.478-.319q-.3-.137-.962-.379a9.426,9.426,0,0,1-1.249-.517,2.507,2.507,0,0,1-.858-.72A1.811,1.811,0,0,1,.506-5.61,1.986,1.986,0,0,1,.847-6.765a2.171,2.171,0,0,1,.946-.759,3.465,3.465,0,0,1,1.4-.264,3.57,3.57,0,0,1,1.49.3A2.43,2.43,0,0,1,5.7-6.661a2.124,2.124,0,0,1,.369,1.238v.165a.127.127,0,0,1-.039.094.127.127,0,0,1-.094.038H4.675a.127.127,0,0,1-.093-.038.127.127,0,0,1-.039-.094v-.088a1,1,0,0,0-.379-.786,1.548,1.548,0,0,0-1.04-.325,1.316,1.316,0,0,0-.808.22.716.716,0,0,0-.292.605.66.66,0,0,0,.171.462,1.5,1.5,0,0,0,.506.335q.336.148,1.039.4a12.948,12.948,0,0,1,1.227.506,2.444,2.444,0,0,1,.8.655,1.717,1.717,0,0,1,.358,1.128A1.97,1.97,0,0,1,5.368-.512,3.231,3.231,0,0,1,3.311.088Z"
                                                  transform="translate(208.383 188)" fill="#EA690A"></path>
                                        </g>
                                    </g>
                                </svg>
                                <span>{l s='Saturday' mod='colissimo'}</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                     viewBox="0 0 22.7 25">
                                    <g id="Composant_12_2" data-name="Composant 12 – 2"
                                       transform="translate(1 1)">
                                        <g id="Icon_feather-calendar" data-name="Icon feather-calendar"
                                           transform="translate(-3 -2)">
                                            <path id="Tracé_846" data-name="Tracé 846"
                                                  d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z"
                                                  transform="translate(0 0.3)" fill="none" stroke="#EA690A"
                                                  stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"></path>
                                            <path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6"
                                                  transform="translate(1.95 0)" fill="none" stroke="#EA690A"
                                                  stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"></path>
                                            <path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6"
                                                  transform="translate(0.75 0)" fill="none" stroke="#EA690A"
                                                  stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"></path>
                                            <path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7"
                                                  transform="translate(0 1.2)" fill="none" stroke="#EA690A"
                                                  stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"></path>
                                        </g>
                                        <path id="Tracé_852" data-name="Tracé 852"
                                              d="M.8,0A.127.127,0,0,1,.71-.038.127.127,0,0,1,.671-.132V-7.568A.127.127,0,0,1,.71-7.661.127.127,0,0,1,.8-7.7h2.75A3.373,3.373,0,0,1,5-7.409a2.252,2.252,0,0,1,.963.82A2.2,2.2,0,0,1,6.3-5.368v3.036a2.2,2.2,0,0,1-.341,1.221A2.252,2.252,0,0,1,5-.292,3.373,3.373,0,0,1,3.553,0ZM2.222-1.386a.049.049,0,0,0,.055.055H3.608a1.061,1.061,0,0,0,.82-.341,1.336,1.336,0,0,0,.325-.913v-2.53a1.3,1.3,0,0,0-.313-.913A1.087,1.087,0,0,0,3.6-6.369H2.277a.049.049,0,0,0-.055.055Z"
                                              transform="translate(7 20)" fill="#EA690A"></path>
                                    </g>
                                </svg>
                                <span>{l s='Sunday' mod='colissimo'}</span>
                            </div>
                            <div class="widget_colissimo_padding0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                     viewBox="0 0 24 24">
                                    <g id="Icon_feather-clock" data-name="Icon feather-clock"
                                       transform="translate(1 1)">
                                        <path id="Tracé_850" data-name="Tracé 850"
                                              d="M24,13A11,11,0,1,1,13,2,11,11,0,0,1,24,13Z"
                                              transform="translate(-2 -2)" fill="none" stroke="#EA690A"
                                              stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"></path>
                                        <path id="Tracé_851" data-name="Tracé 851" d="M12,6v6.6l4.4,2.2"
                                              transform="translate(-1 -1.6)" fill="none" stroke="#EA690A"
                                              stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"></path>
                                    </g>
                                </svg>
                                <span>{l s='After 7pm' mod='colissimo'}</span>
                            </div>
                        </div>
                        <ul id="listPDR"></ul>
                        {if isset($widget_osm_display_map_mobile) && $widget_osm_display_map_mobile}
                            <div {if isset($widget_osm_display_superposed) && $widget_osm_display_superposed}style="display: none"{/if}
                                 id='map_modal'>
                            </div>
                            {if isset($widget_osm_display_superposed) && $widget_osm_display_superposed}
                                <div id="widget_colissimo_map_liste">
                                    <img class="widget_colissimo_map_img"
                                         src="https://ws.colissimo.fr/widget-colissimo/images/liste.svg" alt=""
                                         style="display: none">
                                    <img class="widget_colissimo_map_img"
                                         src="https://ws.colissimo.fr/widget-colissimo/images/carte.svg" alt="">
                                </div>
                            {/if}
                        {/if}
                    </div>
                {/if}
            </div>
        </div>
    </div>
{/if}


{if (isset($widget_osm_in_modal) && !$widget_osm_in_modal) || ( isset($widget_osm_in_modal_mobile) && !$widget_osm_in_modal_mobile)}
    <div
            id="osmDiv"
            class="colissimo-osm-front-widget {if isset($widget_osm_in_modal_mobile) && !$widget_osm_in_modal_mobile}osm-mobile{/if} {if isset($widget_osm_in_modal) && !$widget_osm_in_modal}osm-desktop{/if}">
        <div style="padding: 15px; display: flex; border-bottom: #c3c3c3 solid">
            <div class="logoColissimo"><img src="https://ws.colissimo.fr/widget-colissimo/images/Colissimo.svg"
                                            alt="logo colissimo"></div>
            <div style="flex-grow: 1; display: flex; justify-content: center">
                <div id="divInput">
                    <div style="display: flex; align-items: center; width: 100%">
                        <i><img src="https://ws.colissimo.fr/widget-colissimo/images/pictoAdresse.svg"
                                alt="picto adresse"></i>
                        <input type="text" id="searchInputOsm"
                               placeholder="23 rue de Rivoli, 75004 Paris">
                    </div>
                    <div id="resultAddress"></div>
                </div>
                <button id="buttonSearchOsm" type="button"><img
                            src="https://ws.colissimo.fr/widget-colissimo/images/loupe.svg" alt="loupe">
                </button>
            </div>
        </div>
        {if isset($widget_osm_in_modal) && !$widget_osm_in_modal}
            <div class="osmContent" style="display: flex">
                <div class="leftColumnOsm">
                    <div class="listAccessibilityLogo">
                        <div>
                            <svg id="Icon_awesome-wheelchair" data-name="Icon awesome-wheelchair"
                                 xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                 viewBox="0 0 24 24">
                                <path id="Icon_awesome-wheelchair-2" data-name="Icon awesome-wheelchair"
                                      d="M23.255,18.078l.667,1.344a.75.75,0,0,1-.338,1.005l-3.069,1.542a1.5,1.5,0,0,1-2.024-.706L15.547,15H9a1.5,1.5,0,0,1-1.485-1.288C5.927,2.593,6.018,3.283,6,3A3,3,0,1,1,9.439,5.968L9.658,7.5H15.75a.75.75,0,0,1,.75.75v1.5a.75.75,0,0,1-.75.75H10.087L10.3,12h6.2a1.5,1.5,0,0,1,1.358.862l2.7,5.738,1.7-.86a.75.75,0,0,1,1.005.338ZM14.595,16.5H13.446A5.25,5.25,0,1,1,5.64,11.2L5.2,8.086A8.25,8.25,0,1,0,15.8,19.069Z"
                                      fill="#EA690A"></path>
                            </svg>
                            <span>{l s='Disabled access' mod='colissimo'}</span>
                        </div>
                        <div>
                            <svg id="Composant_10_2" data-name="Composant 10 – 2"
                                 xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                 viewBox="0 0 24 24">
                                <g id="Rectangle_563" data-name="Rectangle 563" fill="none" stroke="#EA690A"
                                   stroke-width="2">
                                    <rect width="24" height="24" rx="3" stroke="none"></rect>
                                    <rect x="1" y="1" width="22" height="22" rx="2" fill="none"></rect>
                                </g>
                                <g id="Tracé_845" data-name="Tracé 845" transform="translate(7.048 17.918)"
                                   fill="none" stroke-linecap="round">
                                    <path d="M5.95-11.917a4.087,4.087,0,0,1,1.972.459,3.235,3.235,0,0,1,1.317,1.3,3.906,3.906,0,0,1,.468,1.929,3.626,3.626,0,0,1-.485,1.887,3.3,3.3,0,0,1-1.36,1.267,4.342,4.342,0,0,1-2.015.451H3.434a.075.075,0,0,0-.085.085V-.2A.2.2,0,0,1,3.29-.06.2.2,0,0,1,3.145,0H1.156a.2.2,0,0,1-.145-.06A.2.2,0,0,1,.952-.2V-11.713a.2.2,0,0,1,.059-.145.2.2,0,0,1,.145-.059ZM5.593-6.545A1.755,1.755,0,0,0,6.851-7a1.552,1.552,0,0,0,.476-1.182,1.6,1.6,0,0,0-.476-1.207,1.736,1.736,0,0,0-1.258-.459H3.434a.075.075,0,0,0-.085.085V-6.63a.075.075,0,0,0,.085.085Z"
                                          stroke="none"></path>
                                    <path d="M 1.156001091003418 -11.91700077056885 C 1.099330902099609 -11.91700077056885 1.051170349121094 -11.8971700668335 1.011500358581543 -11.85750007629395 C 0.9718303680419922 -11.81783008575439 0.952000617980957 -11.7696704864502 0.952000617980957 -11.71300029754639 L 0.952000617980957 -0.2040004730224609 C 0.952000617980957 -0.1473302841186523 0.9718303680419922 -0.09917068481445312 1.011500358581543 -0.05950069427490234 C 1.051170349121094 -0.01983070373535156 1.099330902099609 0 1.156001091003418 0 L 3.145000457763672 0 C 3.20167064666748 0 3.249830722808838 -0.01983070373535156 3.289500713348389 -0.05950069427490234 C 3.329170703887939 -0.09917068481445312 3.349000453948975 -0.1473302841186523 3.349000453948975 -0.2040004730224609 L 3.349000453948975 -4.539000511169434 C 3.349000453948975 -4.595670223236084 3.377330780029297 -4.624000549316406 3.434000492095947 -4.624000549316406 L 5.848000526428223 -4.624000549316406 C 6.607330799102783 -4.624000549316406 7.278830528259277 -4.774170398712158 7.862500667572021 -5.074500560760498 C 8.446170806884766 -5.37483024597168 8.899500846862793 -5.797000408172607 9.222500801086426 -6.341000556945801 C 9.545500755310059 -6.885000228881836 9.707000732421875 -7.514000415802002 9.707000732421875 -8.228000640869141 C 9.707000732421875 -8.953330039978027 9.551170349121094 -9.596500396728516 9.239500999450684 -10.15750026702881 C 8.927830696105957 -10.7185001373291 8.488670349121094 -11.15200042724609 7.922000885009766 -11.45800018310547 C 7.355330467224121 -11.76399993896484 6.698000907897949 -11.91700077056885 5.950000762939453 -11.91700077056885 L 1.156001091003418 -11.91700077056885 M 5.593000888824463 -6.545000553131104 L 3.434000492095947 -6.545000553131104 C 3.377330780029297 -6.545000553131104 3.349000453948975 -6.573330402374268 3.349000453948975 -6.630000591278076 L 3.349000453948975 -9.758000373840332 C 3.349000453948975 -9.814670562744141 3.377330780029297 -9.843000411987305 3.434000492095947 -9.843000411987305 L 5.593000888824463 -9.843000411987305 C 6.114330768585205 -9.843000411987305 6.533670902252197 -9.690000534057617 6.851000785827637 -9.384000778198242 C 7.168330669403076 -9.078000068664551 7.327000617980957 -8.675670623779297 7.327000617980957 -8.177000045776367 C 7.327000617980957 -7.689670562744141 7.168330669403076 -7.295830249786377 6.851000785827637 -6.995500564575195 C 6.533670902252197 -6.695170402526855 6.114330768585205 -6.545000553131104 5.593000888824463 -6.545000553131104 M 1.156001091003418 -13.91700077056885 L 5.950000762939453 -13.91700077056885 C 7.029930591583252 -13.91700077056885 8.013130187988281 -13.68176078796387 8.87229061126709 -13.21781063079834 C 9.777510643005371 -12.72900009155273 10.48927021026611 -12.02615070343018 10.98781108856201 -11.1287899017334 C 11.46503067016602 -10.26981067657471 11.70700073242188 -9.293840408325195 11.70700073242188 -8.228000640869141 C 11.70700073242188 -7.153040409088135 11.44969081878662 -6.174620151519775 10.94221115112305 -5.319920539855957 C 10.42597103118896 -4.450470447540283 9.697680473327637 -3.769570350646973 8.777570724487305 -3.296120643615723 C 7.910830497741699 -2.850130081176758 6.925180435180664 -2.624000549316406 5.848000526428223 -2.624000549316406 L 5.349000453948975 -2.624000549316406 L 5.349000453948975 -0.2040004730224609 C 5.349000453948975 0.3850297927856445 5.11983060836792 0.9385900497436523 4.703710556030273 1.354709625244141 C 4.287590503692627 1.770829200744629 3.734030723571777 2 3.145000457763672 2 L 1.156001091003418 2 C 0.5669708251953125 2 0.01341056823730469 1.770829200744629 -0.4027090072631836 1.354709625244141 C -0.8188295364379883 0.9385900497436523 -1.047999382019043 0.3850297927856445 -1.047999382019043 -0.2040004730224609 L -1.047999382019043 -11.71300029754639 C -1.047999382019043 -12.30203056335449 -0.8188295364379883 -12.8555908203125 -0.4027090072631836 -13.27171039581299 C 0.01341056823730469 -13.68782997131348 0.5669708251953125 -13.91700077056885 1.156001091003418 -13.91700077056885 Z"
                                          stroke="none" fill="#EA690A"></path>
                                </g>
                            </svg>
                            <span>{l s='Parking' mod='colissimo'}</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                 viewBox="0 0 22.7 25">
                                <g id="Composant_11_2" data-name="Composant 11 – 2"
                                   transform="translate(1 1)">
                                    <g id="Groupe_1437" data-name="Groupe 1437"
                                       transform="translate(-201.383 -168)">
                                        <g id="Icon_feather-calendar" data-name="Icon feather-calendar"
                                           transform="translate(198.383 166)">
                                            <path id="Tracé_846" data-name="Tracé 846"
                                                  d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z"
                                                  transform="translate(0 0.3)" fill="none" stroke="#EA690A"
                                                  stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"></path>
                                            <path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6"
                                                  transform="translate(1.95 0)" fill="none" stroke="#EA690A"
                                                  stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"></path>
                                            <path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6"
                                                  transform="translate(0.75 0)" fill="none" stroke="#EA690A"
                                                  stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"></path>
                                            <path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7"
                                                  transform="translate(0 1.2)" fill="none" stroke="#EA690A"
                                                  stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"></path>
                                        </g>
                                        <path id="Tracé_853" data-name="Tracé 853"
                                              d="M3.311.088a3.913,3.913,0,0,1-1.54-.281A2.271,2.271,0,0,1,.754-1,2.1,2.1,0,0,1,.4-2.211v-.242a.127.127,0,0,1,.038-.094.127.127,0,0,1,.093-.038H1.782a.127.127,0,0,1,.094.038.127.127,0,0,1,.039.094v.165a.9.9,0,0,0,.407.743,1.8,1.8,0,0,0,1.1.3,1.3,1.3,0,0,0,.869-.248.776.776,0,0,0,.286-.61A.616.616,0,0,0,4.4-2.547a1.554,1.554,0,0,0-.478-.319q-.3-.137-.962-.379a9.426,9.426,0,0,1-1.249-.517,2.507,2.507,0,0,1-.858-.72A1.811,1.811,0,0,1,.506-5.61,1.986,1.986,0,0,1,.847-6.765a2.171,2.171,0,0,1,.946-.759,3.465,3.465,0,0,1,1.4-.264,3.57,3.57,0,0,1,1.49.3A2.43,2.43,0,0,1,5.7-6.661a2.124,2.124,0,0,1,.369,1.238v.165a.127.127,0,0,1-.039.094.127.127,0,0,1-.094.038H4.675a.127.127,0,0,1-.093-.038.127.127,0,0,1-.039-.094v-.088a1,1,0,0,0-.379-.786,1.548,1.548,0,0,0-1.04-.325,1.316,1.316,0,0,0-.808.22.716.716,0,0,0-.292.605.66.66,0,0,0,.171.462,1.5,1.5,0,0,0,.506.335q.336.148,1.039.4a12.948,12.948,0,0,1,1.227.506,2.444,2.444,0,0,1,.8.655,1.717,1.717,0,0,1,.358,1.128A1.97,1.97,0,0,1,5.368-.512,3.231,3.231,0,0,1,3.311.088Z"
                                              transform="translate(208.383 188)" fill="#EA690A"></path>
                                    </g>
                                </g>
                            </svg>
                            <span>{l s='Saturday' mod='colissimo'}</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                 viewBox="0 0 22.7 25">
                                <g id="Composant_12_2" data-name="Composant 12 – 2"
                                   transform="translate(1 1)">
                                    <g id="Icon_feather-calendar" data-name="Icon feather-calendar"
                                       transform="translate(-3 -2)">
                                        <path id="Tracé_846" data-name="Tracé 846"
                                              d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z"
                                              transform="translate(0 0.3)" fill="none" stroke="#EA690A"
                                              stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"></path>
                                        <path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6"
                                              transform="translate(1.95 0)" fill="none" stroke="#EA690A"
                                              stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"></path>
                                        <path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6"
                                              transform="translate(0.75 0)" fill="none" stroke="#EA690A"
                                              stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"></path>
                                        <path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7"
                                              transform="translate(0 1.2)" fill="none" stroke="#EA690A"
                                              stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"></path>
                                    </g>
                                    <path id="Tracé_852" data-name="Tracé 852"
                                          d="M.8,0A.127.127,0,0,1,.71-.038.127.127,0,0,1,.671-.132V-7.568A.127.127,0,0,1,.71-7.661.127.127,0,0,1,.8-7.7h2.75A3.373,3.373,0,0,1,5-7.409a2.252,2.252,0,0,1,.963.82A2.2,2.2,0,0,1,6.3-5.368v3.036a2.2,2.2,0,0,1-.341,1.221A2.252,2.252,0,0,1,5-.292,3.373,3.373,0,0,1,3.553,0ZM2.222-1.386a.049.049,0,0,0,.055.055H3.608a1.061,1.061,0,0,0,.82-.341,1.336,1.336,0,0,0,.325-.913v-2.53a1.3,1.3,0,0,0-.313-.913A1.087,1.087,0,0,0,3.6-6.369H2.277a.049.049,0,0,0-.055.055Z"
                                          transform="translate(7 20)" fill="#EA690A"></path>
                                </g>
                            </svg>
                            <span>{l s='Sunday' mod='colissimo'}</span>
                        </div>
                        <div class="widget_colissimo_padding0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                 viewBox="0 0 24 24">
                                <g id="Icon_feather-clock" data-name="Icon feather-clock"
                                   transform="translate(1 1)">
                                    <path id="Tracé_850" data-name="Tracé 850"
                                          d="M24,13A11,11,0,1,1,13,2,11,11,0,0,1,24,13Z"
                                          transform="translate(-2 -2)" fill="none" stroke="#EA690A"
                                          stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"></path>
                                    <path id="Tracé_851" data-name="Tracé 851" d="M12,6v6.6l4.4,2.2"
                                          transform="translate(-1 -1.6)" fill="none" stroke="#EA690A"
                                          stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"></path>
                                </g>
                            </svg>
                            <span>{l s='After 7pm' mod='colissimo'}</span>
                        </div>
                    </div>
                    <ul id="listPDR"></ul>
                </div>
                <div id='map'></div>
            </div>
        {/if}
        {if isset($widget_osm_in_modal_mobile) && !$widget_osm_in_modal_mobile}
            <div class="osmContentMobile {if isset($widget_osm_display_first_mobile) && !$widget_osm_display_first_mobile}mapFirst{/if}">
                <div class="listAccessibilityLogo">
                    <div>
                        <svg id="Icon_awesome-wheelchair" data-name="Icon awesome-wheelchair"
                             xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                             viewBox="0 0 24 24">
                            <path id="Icon_awesome-wheelchair-2" data-name="Icon awesome-wheelchair"
                                  d="M23.255,18.078l.667,1.344a.75.75,0,0,1-.338,1.005l-3.069,1.542a1.5,1.5,0,0,1-2.024-.706L15.547,15H9a1.5,1.5,0,0,1-1.485-1.288C5.927,2.593,6.018,3.283,6,3A3,3,0,1,1,9.439,5.968L9.658,7.5H15.75a.75.75,0,0,1,.75.75v1.5a.75.75,0,0,1-.75.75H10.087L10.3,12h6.2a1.5,1.5,0,0,1,1.358.862l2.7,5.738,1.7-.86a.75.75,0,0,1,1.005.338ZM14.595,16.5H13.446A5.25,5.25,0,1,1,5.64,11.2L5.2,8.086A8.25,8.25,0,1,0,15.8,19.069Z"
                                  fill="#EA690A"></path>
                        </svg>
                        <span>{l s='Disabled access' mod='colissimo'}</span>
                    </div>
                    <div>
                        <svg id="Composant_10_2" data-name="Composant 10 – 2"
                             xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                             viewBox="0 0 24 24">
                            <g id="Rectangle_563" data-name="Rectangle 563" fill="none" stroke="#EA690A"
                               stroke-width="2">
                                <rect width="24" height="24" rx="3" stroke="none"></rect>
                                <rect x="1" y="1" width="22" height="22" rx="2" fill="none"></rect>
                            </g>
                            <g id="Tracé_845" data-name="Tracé 845" transform="translate(7.048 17.918)"
                               fill="none" stroke-linecap="round">
                                <path d="M5.95-11.917a4.087,4.087,0,0,1,1.972.459,3.235,3.235,0,0,1,1.317,1.3,3.906,3.906,0,0,1,.468,1.929,3.626,3.626,0,0,1-.485,1.887,3.3,3.3,0,0,1-1.36,1.267,4.342,4.342,0,0,1-2.015.451H3.434a.075.075,0,0,0-.085.085V-.2A.2.2,0,0,1,3.29-.06.2.2,0,0,1,3.145,0H1.156a.2.2,0,0,1-.145-.06A.2.2,0,0,1,.952-.2V-11.713a.2.2,0,0,1,.059-.145.2.2,0,0,1,.145-.059ZM5.593-6.545A1.755,1.755,0,0,0,6.851-7a1.552,1.552,0,0,0,.476-1.182,1.6,1.6,0,0,0-.476-1.207,1.736,1.736,0,0,0-1.258-.459H3.434a.075.075,0,0,0-.085.085V-6.63a.075.075,0,0,0,.085.085Z"
                                      stroke="none"></path>
                                <path d="M 1.156001091003418 -11.91700077056885 C 1.099330902099609 -11.91700077056885 1.051170349121094 -11.8971700668335 1.011500358581543 -11.85750007629395 C 0.9718303680419922 -11.81783008575439 0.952000617980957 -11.7696704864502 0.952000617980957 -11.71300029754639 L 0.952000617980957 -0.2040004730224609 C 0.952000617980957 -0.1473302841186523 0.9718303680419922 -0.09917068481445312 1.011500358581543 -0.05950069427490234 C 1.051170349121094 -0.01983070373535156 1.099330902099609 0 1.156001091003418 0 L 3.145000457763672 0 C 3.20167064666748 0 3.249830722808838 -0.01983070373535156 3.289500713348389 -0.05950069427490234 C 3.329170703887939 -0.09917068481445312 3.349000453948975 -0.1473302841186523 3.349000453948975 -0.2040004730224609 L 3.349000453948975 -4.539000511169434 C 3.349000453948975 -4.595670223236084 3.377330780029297 -4.624000549316406 3.434000492095947 -4.624000549316406 L 5.848000526428223 -4.624000549316406 C 6.607330799102783 -4.624000549316406 7.278830528259277 -4.774170398712158 7.862500667572021 -5.074500560760498 C 8.446170806884766 -5.37483024597168 8.899500846862793 -5.797000408172607 9.222500801086426 -6.341000556945801 C 9.545500755310059 -6.885000228881836 9.707000732421875 -7.514000415802002 9.707000732421875 -8.228000640869141 C 9.707000732421875 -8.953330039978027 9.551170349121094 -9.596500396728516 9.239500999450684 -10.15750026702881 C 8.927830696105957 -10.7185001373291 8.488670349121094 -11.15200042724609 7.922000885009766 -11.45800018310547 C 7.355330467224121 -11.76399993896484 6.698000907897949 -11.91700077056885 5.950000762939453 -11.91700077056885 L 1.156001091003418 -11.91700077056885 M 5.593000888824463 -6.545000553131104 L 3.434000492095947 -6.545000553131104 C 3.377330780029297 -6.545000553131104 3.349000453948975 -6.573330402374268 3.349000453948975 -6.630000591278076 L 3.349000453948975 -9.758000373840332 C 3.349000453948975 -9.814670562744141 3.377330780029297 -9.843000411987305 3.434000492095947 -9.843000411987305 L 5.593000888824463 -9.843000411987305 C 6.114330768585205 -9.843000411987305 6.533670902252197 -9.690000534057617 6.851000785827637 -9.384000778198242 C 7.168330669403076 -9.078000068664551 7.327000617980957 -8.675670623779297 7.327000617980957 -8.177000045776367 C 7.327000617980957 -7.689670562744141 7.168330669403076 -7.295830249786377 6.851000785827637 -6.995500564575195 C 6.533670902252197 -6.695170402526855 6.114330768585205 -6.545000553131104 5.593000888824463 -6.545000553131104 M 1.156001091003418 -13.91700077056885 L 5.950000762939453 -13.91700077056885 C 7.029930591583252 -13.91700077056885 8.013130187988281 -13.68176078796387 8.87229061126709 -13.21781063079834 C 9.777510643005371 -12.72900009155273 10.48927021026611 -12.02615070343018 10.98781108856201 -11.1287899017334 C 11.46503067016602 -10.26981067657471 11.70700073242188 -9.293840408325195 11.70700073242188 -8.228000640869141 C 11.70700073242188 -7.153040409088135 11.44969081878662 -6.174620151519775 10.94221115112305 -5.319920539855957 C 10.42597103118896 -4.450470447540283 9.697680473327637 -3.769570350646973 8.777570724487305 -3.296120643615723 C 7.910830497741699 -2.850130081176758 6.925180435180664 -2.624000549316406 5.848000526428223 -2.624000549316406 L 5.349000453948975 -2.624000549316406 L 5.349000453948975 -0.2040004730224609 C 5.349000453948975 0.3850297927856445 5.11983060836792 0.9385900497436523 4.703710556030273 1.354709625244141 C 4.287590503692627 1.770829200744629 3.734030723571777 2 3.145000457763672 2 L 1.156001091003418 2 C 0.5669708251953125 2 0.01341056823730469 1.770829200744629 -0.4027090072631836 1.354709625244141 C -0.8188295364379883 0.9385900497436523 -1.047999382019043 0.3850297927856445 -1.047999382019043 -0.2040004730224609 L -1.047999382019043 -11.71300029754639 C -1.047999382019043 -12.30203056335449 -0.8188295364379883 -12.8555908203125 -0.4027090072631836 -13.27171039581299 C 0.01341056823730469 -13.68782997131348 0.5669708251953125 -13.91700077056885 1.156001091003418 -13.91700077056885 Z"
                                      stroke="none" fill="#EA690A"></path>
                            </g>
                        </svg>
                        <span>{l s='Parking' mod='colissimo'}</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                             viewBox="0 0 22.7 25">
                            <g id="Composant_11_2" data-name="Composant 11 – 2"
                               transform="translate(1 1)">
                                <g id="Groupe_1437" data-name="Groupe 1437"
                                   transform="translate(-201.383 -168)">
                                    <g id="Icon_feather-calendar" data-name="Icon feather-calendar"
                                       transform="translate(198.383 166)">
                                        <path id="Tracé_846" data-name="Tracé 846"
                                              d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z"
                                              transform="translate(0 0.3)" fill="none" stroke="#EA690A"
                                              stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"></path>
                                        <path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6"
                                              transform="translate(1.95 0)" fill="none" stroke="#EA690A"
                                              stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"></path>
                                        <path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6"
                                              transform="translate(0.75 0)" fill="none" stroke="#EA690A"
                                              stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"></path>
                                        <path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7"
                                              transform="translate(0 1.2)" fill="none" stroke="#EA690A"
                                              stroke-linecap="round" stroke-linejoin="round"
                                              stroke-width="2"></path>
                                    </g>
                                    <path id="Tracé_853" data-name="Tracé 853"
                                          d="M3.311.088a3.913,3.913,0,0,1-1.54-.281A2.271,2.271,0,0,1,.754-1,2.1,2.1,0,0,1,.4-2.211v-.242a.127.127,0,0,1,.038-.094.127.127,0,0,1,.093-.038H1.782a.127.127,0,0,1,.094.038.127.127,0,0,1,.039.094v.165a.9.9,0,0,0,.407.743,1.8,1.8,0,0,0,1.1.3,1.3,1.3,0,0,0,.869-.248.776.776,0,0,0,.286-.61A.616.616,0,0,0,4.4-2.547a1.554,1.554,0,0,0-.478-.319q-.3-.137-.962-.379a9.426,9.426,0,0,1-1.249-.517,2.507,2.507,0,0,1-.858-.72A1.811,1.811,0,0,1,.506-5.61,1.986,1.986,0,0,1,.847-6.765a2.171,2.171,0,0,1,.946-.759,3.465,3.465,0,0,1,1.4-.264,3.57,3.57,0,0,1,1.49.3A2.43,2.43,0,0,1,5.7-6.661a2.124,2.124,0,0,1,.369,1.238v.165a.127.127,0,0,1-.039.094.127.127,0,0,1-.094.038H4.675a.127.127,0,0,1-.093-.038.127.127,0,0,1-.039-.094v-.088a1,1,0,0,0-.379-.786,1.548,1.548,0,0,0-1.04-.325,1.316,1.316,0,0,0-.808.22.716.716,0,0,0-.292.605.66.66,0,0,0,.171.462,1.5,1.5,0,0,0,.506.335q.336.148,1.039.4a12.948,12.948,0,0,1,1.227.506,2.444,2.444,0,0,1,.8.655,1.717,1.717,0,0,1,.358,1.128A1.97,1.97,0,0,1,5.368-.512,3.231,3.231,0,0,1,3.311.088Z"
                                          transform="translate(208.383 188)" fill="#EA690A"></path>
                                </g>
                            </g>
                        </svg>
                        <span>{l s='Saturday' mod='colissimo'}</span>
                    </div>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                             viewBox="0 0 22.7 25">
                            <g id="Composant_12_2" data-name="Composant 12 – 2"
                               transform="translate(1 1)">
                                <g id="Icon_feather-calendar" data-name="Icon feather-calendar"
                                   transform="translate(-3 -2)">
                                    <path id="Tracé_846" data-name="Tracé 846"
                                          d="M5.3,4H21.4a2.3,2.3,0,0,1,2.3,2.3V22.4a2.3,2.3,0,0,1-2.3,2.3H5.3A2.3,2.3,0,0,1,3,22.4V6.3A2.3,2.3,0,0,1,5.3,4Z"
                                          transform="translate(0 0.3)" fill="none" stroke="#EA690A"
                                          stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"></path>
                                    <path id="Tracé_847" data-name="Tracé 847" d="M16,2V6.6"
                                          transform="translate(1.95 0)" fill="none" stroke="#EA690A"
                                          stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"></path>
                                    <path id="Tracé_848" data-name="Tracé 848" d="M8,2V6.6"
                                          transform="translate(0.75 0)" fill="none" stroke="#EA690A"
                                          stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"></path>
                                    <path id="Tracé_849" data-name="Tracé 849" d="M3,10H23.7"
                                          transform="translate(0 1.2)" fill="none" stroke="#EA690A"
                                          stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"></path>
                                </g>
                                <path id="Tracé_852" data-name="Tracé 852"
                                      d="M.8,0A.127.127,0,0,1,.71-.038.127.127,0,0,1,.671-.132V-7.568A.127.127,0,0,1,.71-7.661.127.127,0,0,1,.8-7.7h2.75A3.373,3.373,0,0,1,5-7.409a2.252,2.252,0,0,1,.963.82A2.2,2.2,0,0,1,6.3-5.368v3.036a2.2,2.2,0,0,1-.341,1.221A2.252,2.252,0,0,1,5-.292,3.373,3.373,0,0,1,3.553,0ZM2.222-1.386a.049.049,0,0,0,.055.055H3.608a1.061,1.061,0,0,0,.82-.341,1.336,1.336,0,0,0,.325-.913v-2.53a1.3,1.3,0,0,0-.313-.913A1.087,1.087,0,0,0,3.6-6.369H2.277a.049.049,0,0,0-.055.055Z"
                                      transform="translate(7 20)" fill="#EA690A"></path>
                            </g>
                        </svg>
                        <span>{l s='Sunday' mod='colissimo'}</span>
                    </div>
                    <div class="widget_colissimo_padding0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                             viewBox="0 0 24 24">
                            <g id="Icon_feather-clock" data-name="Icon feather-clock"
                               transform="translate(1 1)">
                                <path id="Tracé_850" data-name="Tracé 850"
                                      d="M24,13A11,11,0,1,1,13,2,11,11,0,0,1,24,13Z"
                                      transform="translate(-2 -2)" fill="none" stroke="#EA690A"
                                      stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"></path>
                                <path id="Tracé_851" data-name="Tracé 851" d="M12,6v6.6l4.4,2.2"
                                      transform="translate(-1 -1.6)" fill="none" stroke="#EA690A"
                                      stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"></path>
                            </g>
                        </svg>
                        <span>{l s='After 7pm' mod='colissimo'}</span>
                    </div>
                </div>
                <ul id="listPDR"></ul>
                {if isset($widget_osm_display_map_mobile) && $widget_osm_display_map_mobile}
                    <div {if isset($widget_osm_display_superposed) && $widget_osm_display_superposed}style="display: none"{/if}
                         id='map'>
                    </div>
                    {if isset($widget_osm_display_superposed) && $widget_osm_display_superposed}
                        <div id="widget_colissimo_map_liste">
                            <img class="widget_colissimo_map_img"
                                 src="https://ws.colissimo.fr/widget-colissimo/images/liste.svg" alt=""
                                 style="display: none">
                            <img class="widget_colissimo_map_img"
                                 src="https://ws.colissimo.fr/widget-colissimo/images/carte.svg" alt="">
                        </div>
                    {/if}
                {/if}
            </div>
        {/if}
    </div>
{/if}
