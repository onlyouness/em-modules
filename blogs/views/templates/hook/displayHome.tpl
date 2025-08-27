<div class="solutions_container">
    <div class="pos_title">
        <h2>
            <span>{l s="Solutions" mod="blogs"}</span>
        </h2>
    </div>
    <div class="solutions solutionsSwiper swiper">
        <div class="swiper-wrapper">
            {foreach from=$blogs item=blog}
                <div class="swiper-slide">
                    <a class="blog_detail" href="{$link->getModuleLink('blogs', 'blog', ['id_blog' => $blog.id])}">
                        <img src="/modules/blogs/img/{$blog.image}" />
                        <div class="detail_solution_container">
                            <h3 >{$blog.title}</h3>
                            <p>{$blog.shortDescription|truncate:120 nofilter}</p>
                        </div>
                    </a>
                </div>
            {/foreach}
            <div>
            </div>
        </div>

        <!-- Add Pagination and Navigation -->
        <div class="arrows">
            <div class="prev-arrow arrow_btn">

                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 14 13" fill="none">
                    <path
                        d="M3.70295 7.4L7.16661 10.8637L7.27268 10.9697L7.16661 11.0758L6.10595 12.1365L5.99989 12.2425L5.89382 12.1365L0.363491 6.60612L0.257424 6.50006L0.363491 6.39399L5.89382 0.863661L5.99989 0.757595L6.10595 0.863661L7.16661 1.92432L7.27268 2.03039L7.16661 2.13645L3.70307 5.6H13.5303H13.6803V5.75V7.25V7.4H13.5303H3.70295Z"
                        fill="#C42128" stroke="#C42128" stroke-width="0.3" />
                </svg>
            </div>
            <div class="next-arrow arrow_btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 14 13" fill="none">
                    <path
                        d="M10.297 7.4L6.83339 10.8637L6.72732 10.9697L6.83339 11.0758L7.89405 12.1365L8.00011 12.2425L8.10618 12.1365L13.6365 6.60612L13.7426 6.50006L13.6365 6.39399L8.10618 0.863661L8.00011 0.757595L7.89405 0.863661L6.83339 1.92432L6.72732 2.03039L6.83339 2.13645L10.2969 5.6H0.469727H0.319727V5.75V7.25V7.4H0.469727H10.297Z"
                        fill="#C42128" stroke="#C42128" stroke-width="0.3" />
                </svg>
            </div>
        </div>


    </div>
</div>
<script>
    const swiper = new Swiper('.solutionsSwiper', {
        speed: 400,
        spaceBetween: 20,
        slidesPerView: 3,
        direction: 'horizontal',
        navigation: {
            nextEl: '.next-arrow',
            prevEl: '.prev-arrow',
        },
        breakpoints: {
                // when window width is >= 320px
                320: {
                    slidesPerView: 1,
                    spaceBetween: 20
                },
                // when window width is >= 480px
                480: {
                    slidesPerView: 2,
                    spaceBetween: 30
                },
                // when window width is >= 640px
                640: {
                    slidesPerView: 2,
                    spaceBetween: 40
                },
                1700:{
                    slidesPerView:3,
                }
            }
    });
</script>