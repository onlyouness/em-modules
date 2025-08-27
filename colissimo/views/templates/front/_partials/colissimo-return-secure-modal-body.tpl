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

<section class="form-fields">
    <div class="colissimo-secure-return-intro">
        <p>{l s='Select the items to be returned' mod='colissimo'}</p>
        <span>{l s='How to return your package?' mod='colissimo'}</span>
        <ol>
            <li>{l s='Pack your merchandise' mod='colissimo'}</li>
            <li>{l s='Go to the post office of your choice : www.laposte.fr/retour' mod='colissimo'}</li>
            <li>{l s='At the drop-off point, present your barcode to the person or machine (or your 9-character code) to have your label printed and make your deposit' mod='colissimo'}</li>
            <li>{l s='Track the delivery of your package on : www.laposte.fr/suivi' mod='colissimo'}</li>
        </ol>
    </div>
    <form method="post" id="colissimo-secure-return-request">
        <input type="hidden" name="id_colissimo_label" value="{$id_colissimo_label|intval}"/>
        <table id="order-products" class="table table-bordered">
            <thead class="thead-default">
            <tr>
                <th></th>
                <th>{l s='Product' mod='colissimo'}</th>
                <th>{l s='Quantity' mod='colissimo'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach $products as $product}
            <tr>
                <td><input class="colissimo-product-selection" type="checkbox" name="colissimo_order_{$product['id_product']|intval}_{$product['id_product_attribute']|intval}"></td>
                <td class="text-xs-left">{$product['name']|escape:'htmlall':'UTF-8'}</td>
                <td class="text-xs-left"><input type="text" name="quantity_{$product['id_product']|intval}_{$product['id_product_attribute']|intval}" value="{$product['quantity']|intval}">/{$product['quantity']|intval}
                </td>
            </tr>
            </tbody>
            <input type="hidden" name="weight_{$product['id_product']|intval}_{$product['id_product_attribute']|intval}" value="{$product['weight']|floatval}">
            {/foreach}
        </table>
        <div class="row form-group">
            <div class="offset-md-4 col-md-6">
                <button class="btn btn-primary colissimo-submit-secure-return" type="submit"
                        name="submitcolissimosecure-return">
                    <i class="material-icons icon-spinner-off">loop</i>
                    {l s='Confirm my return request' mod='colissimo'}
                </button>
            </div>
        </div>
    </form>
</section>
