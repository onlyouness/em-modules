<style>
    .brandContainer{
        display: flex;
        justify-content: start;
        align-items: flex-start;
        gap: 1rem;
        flex-direction: column;
    }
</style>
<div class="block-categories hidden-sm-down">
  <ul class="category-top-menu">
    <li>{l s='All Brands' d='Modules.BrandList.Shop'}</li>
    <li>
        <div class="brandContainer">
            {foreach from=$brands item=brand}
                <a {if $brand->id == $currentBrand}style="color:#C7000B;"{/if} href="{url entity='manufacturer' id=$brand->id }">{$brand->name}</a>
            {/foreach}
        </div>
    </li>
  </ul>
</div>

















{assign var="id_customer" value=$customer.id}
{if !is_null($id_customer)}
    {assign var="id_lang" value=$language.id}
    {assign var="address" value=Address::getFirstCustomerAddressId($id_customer)}
    {assign var="group_id" value=$customer.id_default_group}
    {assign var="countryId" value=Context::getContext()->cookie->selected_country}
    
    {* Determine product visibility based on customer group *}
    {assign var="groupCondition" value=""}
    {if $group_id == 4}
        {* Group 4 can see and buy their own products *}
        {assign var="groupCondition" value="cg.id_default_group = 4"}
    {/if}
    {if $group_id == 5}
        {* Group 5 can see products from groups 4 and 5 *}
        {assign var="groupCondition" value="cg.id_default_group = 4 OR cg.id_default_group = 5"}
    {/if}
    {if $group_id == 3}
        {* Normal customers can see products from group 5 *}
        {assign var="groupCondition" value="cg.id_default_group = 5"}
        
        {assign var="continent_query" value="SELECT c.continent
            FROM `country` c
            INNER JOIN `country_translation` ct ON ct.country_id = c.id
            WHERE ct.name = LOWER('" . pSQL($country->name) . "')
            AND ct.language_code = '".$lang_iso."'"}
        {assign var="seller_continent" value=Db::getInstance()->getValue($continent_query)}
        
        {assign var="countries_query" value="SELECT ct.name
            FROM `country` c
            INNER JOIN `country_translation` ct ON ct.country_id = c.id
            WHERE c.continent = '" . pSQL($seller_continent) . "'
            AND ct.language_code = '".$lang_iso."'"}
        {assign var="continent_countries" value=Db::getInstance()->executeS($countries_query)}
        
        {assign var="country_list" value=[]}
        {foreach from=$continent_countries item="continent_country"}
            {$country_list[] = "'" . $continent_country.name . "'"}
        {/foreach}
        {assign var="country_names" value=implode(",", $country_list)}
        
        {assign var="query" value="SELECT sp.id_product
            FROM `ps_seller_product` sp
            INNER JOIN `ps_seller` s ON s.id_seller = sp.id_seller
            INNER JOIN `ps_customer` cg ON cg.id_customer = s.id_customer
            INNER JOIN `ps_country_lang` cl ON cl.id_country = s.id_country
            WHERE ({$groupCondition})
            AND (
                (cg.id_default_group = 4) OR 
                (cg.id_default_group = 5 AND cl.name IN ({$country_names}) AND cl.id_lang = {$id_lang})
            )"}
    {else}
        {assign var="query" value="SELECT sp.id_product
            FROM `ps_seller_product` sp
            INNER JOIN `ps_seller` s ON s.id_seller = sp.id_seller
            INNER JOIN `ps_customer` cg ON cg.id_customer = s.id_customer
            WHERE {$groupCondition}"}
    {/if}
    
    {* Execute query to get product IDs *}
    {assign var="productIds" value=Db::getInstance()->executeS($query)}
    
    {* Prepare list of product IDs *}
    {assign var="productIdList" value=[]}
    {foreach from=$productIds item="productItem"}
        {$productIdList[] = $productItem.id_product}
    {/foreach}
{/if}







