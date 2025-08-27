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

<div class="colissimo-orders-buttons pull-right">
    <button id="submit-create-labels" class="btn btn-primary" name="submitProcessColissimoCreateLabels">{l s='Create labels' mod='colissimo'}</button>
    <button id="submit-delete-labels" class="btn btn-primary" name="submitProcessColissimoDeleteLabels">{l s='Delete labels' mod='colissimo'}</button>
</div>

{literal}
<script type="text/javascript">
    var noOrdersMessage = "{/literal}{l s='Please select at least one order.' mod='colissimo'}{literal}";
    var genericErrorMessage = "{/literal}{l s='An error occured. Please try again.' mod='colissimo'}{literal}";
    var token = '{/literal}{getAdminToken tab='AdminColissimoOrders'}{literal}';
    var tokenLabel = '{/literal}{getAdminToken tab='AdminColissimoLabel'}{literal}';
</script>
{/literal}
