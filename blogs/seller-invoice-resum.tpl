{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    JA Modules <info@jamodules.com>
 * @copyright Since 2007 JA Modules
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{debug}
{if $JMARKETPLACE_SELLERINVOICE_RESUM == 1}
    <div class="col-xs-12 col-lg-{if $JMARKETPLACE_SELLERINVOICE_PAYMENT_INFORMATION == 1}6{else}12{/if}">
        <div class="jmarketplace-panel">
            <div class="jmarketplace-panel-header">
                <h2>{l s='Resum' mod='jmarketplace'}</h2>
                {if $JMARKETPLACE_SELLERINVOICE_TRANSFER_HISTORY == 1}
                    <a href="#" title="{l s='Transfer history' mod='jmarketplace'}" data-toggle="modal" data-target="#transfer-history">
                        {if $use_icons == 'fontawesome'}
                            <i class="fa fas fa-history"></i>
                        {else}
                            <span class="material-icons">history</span>
                        {/if}
                    </a>
                {/if}
            </div>
            <div class="modal fade" id="transfer-history" tabindex="-1" role="dialog" aria-labelledby="transfer-historyLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="transfer-historyLabel">{l s='Transfer history' mod='jmarketplace'}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            {if $transfer_funds_history && count($transfer_funds_history)}
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>{l s='Date of demand' mod='jmarketplace'}</th>
                                                <th>{l s='Date of payment' mod='jmarketplace'}</th>
                                                <th style="text-align:center;">{l s='Total' mod='jmarketplace'}</th>
                                                <th>{l s='Status' mod='jmarketplace'}</th>
                                                {if $JMARKETPLACE_SELLERINVOICE_REQUIRED == 1}
                                                    <th style="text-align:center;">{l s='Invoice' mod='jmarketplace'}</th>
                                                {/if}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$transfer_funds_history item=demand name=sellerdemand}
                                                <tr>
                                                    <td>{dateFormat date=$demand.date_add full=0} - {$demand.date_add|escape:'htmlall':'UTF-8'|substr:11:5}</td>
                                                    <td>
                                                        {if $demand.validate == 0}
                                                            -
                                                        {else}
                                                            {dateFormat date=$demand.date_upd full=0} - {$demand.date_upd|escape:'htmlall':'UTF-8'|substr:11:5}
                                                        {/if}
                                                    </td>
                                                    <td style="text-align:center;">{$demand.total|escape:'html':'UTF-8'}</td>
                                                    <td>
                                                        {if $demand.validate == 0}
                                                            {l s='Transfer pending' mod='jmarketplace'}
                                                        {else}
                                                            {l s='Transfer accepted' mod='jmarketplace'}
                                                        {/if}
                                                    </td>
                                                    {if $JMARKETPLACE_SELLERINVOICE_REQUIRED == 1}
                                                        <td style="text-align:center;">
                                                            <a href="{$demand.invoice|escape:'html':'UTF-8'}" title="{l s='Download invoice' mod='jmarketplace'}" target="_blank">
                                                                {if $use_icons == 'fontawesome'}
                                                                    <i class="fa fas fa-file-pdf"></i>
                                                                {else}
                                                                    <span class="material-icons">picture_as_pdf</span>
                                                                {/if}
                                                            <a>
                                                        </td>
                                                    {/if}
                                                </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            {else}
                                <p class="alert alert-info">
                                    {l s='There are not demands.' mod='jmarketplace'}
                                </p>
                            {/if} 
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tbody>
                        <tr>
                            <td>{l s='Date of the last withdrawal' mod='jmarketplace'}</td>
                            <td style="text-align:right;">
                                {dateFormat date=$last_commissions_payed.date_upd full=0} - {$last_commissions_payed.date_upd|escape:'htmlall':'UTF-8'|substr:11:5}
                            </td>
                        </tr>
                        <tr>
                            <td>{l s='Total amount received' mod='jmarketplace'}</td>
                            <td style="text-align:right;">
                                {if $JMARKETPLACE_COMMISSION_TAX == 1}
                                    {$total_paid_commission_tax_incl|escape:'html':'UTF-8'}
                                {else}
                                    {$total_paid_commission_tax_excl|escape:'html':'UTF-8'}
                                {/if} 
                            </td>
                        </tr>
                        <tr>
                            <td>{l s='Outstanding amount' mod='jmarketplace'}</td>
                            <td style="text-align:right;">
                                {if $JMARKETPLACE_COMMISSION_TAX == 1}
                                    {$total_outstanding_commission_tax_incl|escape:'html':'UTF-8'}
                                {else}
                                    {$total_outstanding_commission_tax_excl|escape:'html':'UTF-8'}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td>{l s='Canceled amount' mod='jmarketplace'}</td>
                            <td style="text-align:right;">
                                {if $JMARKETPLACE_COMMISSION_TAX == 1}
                                    {$total_canceled_commission_tax_incl|escape:'html':'UTF-8'}
                                {else}
                                    {$total_canceled_commission_tax_excl|escape:'html':'UTF-8'}
                                {/if}
                            </td>
                        </tr>
                        {if $JMARKETPLACE_SELLERINVOICE_MIN_VALUE != 0}
                            <tr>
                                <td>{l s='Minimum amount to request a transfer funds' mod='jmarketplace'}</td>
                                <td style="text-align:right;">{$seller_invoice_min_value_to_display|escape:'html':'UTF-8'}</td>
                            </tr>
                        {/if}
                        {if $JMARKETPLACE_SELLERINVOICE_DAYS != 0}
                            <tr>
                                <td>{l s='Number days to request a transfer funds' mod='jmarketplace'}</td>
                                <td style="text-align:right;">{$JMARKETPLACE_SELLERINVOICE_DAYS|intval}</td>
                            </tr>
                        {/if}
                        {if $JMARKETPLACE_SELLERINVOICE_MIN_VALUE != 0 AND $JMARKETPLACE_SELLERINVOICE_MIN_VALUE > $total_funds}
                            <tr>
                                <td>{l s='Total amount non cashable' mod='jmarketplace'}</td>
                                <td style="text-align:right;">{$total_funds_display|escape:'html':'UTF-8'}</td>
                            </tr>
                        {else}
                            <tr>
                                <td>{l s='Cashable amount for the next invoicing' mod='jmarketplace'}</td>
                                <td style="text-align:right;"><strong>{$total_funds_display|escape:'html':'UTF-8'}</strong></td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/if}
