<section class="banner_item_container">
    {if !empty($group_banner_1)}
        <a href="{$group_banner_1.link}" class="banner_item" style="background-image: url('{$group_banner_1.image}')">
            <div class="banner_item_container">
                <div class="banner_item_detail">
                    <p class="shortDescription">{$group_banner_1.description} </p>
                    <h2 class="title">{$group_banner_1.title} </h2>
                </div>
            </div>
        </a>
    {/if}
    {if !empty($group_banner_2)}
        <a href="{$group_banner_2.link}" class="banner_item" style="background-image: url('{$group_banner_2.image}')"
            alt="{$group_banner_2.title}">
            <div class="banner_item_container">
                <div class="banner_item_detail">
                    <p class="shortDescription">{$group_banner_2.description} </p>
                    <h2 class="title">{$group_banner_2.title} </h2>
                </div>
            </div>
        </a>
    {/if}
</section>
<style>
    .banner_item{
        width: 100%;
    }
    .banner_item_container{
        display: flex;
        flex-direction: column;
        gap:1rem;
        width: 50%;
    }
</style>