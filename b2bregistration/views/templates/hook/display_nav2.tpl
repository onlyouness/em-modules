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
{if isset($mobile_view) && $mobile_view == false} 
{foreach from=$b2b_links item=link}
    <div class="b2bregistration-top-link-text">
        <a href="{$link.page_link|escape:'htmlall':'UTF-8'}" title="{$link.top_link_text|escape:'htmlall':'UTF-8'}">&nbsp;{$link.top_link_text|escape:'htmlall':'UTF-8'}</a>
    </div>
{/foreach}
{/if}
{if isset($mobile_view) && $mobile_view == true} 
<div id='b2b_toplink_mobile_view'>
    {foreach from=$b2b_links item=link}
        <div class="b2bregistration-top-link-text">
            <a href="{$link.page_link|escape:'htmlall':'UTF-8'}" title="{$link.top_link_text|escape:'htmlall':'UTF-8'}">&nbsp;{$link.top_link_text|escape:'htmlall':'UTF-8'}</a>
        </div>
    {/foreach}
</div>
{/if}


<style>
.b2bregistration-top-link-text {
    background-color: {$background_color|escape:'htmlall':'UTF-8'} !important;
}
.b2bregistration-top-link-text a {
    color: {$text_color|escape:'htmlall':'UTF-8'} !important;
}
 #b2b_toplink_mobile_view{
    display: none;
 }
.b2bregistration-top-link-text a {
    color: {$text_color|escape:'htmlall':'UTF-8'} !important;
    text-decoration: none; /* Remove underline from links */
}

@media (max-width: 767px) {

    .b2bregistration-top-link-text {
        display: inline-block;
        width: 100%;
        box-sizing: border-box;
        margin-bottom: 5px;
        margin-left: 2px;
        margin-right: 2px;
    }
    #b2b_toplink_mobile_view{
    width: 100%;
    display: block;
 }
}
</style>