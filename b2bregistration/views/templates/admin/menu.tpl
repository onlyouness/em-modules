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
{$ps_version= 1.7}
{$module_path = 'dummy'}
{include file='./script.tpl'}
<div class="bootstrap col-lg-2">
    <div class="sidebar-fme-menu">
        <div class="fme-b2bregistration-menu" id="b2b-menu">
            <ul class="tab">
                <li class="tab-row">
                    <a class="tab-page" id="b2bregisteration_general_settings" href="{$general_settings|escape:'htmlall':'UTF-8'}" title="{l s='Posts' mod='b2bregistration'}">
                    {if $ps_version < 1.6}<img src="../img/admin/translation.gif">{else}<i class="material-icons">settings</i>{/if}
                    &nbsp;<span class="fme-menu-titles">{l s='General settings' mod='b2bregistration'}</span>
                    </a>
                </li>
                <li class="tab-row">
                    <a class="tab-page" id="b2bregistration_default_profile" href="{$default_profile|escape:'htmlall':'UTF-8'}" title="{l s='Related Posts' mod='b2bregistration'}">
                    {if $ps_version < 1.6}<img src="../img/admin/copy_files.gif">{else}<i class="material-icons">person</i>{/if}
                    &nbsp;<span class="fme-menu-titles">{l s='Default Profile' mod='b2bregistration'}</span>
                    </a>  
                </li>
                <li class="tab-row">
                    <a class="tab-page" id="b2bregisteration_b2b_profiles" href="{$b2b_profiles|escape:'htmlall':'UTF-8'}" title="{l s='Products' mod='b2bregistration'}">
                    {if $ps_version < 1.6}<img src="../img/admin/products.gif">{else}<i class="material-icons">star</i>{/if}
                    &nbsp;<span class="fme-menu-titles">{l s='B2B Profiles' mod='b2bregistration'}</span><span class="fa fa-angle-right" style="float: right"></span>
                    </a>   
                </li>
                <li class="tab-row">
                    <a class="tab-page" id="b2bregistration_custom_fields" href="{$custom_fields|escape:'htmlall':'UTF-8'}" title="{l s='Images' mod='b2bregistration'}">
                    {if $ps_version < 1.6}<img src="../img/admin/picture.gif">{else}<i class="material-icons">code</i>{/if}
                    &nbsp;<span class="fme-menu-titles">{l s='Custom Fields' mod='b2bregistration'}</span><span class="fa fa-angle-double-right" style="float: right"></span>
                    </a>   
                </li>
                <li class="tab-row">
                    <a class="tab-page" id="b2bregistration_manage_b2b_customers" href="{$manage_b2b_customers|escape:'htmlall':'UTF-8'}" title="{l s='Videos' mod='b2bregistration'}">
                    {if $ps_version < 1.6}<img src="../img/admin/photo.gif">{else}<i class="material-icons">edit</i> {/if}
                    &nbsp;<span class="fme-menu-titles">{l s='Manage B2B Users' mod='b2bregistration'}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

