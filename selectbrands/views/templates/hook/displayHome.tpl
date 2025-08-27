<section class="logos-sec-one">
    <div class="container">
        {* <div class="title-sec"><span>I nostri</span> marchi principali</div> *}
        <div class="title-sec">{$title}</div>
        <p>{$description}</p>
        <div class="images-hol-sec">
            {foreach from=$brands item=brand}
                <a href="{$link->getManufacturerLink($brand->id, $brand->link_rewrite)}">
                    <img src="{$link->getMediaLink("/img/m/`$brand->id`.jpg")}" class="lazy"
                        data-original="{$link->getMediaLink("/img/m/`$brand->id`.jpg")}" alt="{$brand->name|escape}"
                        style="display: none;">
                </a>
            {/foreach}
        </div>
    </div>
</section>