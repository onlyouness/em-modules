{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    FMM Modules
* @copyright FMM Modules
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* @package   B2B Registration
*}

{foreach from=$b2b_links item=link}
    <div class="top-link-text-ps6">
        <a href="{$link.page_link|escape:'htmlall':'UTF-8'}" title="{$link.top_link_text|escape:'htmlall':'UTF-8'}">&nbsp;{$link.top_link_text|escape:'htmlall':'UTF-8'}</a>
    </div>
{/foreach}
<style>
.top-link-text-ps6 {
    background-color: {$background_color|escape:'htmlall':'UTF-8'} !important;
}
.top-link-text-ps6 a {
    color: {$text_color|escape:'htmlall':'UTF-8'} !important;
}
</style>