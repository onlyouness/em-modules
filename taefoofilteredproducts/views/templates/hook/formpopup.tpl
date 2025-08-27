        {* {if $page.page_name != "authentication" &  $page.page_name != "password"}



            {if isset($customer)}



                {if !$customer['is_logged']} *}
                    <dialog open class="no-login">
                        <div class="pop-customer">
                            <span onclick="toggleLoginPopup()">X</span>
                            {assign var="lang_iso" value=$language.iso_code}
                            {if $lang_iso == 'en'}
                                <p>Please<a href="{$link->getPageLink('authentication')}"> log in </a> or create an account to access
                                    your
                                    space.</p>
                            {else}
                                <p>Veuillez vous <a href="{$link->getPageLink('authentication')}"> connecter </a> ou créer un compte
                                    pour
                                    accéder à votre espace.</p>
                            {/if}
                            <div class="space-link">
                                {hook h="displayCustomerLoginFormAfter"}
                            </div>
                        </div>
            </dialog>