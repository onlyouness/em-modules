{extends file='page.tpl'}


{block name="content"}

    <section class="solutions_blog_container">
        <div class="block_detail_container">
            <div class="block_detail_title">
                <h1>{$blog.blog_title}</h1>
                <div class="short_description">{$blog.blogshortDescription nofilter}</div>
            </div>
            <div class="block_detail">
                <div class="block_banner">
                    {if isset($blog.blog_image) }
                    
                        <div class="block_detail_img">

                            <img src="/modules/blogs/img/{$blog.blog_image}" alt="{$blog.blog_title}" />
                        </div>
                    {/if}
                    <div class="detail_description">

                        <div>

                        </div>
                    </div>
                </div>
                <div class="descripiton_section">
                    {if !empty($paragraphs) }
                        {foreach from=$paragraphs item=paragraph name=paragraphs}
                            {assign var="index" value=$smarty.foreach.paragraphs.index+1}
                            <!-- Add 1 to index as Smarty uses 0-based indexing -->
                            {assign var="class" value="right"}
                            <!-- Default to 'left' class -->
                            {if $index == 1 || $index == 3 || $index == 5}
                                {assign var="class" value="left"}
                                <!-- If it's 1, 3, or 5, set to 'right' -->
                            {/if}

                            <div class="paragraph_items">
                                <div
                                    class="description_paragraph {if !isset($paragraph.image)}full_width{else if ( isset($paragraph.image) && ( (empty($paragraph.title) && empty($paragraph.description) )))}time{else}half_width{/if} {$class}">
                                    <h2>{if $paragraph.title}{$paragraph.title}{/if}</h3>
                                        {if $paragraph.description} {$paragraph.description nofilter}{/if}
                                </div>
                                {if isset($paragraph.image)}
                                    <div
                                        class="section_detail_img  {if ( isset($paragraph.image) && ( (empty($paragraph.title) && empty($paragraph.description) )))}full_image{/if} ">
                                        <img src="/modules/blogs/img/{$paragraph.image}" alt="{$paragraph.title}" />
                                    </div>
                                {/if}
                            </div>
                        {/foreach}

                    {/if}
                </div>


            </div>

            <div id="product_container" class="block_sections">
                {if isset($sections)}
                    {foreach from=$sections item=bsection}

                        <div class="block_section_container">
                            <div class="pos_title">
                                <h2>
                                    <span>{$bsection.title_product}</span>
                                </h2>
                                <div class="row swiper swiperBlogDetail">
                                    <div class="swiper-wrapper">
                                        {if isset($bsection.products)}
                                            {foreach from=$bsection.products item="product"}
                                                {include file="catalog/_partials/miniatures/product.tpl" product=$product}
                                            {/foreach}
                                        {/if}


                                    </div>
                                    <div class="prev-blog-arrow arrow_blog_btn">

                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 14 13" fill="none">
                                        <path
                                            d="M3.70295 7.4L7.16661 10.8637L7.27268 10.9697L7.16661 11.0758L6.10595 12.1365L5.99989 12.2425L5.89382 12.1365L0.363491 6.60612L0.257424 6.50006L0.363491 6.39399L5.89382 0.863661L5.99989 0.757595L6.10595 0.863661L7.16661 1.92432L7.27268 2.03039L7.16661 2.13645L3.70307 5.6H13.5303H13.6803V5.75V7.25V7.4H13.5303H3.70295Z"
                                            fill="#C42128" stroke="#C42128" stroke-width="0.3" />
                                    </svg>
                                </div>
                                <div class="next-blog-arrow arrow_blog_btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 14 13" fill="none">
                                        <path
                                            d="M10.297 7.4L6.83339 10.8637L6.72732 10.9697L6.83339 11.0758L7.89405 12.1365L8.00011 12.2425L8.10618 12.1365L13.6365 6.60612L13.7426 6.50006L13.6365 6.39399L8.10618 0.863661L8.00011 0.757595L7.89405 0.863661L6.83339 1.92432L6.72732 2.03039L6.83339 2.13645L10.2969 5.6H0.469727H0.319727V5.75V7.25V7.4H0.469727H10.297Z"
                                            fill="#C42128" stroke="#C42128" stroke-width="0.3" />
                                    </svg>
                                </div>

                                </div>
                            </div>

                        </div>
                    {/foreach}
                {/if}

            </div>


    </section>
    <script>
        const swiper = new Swiper('.swiperBlogDetail', {
            speed: 400,
            slidesPerView: 7,
            direction: 'horizontal',
            loop:true,
            autoplay: {
                delay: 2500,
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
                1400: {
                    slidesPerView: 6,
                    spaceBetween: 40
                },
                1700: {
                    slidesPerView: 7,
                }
            }
        });
    </script>

{/block}