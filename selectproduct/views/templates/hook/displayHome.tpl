<div id="product_container" class="block_sections">
    <div class="">
        <div class="">
            <h2>
                <span>{$title}</span>
            </h2>
            <div class="row swiper">
                <div class="swiper-wrapper">
                    {if isset($products)}
                        {foreach from=$products item=product}
                            {include file="catalog/_partials/miniatures/product.tpl" product=$product}
                        {/foreach}
                    {/if}


                </div>
                <div class="next-blog-arrow arrow_blog_btn"></div>
                <div class="prev-blog-arrow arrow_blog_btn"></div>

            </div>
        </div>

    </div>


</div>