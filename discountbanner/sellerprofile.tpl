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

{extends file='page.tpl'}

{block name='page_content_container'}

    {hook h='displayMarketplaceHeaderProfile'} {*deprecated*}
    {hook h='displayMarketplaceSellerProfileTop'}

    {block name='page_content_top'}{/block}
    {block name='page_content'}

        {assign var="id_customer" value=$customer.id}
        {assign var="lang_iso" value=$language.iso_code}
        {assign var="id_customer" value=$customer.id}
        {if !is_null($id_customer)}
            {assign var="id_lang" value=$language.id}
            {assign var="lang_iso" value=$language.iso_code}
            {assign var="address" value=Address::getFirstCustomerAddressId($id_customer)}
            {assign var="group_id" value=$customer.id_default_group}
            {assign var="countryId" value=Context::getContext()->cookie->selected_country}
            {assign var="country" value=Country::getNameById($id_lang, $countryId)}
            {assign var="groupCondition" value=""}
            {if $group_id == 4}
                {assign var="groupCondition" value="cg.id_default_group = 4"}
            {/if}
            {if $group_id == 5}
                {assign var="groupCondition" value="cg.id_default_group = 4 OR cg.id_default_group = 5"}
            {/if}
            {if $group_id == 3 || $group_id ==6}
                {assign var="groupCondition" value="cg.id_default_group = 5"}
                {assign var="continent_query" value="SELECT c.continent
                        FROM `country` c
                        INNER JOIN `country_translation` ct ON ct.country_id = c.id
                        WHERE ct.name = '"|cat:$country|cat:"'"
                                                                                                                                                                                        }

                {assign var="seller_continent" value=Db::getInstance()->getValue($continent_query)}
                {assign var="countries_query" value="SELECT ct.name
                        FROM `country` c
                        INNER JOIN `country_translation` ct ON ct.country_id = c.id
                        WHERE c.continent = '"|cat:$seller_continent|cat:"'
                       "}

                {assign var="continent_countries" value=Db::getInstance()->executeS($countries_query)}

                {assign var="country_list" value=""}
                {foreach from=$continent_countries item=continent_country}
                    {if $country_list != ""}
                        {assign var="country_list" value=$country_list|cat:","}
                    {/if}
                    {assign var="country_list" value=$country_list|cat:"'"|cat:$continent_country.name|cat:"'"}
                {/foreach}

                {* Fallback if country_list is empty *}
                {if $country_list == ""}
                    {assign var="country_condition" value=""}
                {else}
                    {assign var="country_condition" value="AND cl.name IN ("|cat:$country_list|cat:")"}
                {/if}


                {assign var="query" value="SELECT sp.id_product
                        FROM `ps_seller_product` sp
                        INNER JOIN `ps_seller` s ON s.id_seller = sp.id_seller
                        INNER JOIN `ps_customer` cg ON cg.id_customer = s.id_customer
                        INNER JOIN `ps_country_lang` cl ON cl.name = s.country
                        WHERE ("|cat:$groupCondition|cat:")
                            "|cat:$country_condition|cat:"
                        "
                                                                                                                                                                                    }

            {else}
                {assign var="query" value="SELECT sp.id_product
                        FROM `ps_seller_product` sp
                        INNER JOIN `ps_seller` s ON s.id_seller = sp.id_seller
                        INNER JOIN `ps_customer` cg ON cg.id_customer = s.id_customer
                        WHERE {$groupCondition}"}
            {/if}
            {assign var="productIds" value=Db::getInstance()->executeS($query)}
            {assign var="productIdList" value=[]}
            {foreach from=$productIds item="productItem"}
                {$productIdList[] = $productItem.id_product}
            {/foreach}
        {/if}



        <div class="_profile_pg">
            <div class="img-profile">
                <div class="seller-logo_">
                    {if $show_logo}
                        {if $photo != false}
                            <img class="img-fluid" src="{$photo|escape:'html':'UTF-8'}" title="{$seller_name|escape:'htmlall':'UTF-8'}"
                                width="240" height="auto" />
                        {else}
                            <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.25" y="0.25" width="43.5" height="43.5" stroke="#ABABAB" stroke-width="0.5"></rect>
                                <circle cx="21.973" cy="18.428" r="3.86514" stroke="#ABABAB" stroke-width="1.2"></circle>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M28.7867 30.0373C28.5073 27.3709 25.7531 24.9861 21.9728 24.9861C18.1925 24.9861 15.4383 27.3709 15.1589 30.0373H13.9531C14.2288 26.5465 17.714 23.7861 21.9728 23.7861C26.2316 23.7861 29.7168 26.5465 29.9925 30.0373H28.7867Z"
                                    fill="#ABABAB"></path>
                            </svg>
                        {/if}
                    {else}
                        <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="0.25" y="0.25" width="43.5" height="43.5" stroke="#ABABAB" stroke-width="0.5"></rect>
                            <circle cx="21.973" cy="18.428" r="3.86514" stroke="#ABABAB" stroke-width="1.2"></circle>
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M28.7867 30.0373C28.5073 27.3709 25.7531 24.9861 21.9728 24.9861C18.1925 24.9861 15.4383 27.3709 15.1589 30.0373H13.9531C14.2288 26.5465 17.714 23.7861 21.9728 23.7861C26.2316 23.7861 29.7168 26.5465 29.9925 30.0373H28.7867Z"
                                fill="#ABABAB"></path>
                        </svg>
                    {/if}
                </div>
                <a href="{$url_seller_levels|escape:'html':'UTF-8'}" target="_blank">
                    {if $seller_level_logo != false}
                        <img class="img-fluid" src="{$seller_level_logo|escape:'html':'UTF-8'}"
                            alt="{$seller_level->name|escape:'html':'UTF-8'}" width="35" height="35" />
                    {/if}
                </a>
            </div>

            <div class="info_ctn">

                <h1>{$seller_name|escape:'htmlall':'UTF-8'}</h1>

                <div class="local-riwie">
                    <div class="locl-city-state">
                        <svg width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <rect y="0.00390625" width="21" height="21" fill="url(#pattern0)" fill-opacity="0.37" />
                            <defs>
                                <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1" height="1">
                                    <use xlink:href="#image0_1268_8542" transform="scale(0.01)" />
                                </pattern>
                                <image id="image0_1268_8542" width="100" height="100"
                                    xlink:href="data:image/png;base64,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" />
                            </defs>
                        </svg>
                        {if $show_city && $seller->city != ''}
                            <li class="col-xs-12 col-md-6">
                                <div class="seller-label-container">
                                    <div class="seller-info-container">
                                        <div class="seller-info-value">{$seller->city|escape:'html':'UTF-8'}</div>
                                    </div>
                                </div>
                            </li>
                        {/if}
                    </div>
                    {if $show_rating}
                        <div class="average-rating-container">
                            <div class="average-rating">
                                <a href="{$url_seller_comments|escape:'html':'UTF-8'}"
                                    title="{l s='View comments about' mod='jmarketplace'} {$seller_name|escape:'html':'UTF-8'}">
                                    {section name="i" start=0 loop=5 step=1}
                                        {if $averageMiddle le $smarty.section.i.index}
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M9.40987 4.34976L9.4905 4.57174L9.49901 4.59516C9.60342 4.88265 9.69518 5.13531 9.7942 5.33636C9.90135 5.55391 10.0363 5.75323 10.2493 5.90801C10.4623 6.06279 10.6936 6.12951 10.9336 6.16419C11.1554 6.19623 11.4241 6.20543 11.7298 6.21589L11.7546 6.21674L11.9907 6.22483C13.1576 6.26483 13.9787 6.29412 14.5393 6.39C15.1343 6.49176 15.1892 6.6296 15.2005 6.66433C15.2117 6.69907 15.2484 6.84283 14.8268 7.27488C14.4297 7.68196 13.7826 8.18832 12.862 8.90656L12.6758 9.05185L12.6562 9.06718C12.415 9.25531 12.2031 9.42065 12.0425 9.57695C11.8687 9.74609 11.7208 9.936 11.6394 10.1864C11.5581 10.4369 11.5661 10.6774 11.6073 10.9164C11.6453 11.1373 11.7196 11.3956 11.8041 11.6896L11.811 11.7135L11.8762 11.9405C12.1988 13.0626 12.4247 13.8526 12.5067 14.4154C12.5938 15.0128 12.4797 15.1075 12.4501 15.129C12.4206 15.1505 12.2952 15.2297 11.754 14.9623C11.2441 14.7104 10.5626 14.2514 9.59503 13.5979L9.39932 13.4657L9.37866 13.4517C9.12521 13.2805 8.90248 13.1301 8.7042 13.0256C8.48963 12.9126 8.26332 12.8306 8 12.8306C7.73668 12.8306 7.51037 12.9126 7.2958 13.0256C7.09751 13.1301 6.87476 13.2806 6.6213 13.4518L6.60068 13.4657L6.40497 13.5979C5.43743 14.2514 4.75588 14.7104 4.24599 14.9623C3.70481 15.2297 3.57941 15.1505 3.54987 15.129C3.52032 15.1075 3.40618 15.0128 3.49326 14.4154C3.57531 13.8526 3.8012 13.0626 4.12375 11.9405L4.189 11.7135L4.19588 11.6896C4.28039 11.3956 4.35467 11.1373 4.39273 10.9164C4.43392 10.6774 4.44192 10.4369 4.36055 10.1864C4.27918 9.936 4.13131 9.74609 3.95751 9.57695C3.7969 9.42065 3.58496 9.25531 3.34381 9.06717L3.32417 9.05185L3.13797 8.90656C2.21744 8.18832 1.57033 7.68196 1.17316 7.27488C0.751608 6.84283 0.788253 6.69907 0.799538 6.66433C0.810823 6.6296 0.865677 6.49176 1.46067 6.39C2.02127 6.29412 2.84242 6.26483 4.00931 6.22483L4.24535 6.21674L4.27024 6.21589C4.57593 6.20543 4.84458 6.19623 5.06639 6.16419C5.30641 6.12951 5.53766 6.06279 5.7507 5.90801C5.96373 5.75323 6.09865 5.55391 6.2058 5.33636C6.30482 5.13531 6.39658 4.88265 6.50099 4.59516L6.5095 4.57174L6.59013 4.34976C6.98876 3.25234 7.27037 2.48043 7.53479 1.97689C7.81543 1.44247 7.96348 1.43289 8 1.43289C8.03652 1.43289 8.18457 1.44247 8.46521 1.97689C8.72963 2.48043 9.01124 3.25234 9.40987 4.34976Z"
                                                    stroke="#151515" stroke-opacity="0.24" />
                                            </svg>
                                        {else}
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M6.12017 4.17905C6.90627 2.01495 7.29931 0.932893 8 0.932893C8.70069 0.932893 9.09373 2.01494 9.87983 4.17905L9.96046 4.40104C10.1839 5.01607 10.2956 5.32359 10.5432 5.5035C10.7908 5.68341 11.1178 5.69462 11.7718 5.71703L12.0078 5.72512C14.3089 5.804 15.4595 5.84343 15.676 6.50983C15.8925 7.17622 14.9849 7.8844 13.1696 9.30076L12.9834 9.44605C12.4675 9.84858 12.2096 10.0498 12.115 10.3409C12.0204 10.632 12.1108 10.9465 12.2915 11.5754L12.3568 11.8024C12.9929 14.0152 13.3109 15.1216 12.744 15.5335C12.1772 15.9453 11.2232 15.301 9.31517 14.0122L9.11945 13.88C8.57721 13.5138 8.30608 13.3306 8 13.3306C7.69392 13.3306 7.42279 13.5138 6.88055 13.88L6.68483 14.0122C4.77684 15.301 3.82284 15.9453 3.25598 15.5335C2.68911 15.1216 3.00714 14.0152 3.64321 11.8024L3.70845 11.5754C3.88922 10.9465 3.97961 10.632 3.88502 10.3409C3.79044 10.0498 3.53249 9.84858 3.01659 9.44605L2.83039 9.30076C1.01512 7.8844 0.107486 7.17622 0.32401 6.50983C0.540534 5.84343 1.69108 5.804 3.99219 5.72512L4.22823 5.71703C4.88219 5.69462 5.20918 5.68341 5.4568 5.5035C5.70443 5.32359 5.81613 5.01607 6.03954 4.40104L6.12017 4.17905Z"
                                                    fill="#F9B813" fill-opacity="0.81" />
                                            </svg>
                                        {/if}
                                    {/section}
                                </a>
                            </div>
                        </div>
                    {/if}
                </div>
                <div class="shot-desscription_">
                    {if $show_short_description}
                        {assign var="shortdescription" value=$seller->short_description}
                        <div class="seller-description">{$shortdescription nofilter}</div>
                    {/if}
                </div>
                <div class="btn-suives">
                    {if $show_followers}
                        <div class="number_of_followers follow-counter">
                            <svg width="27" height="28" viewBox="0 0 27 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="0.5" y="0.503906" width="26" height="26" rx="4.5" fill="white" stroke="#C5C5C5"></rect>
                                <path d="M19.2422 13.9697L8.24128 13.9697" stroke="#575757" stroke-width="1.2"></path>
                                <path d="M13.7422 8.38325L13.7422 19.5557" stroke="#575757" stroke-width="1.2"></path>
                            </svg>
                            {if $followers > 1 }
                                <p>{$followers|intval} Abonnés</p>
                            {else}
                                <p>{$followers|intval} Abonné</p>
                            {/if}
                        </div>
                    {/if}


                    <a class="btn btn-suivre btn-list" title="{l s='Add to favorite seller' mod='jmarketplace'}"
                        href="{$url_favorite_seller|escape:'html':'UTF-8'}">
                        {assign var="id_customer" value=$seller->id_customer}

                        {assign var="id_seller" value="SELECT `id_seller` FROM `ps_seller` WHERE `id_customer` = $id_customer"}
                        {assign var="id_customer_seller" value=Db::getInstance()->getValue($id_seller)}
                        {assign var="customerid" value=$customer.id}
                        {assign var="check_favorit" value="SELECT `id_customer` FROM `ps_seller_favorite` WHERE `id_seller` = $id_customer_seller AND `id_customer` = $customerid"}
                        {assign var="id_customer_favoret" value=Db::getInstance()->getValue($check_favorit)}
                        {if empty($id_customer_favoret)}
                            {l s='Suivre' mod='jmarketplace'}
                        {else}
                            {l s='Suivi(e)' mod='jmarketplace'}
                        {/if}
                    </a>
                </div>
            </div>
        </div>
    {/block}
    {if $customer.is_logged}
        <div class="tabs-links">
            <button class="tab link-active">Articles</button>
            <button class="tab ">évaluation</button>
        </div>
        <div class="tabs-content">
            <div class="content-lows active ">
                {if  !empty($products)}
                    <div id="js-product-list">
                        <div class="products row">
                            {assign var="count" value=0}
                            {assign var="empty" value="0"}
                            {foreach from=$products item="product"}
                                {if isset($productIdList) && $product.id_product|in_array:$productIdList}
                                    {block name='product_miniature'}
                                        {include file='catalog/_partials/miniatures/product.tpl' product=$product productClasses='col-xs-6 col-md-3 col-xl-3'}
                                    {/block}
                                    {assign var="empty" value="1"}
                                {/if}
                                {assign var="count" value=$count+1}
                            {/foreach}
                            {if $empty == "0"}
                                {if $lang_iso == "en"}
                                    <p class="alert alert-info">
                                        There is no product
                                    </p>
                                {else}
                                    <p class="alert alert-info">
                                        Aucun produit
                                    </p>
                                {/if}
                            {/if}
                        </div>
                    </div>
                {else}
                    {if $lang_iso == "en"}
                        <p class="alert alert-info">
                        This seller have doesn't have any products listed.
        </p>
        {else}
        <p class="alert alert-info">
            Ce vendeur n'a pas encore de produits listés.
                        </p>
                    {/if}
                {/if}

            </div>
            <div class="content-lows">
                {include file='module:jmarketplace/views/templates/front/comments.tpl'}
            </div>
        </div>


    {/if}
    {hook h='displayMarketplaceFooterProfile'}
    {hook h='displayMarketplaceSellerProfileFooter'}
{/block}

{block name='page_footer'}
    <footer class="page-footer">


        {if $seller_me}
            <a href="{$seller_account_link|escape:'html':'UTF-8'}" class="account-link">

                {if $use_icons == 'fontawesome'}
                    <i class="fa fas fa-chevron-left"></i>
                {else}
                    <i class="material-icons">keyboard_arrow_left</i>
                {/if}
                <span>
                    {l s='Back to your seller account' mod='jmarketplace'}</span>
            </a>
        {else}
            <a href="javascript: history.go(-1)" class="account-link">
                {if $use_icons == 'fontawesome'}
                    <i class="fa fas fa-chevron-left"></i>
                {else}
                    <i class="material-icons">keyboard_arrow_left</i>
                {/if}
                <span>
                    {l s='Go back' mod='jmarketplace'}</span>
            </a>
        {/if}
        <a href="{$urls.base_url|escape:'html':'UTF-8'}" class="account-link">
            {if $use_icons == 'fontawesome'}
                <i class="fa fas fa-home"></i>
            {else}
                <i class="material-icons">home</i>
            {/if}
            <span>
                {l s='Home' mod='jmarketplace'}</span>
        </a>
    </footer>
{/block}