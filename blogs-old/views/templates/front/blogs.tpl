{extends file='page.tpl'}


{block name="content"}

    <section class="solutions_blog_container">
        <div class="block_detail_container">
            <div class="block_detail">
                <div class="block_detail_img">
                    <img src="/modules/blogs/img/{$blog.blog_image}" alt="{$blog.blog_title}" />
                </div>
                <h1>{$blog.blog_title}</h1>
                <div>{$blog.blog_description nofilter}</div>
                <a href="#product_container">
                    <span>Nos Produits</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="15" viewBox="0 0 13 15" fill="none">
                        <path opacity="0.8" fill-rule="evenodd" clip-rule="evenodd"
                            d="M7 10.531L10.8256 6.70541L12.1014 7.98122L6.0507 14.0319L0 7.98122L1.27581 6.70541L5 10.4296V0.96814H7V10.531Z"
                            fill="white" />
                    </svg>
                </a>
            </div>


        </div>

        <div id="product_container" class="block_sections">
            {foreach from=$sections item=bsection}

                <div class="block_section_container">
                    <div class="pos_title">
                        <h2>
                            <a href="#">
                                <span>{$bsection.title_product}</span>
                            </a>
                        </h2>
                        <div class="row swiper swiperBlogDetail">
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
        const swiper = new Swiper('.swiperBlogDetail', {
            speed: 400,
            slidesPerView: 7,
            direction: 'horizontal',
            autoplay: {
        delay: 2500,
        disableOnInteraction: false,
        },
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

{/block}