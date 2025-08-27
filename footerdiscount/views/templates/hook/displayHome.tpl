<style>

    .banner_discount_container .submittion {
        padding: 16px 44px;
        background: #FE8000;
        border-radius: 4px;
        color: white;
        border: none;
        margin: 0;
        cursor: pointer;

    }
</style>
{if $image && $title}
<section>

    <div class="banner_discount_container"  style="background-image:url('{$image}');">
        <div class="bg-discount" >
            <div class="banner_discount_details">
               <img style="width: 40px;height: 40px" src="/modules/footerdiscount/img/logoBanner.png" alt="emeuble logo"/>
                <h4 class="shortDescriptionB">{$shortDescription}</h4>
                <h2 class="titleB">{$title}</h2>
                {if $description}
                    <p class="descriptionB"> {$description}</p>
                {/if}

            </div>
            <a class="submittion" href="{$link->getProductLink($product)}">
                En Savoir Plus
            </a>

        </div>
    </div>
</section>
{/if}