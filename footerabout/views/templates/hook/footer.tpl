<div class="col-md-3 footer-l">
    {if $showLogo}
        {renderLogo}
    {else}
        <img class="logo-footer lazy" src="{$image_url}" data-original="{$image_url}" style="display: inline;" />
    {/if}
    <p class="p-footer">{$description}</p> <span class="mode-paiement"></span>
</div>