{*
* 2007-2022 PrestaShop
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
*  @author    FMM Modules
*  @copyright 2022 FME Modules
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{literal}
    <script type="text/javascript">
    $(document).ready(function() {

        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }
        var action = getUrlParameter('action');
        var configure = getUrlParameter('configure');

        var selectedTab;
        if (configure == 'b2bregistration') {
            if(action === 'default_profile') {
                selectedTab = 'b2bregistration_default_profile';
            } else {
                selectedTab = 'b2bregisteration_general_settings';
            }
        } else if(localStorage.getItem('selectedTab')) {
                var storedTab = localStorage.getItem('selectedTab');
                selectedTab = storedTab;
        }
    
        $('.tab-page').removeClass('selected');
        $('#' + selectedTab).addClass('selected');
    
        $(document).on('click', ".tab-page", function() {
            $('.tab-page').removeClass('selected');
            $(this).addClass('selected');
            localStorage.setItem('selectedTab', $(this).attr('id'));
        });
    });
    </script>
    {/literal}
    