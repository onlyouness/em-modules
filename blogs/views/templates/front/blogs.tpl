{extends file='page.tpl'}


{block name="content"}

    <section class="solutions_blog_container">
        <div class="block_detail_container">
            <div class="block_detail">
                <div class="block_detail_img">
                    <img src="/modules/blogs/img/{$blog.blog_image}" alt="{$blog.blog_title}" />
                </div>
                <h1>{$blog.blog_title}</h1>
                <div>
                    <div>{$blog.blog_description nofilter}</div>
                    <div class="descripiton_section">
                        {if !empty($paragraphs) }
                            {foreach from=$paragraphs item=paragraph}
                                <div class="paragraph_items">
                                    <div>{$paragraph.description nofilter}</div>
                                    <div class="block_detail_img">
                                        <img src="/modules/blogs/img/{$paragraph.image}" alt="{$paragraph.title}" />
                                    </div>
                                </div>
                            {/foreach}
                        {/if}
                    </div>

                </div>


            </div>

            <div id="product_container" class="block_sections">
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
                                <div class="next-blog-arrow arrow_blog_btn"></div>
                                <div class="prev-blog-arrow arrow_blog_btn"></div>

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



{* const paragraph = document.querySelector(".description_blog");
const image = document.querySelector(".block_detail_img");
const paragraphRest = document.querySelector(".paragraph_rest");

if (
  paragraph.scrollHeight > 600 &&
  window.matchMedia("(min-width: 768px)").matches
) {
  const fullText = paragraph.innerHTML;
  const textBeforeLimit = fullText.slice(0, 1450); // This part will stay in the paragraph
  const textAfterLimit = fullText.slice(1450); // This part will go into the rest paragraph

  // Update the paragraph with the limited text
  paragraph.innerHTML = textBeforeLimit;

  // Update the rest of the paragraph content
  paragraphRest.innerHTML = textAfterLimit; *}