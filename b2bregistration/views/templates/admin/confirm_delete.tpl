{*
* Registration
*
* NOTICE OF LICENSE
*
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FME Modules
*  @copyright 2022 fmemodules All right reserved
*  @license   FMM Modules
*}

<a href="{$link->getAdminLink('AdminB2BCustomers')|escape:'htmlall':'UTF-8'}&amp;id_customer={$id|escape:'htmlall':'UTF-8'}&amp;deleteb2b_confirm{$table|escape:'htmlall':'UTF-8'}"
  title="{l s='Delete' mod='b2bregistration'}"
  class="delete"
  onclick="event.stopPropagation();event.preventDefault();confirm_link('',{l s='Delete selected item?' mod='b2bregistration'},{l s='Yes' mod='b2bregistration'},{l s='No' mod='b2bregistration'},{$link->getAdminLink('AdminB2BCustomers')|escape:'htmlall':'UTF-8'}&amp;id_customer={$id|escape:'htmlall':'UTF-8'}&amp;deleteb2b_confirm{$table|escape:'htmlall':'UTF-8'},'#');">
  <i class="icon-trash"></i> {l s='Delete' mod='b2bregistration'}</a>