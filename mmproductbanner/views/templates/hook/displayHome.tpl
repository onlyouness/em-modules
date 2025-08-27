<section>
<a href="{{$link->getProductLink($product)}}" class="banner_product_container" style="background-image:url('{$image}');">
    <div class="bg-discount">
        {if !empty($bannerInfo)}
            <div class="banner_discount_details">
                <h4 class="shortDescriptionB">{$bannerInfo.shortDescription}</h4>
                <h2 class="titleB">{$bannerInfo.title}</h2>
                <p class="descriptionB"> {$bannerInfo.description}</p>
            </div>
        {/if}
    </div>
</a>
</section>