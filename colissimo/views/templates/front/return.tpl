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

{extends file='page.tpl'}

{block name='page_title'}
    {l s='Colissimo returns' mod='colissimo'}
{/block}

{block name='page_content_container'}
    <section id="colissimo-returns" class="page-content">
        <h6 class="colissimo-returns-header">
            {l s='Find on this page your orders processed by Colissimo.' mod='colissimo'}
        </h6>
        <p>
            {l s='This page will allow you to:' mod='colissimo'}
        </p>
        <ul class="colissimo-list">
            <li>{l s='download your return labels,' mod='colissimo'}</li>
            <li>{l s='choose a place of deposit to return your parcel,' mod='colissimo'}</li>
            <li>{l s='trigger the collection of your parcel in your mailbox' mod='colissimo'}</li>
        </ul>
        <p>
            {l s='If you want to deposit your parcel at a retailer or post office, click' mod='colissimo'}
            <a target="_blank"
               href="https://www.laposte.fr/particulier/outils/trouver-un-point-de-retrait-ou-de-depot-colissimo">
                {l s='here' mod='colissimo'}
            </a>
        </p>
        <img class="colissimo-returns-logo img-responsive"
             src="{$colissimo_img_path|escape:'htmlall':'UTF-8'}Colissimo_Logo_H.png"/>
        <table class="table table-striped table-bordered table-labeled hidden-sm-down">
            <thead class="thead-default">
            <tr>
                <th>{l s='Order reference' mod='colissimo'}</th>
                <th>{l s='Date' mod='colissimo'}</th>
                <th class="hidden-md-down">{l s='Status' mod='colissimo'}</th>
                <th class="text-sm-center">{l s='Return label' mod='colissimo'}</th>
                <th class="text-sm-center">{l s='Mailbox return' mod='colissimo'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach $shipments as $shipment}
                <tr>
                    <th scope="row">{$shipment.reference|escape:'htmlall':'UTF-8'}</th>
                    <td>{$shipment.date|escape:'htmlall':'UTF-8'}</td>
                    <td class="hidden-md-down">
          <span class="label label-pill {$shipment.status.contrast|escape:'htmlall':'UTF-8'}"
                style="background-color: {$shipment.status.color}">
            {$shipment.status.name|escape:'htmlall':'UTF-8'}
          </span>
                    </td>
                    <td class="text-sm-center">
                        {if $shipment.return_label.id}
                            {if $shipment.secure_return}
                                <button data-colissimo-label-id="{$shipment.label.id|intval}"
                                        class="btn btn-primary colissimo-request-secure-code">
                                    {l s='Receive my secure code' mod='colissimo'}
                                </button>
                                {if $shipment.label.secure_return_path}
                                    <a target="_blank"
                                       href="{$link->getModuleLink('colissimo', 'return', ['action' => 'downloadSecureReturn', 'id_label' => $shipment.label.id|intval])}">
                                        <br>
                                        <i class="material-icons">get_app</i>{l s='Download secure code' mod='colissimo'}
                                    </a>
                                {/if}
                            {else}
                                {if !$shipment.return_file_deleted}
                                    <a target="_blank"
                                       href="{$link->getModuleLink('colissimo', 'return', ['action' => 'downloadLabel', 'id_label' => $shipment.return_label.id|intval])|escape:'htmlall':'UTF-8'}">
                                        <i class="material-icons">get_app</i>
                                        {$shipment.return_label.shipping_number|escape:'htmlall':'UTF-8'}
                                    </a>
                                {else}
                                    {$shipment.return_label.shipping_number|escape:'htmlall':'UTF-8'}
                                {/if}
                            {/if}
                        {else}
                            {if $shipment.return_available}
                                {if $shipment.secure_return}
                                    <button data-colissimo-label-id="{$shipment.label.id|intval}"
                                            class="btn btn-primary colissimo-request-secure-code">
                                        {l s='Receive my secure code' mod='colissimo'}
                                    </button>
                                    {if $shipment.label.secure_return_path}
                                        <a target="_blank"
                                           href="{$link->getModuleLink('colissimo', 'return', ['action' => 'downloadSecureReturn', 'id_label' => $shipment.label.id|intval])}">
                                            <br>
                                            <i class="material-icons">get_app</i>{l s='Download secure code' mod='colissimo'}
                                        </a>
                                    {/if}
                                {else}
                                    <a class="btn btn-primary colissimo-request-return"
                                       href="{$link->getModuleLink('colissimo', 'return', ['action' => 'generateLabel', 'id_label' => $shipment.label.id|intval])}">
                                        {l s='Generate a return label' mod='colissimo'}
                                    </a>
                                {/if}
                            {else}
                                --
                            {/if}
                        {/if}
                    </td>
                    <td class="text-sm-center font-weight-bold">
                        {if $shipment.mailbox_return && $shipment.return_label.id}
                            {if $shipment.mailbox_return_text}
                                {$shipment.mailbox_return_text|escape:'htmlall':'UTF-8'}
                            {else}
                                {if !$shipment.return_file_deleted}
                                    <button data-colissimo-label-id="{$shipment.return_label.id|intval}"
                                            class="btn btn-primary colissimo-request-pickup">
                                        {l s='Request a mailbox pickup' mod='colissimo'}
                                    </button>
                                {else}
                                    --
                                {/if}
                            {/if}
                        {else}
                            --
                        {/if}
                    </td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="5">
                        <div class="alert alert-info">
                            {l s='You don\'t have any orders shipped with Colissimo yet.' mod='colissimo'}
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        <div class="hidden-md-up colissimo-returns">
            {foreach from=$shipments item=shipment}
                <div class="colissimo-return">
                    <div class="row">
                        <div class="col-xs-10">
                            <h3>{$shipment.reference|escape:'htmlall':'UTF-8'}</h3>
                            <div class="date">{$shipment.date|escape:'htmlall':'UTF-8'}</div>
                            <div class="status">
                <span class="label label-pill {$shipment.status.contrast|escape:'htmlall':'UTF-8'}"
                      style="background-color:{$shipment.status.color}">
                  {$shipment.status.name|escape:'htmlall':'UTF-8'}
                </span>
                            </div>
                            <div class="font-weight-bold">
                                {if $shipment.return_label.id}
                                    {if $shipment.secure_return}
                                        <button data-colissimo-label-id="{$shipment.label.id|intval}"
                                                class="btn btn-primary colissimo-request-secure-code">
                                            {l s='Receive my secure code' mod='colissimo'}
                                        </button>
                                        {if $shipment.label.secure_return_path}
                                            <a target="_blank"
                                               href="{$link->getModuleLink('colissimo', 'return', ['action' => 'downloadSecureReturn', 'id_label' => $shipment.label.id|intval])}">
                                                <i class="material-icons">get_app</i>{l s='Download secure code' mod='colissimo'}
                                            </a>
                                        {/if}
                                    {else}
                                        {if !$shipment.return_file_deleted}
                                            <a target="_blank"
                                               href="{$link->getModuleLink('colissimo', 'return', ['action' => 'downloadLabel', 'id_label' => $shipment.return_label.id|intval])}">
                                                <i class="material-icons">get_app</i>
                                                {$shipment.return_label.shipping_number}
                                            </a>
                                        {else}
                                            {$shipment.return_label.shipping_number}
                                        {/if}
                                    {/if}
                                {else}
                                    {if $shipment.return_available}
                                        {if $shipment.secure_return}
                                            <button data-colissimo-label-id="{$shipment.label.id|intval}"
                                                    class="btn btn-primary colissimo-request-secure-code">
                                                {l s='Receive my secure code' mod='colissimo'}
                                            </button>
                                            {if $shipment.label.secure_return_path}
                                                <a target="_blank"
                                                   href="{$link->getModuleLink('colissimo', 'return', ['action' => 'downloadSecureReturn', 'id_label' => $shipment.label.id|intval])}">
                                                    <i class="material-icons">get_app</i>{l s='Download secure code' mod='colissimo'}
                                                </a>
                                            {/if}
                                        {else}
                                            <a class="btn btn-primary colissimo-request-return"
                                               href="{$link->getModuleLink('colissimo', 'return', ['action' => 'generateLabel', 'id_label' => $shipment.label.id|intval])}">
                                                {l s='Generate a return label' mod='colissimo'}
                                            </a>
                                        {/if}
                                    {else}
                                        --
                                    {/if}
                                {/if}
                            </div>
                            <div class="font-weight-bold btn-colissimo-pickup">
                                {if $shipment.mailbox_return && $shipment.return_label.id}
                                    {if $shipment.mailbox_return_text}
                                        {$shipment.mailbox_return_text|escape:'htmlall':'UTF-8'}
                                    {else}
                                        {if !$shipment.return_file_deleted}
                                            <button data-colissimo-label-id="{$shipment.return_label.id|intval}"
                                                    class="btn btn-primary colissimo-request-pickup">
                                                {l s='Request a mailbox pickup' mod='colissimo'}
                                            </button>
                                        {else}
                                            --
                                        {/if}
                                    {/if}
                                {else}
                                    --
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </section>
    <div class="colissimo-modal">
        <div class="modal fade colissimo-bal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        {l s='Colissimo Mailbox return' mod='colissimo'}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger modal-body-error" style="display: none;"></div>
                        <div class="modal-body-content"></div>
                    </div>
                    <div class="modal-footer">
                        <img src="{$colissimo_img_path|escape:'htmlall':'UTF-8'}Colissimo_Logo_H.png"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="colissimo-modal">
        <div class="modal fade colissimo-secure-return" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        {l s='Colissimo Secure return' mod='colissimo'}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger modal-body-error" style="display: none;"></div>
                        <div class="modal-body-content"></div>
                    </div>
                    <div class="modal-footer">
                        <img src="{$colissimo_img_path|escape:'htmlall':'UTF-8'}Colissimo_Logo_H.png"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
{literal}
    <script type="text/javascript">
        var colissimoAjaxReturn = prestashop.urls.base_url;
        var genericErrorMessage = "{/literal}{l s='An unexpected error occurred. Please try again later.' mod='colissimo'}{literal}";
    </script>
{/literal}
{/block}
