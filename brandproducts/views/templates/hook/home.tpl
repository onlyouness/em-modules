
    <section class="brands_container">
        <div id="product_container" class="block_sections">
            {foreach from=$data item=bsection}
                <div class="block_section_container">
                    <div class="pos_title">
                        <h2>
                            {$bsection.title}
                        </h2>
                        <div class="row swiper brandSwiper ">
                            <div class="swiper-wrapper">
                                {foreach from=$bsection.products item="product"}
                                    {include file="catalog/_partials/miniatures/product.tpl" product=$product}
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </section>

    <script>
        const swiper = new Swiper('.brandSwiper', {
            speed: 400,
            slidesPerView: 7,
            direction: 'horizontal',
            navigation: {
                nextEl: '.next-blog-arrow',
                prevEl: '.prev-blog-arrow',
            },
            breakpoints: {
                // when window width is >= 320px
                320: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },
                // when window width is >= 480px
                480: {
                    slidesPerView: 3,
                    spaceBetween: 30
                },
                // when window width is >= 640px
                640: {
                    slidesPerView: 4,
                    spaceBetween: 40
                },
                1700: {
                    slidesPerView: 7,
                }
            }
        });
    </script>
