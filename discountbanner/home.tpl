<section class="banner_item_container">

    {if !empty($group_banner_1)}

        <a href="{$group_banner_1.link}" class="banner_item" style="background-image: url('{$group_banner_1.image}')">

            <div class="banner_item_container">

                <div class="banner_item_detail">

                    <p class="shortDescription {if $group_banner_1.group == 3}firstDescColor{else}{/if}">
                        {$group_banner_1.description} </p>
                    <h2 class="title {if $group_banner_1.group == 3}firstTitleColor{else}{/if}">
                        {$group_banner_1.title} </h2>

                </div>

            </div>

        </a>

    {/if}

    {if !empty($group_banner_2)}

        <a href="{$group_banner_2.link}" class="banner_item" style="background-image: url('{$group_banner_2.image}')"
            alt="{$group_banner_2.title}">

            <div class="banner_item_container">

                <div class="banner_item_detail">
                    <p class="shortDescription {if $group_banner_2.group == 3}secondDescColor{else}{/if}">
                        {$group_banner_2.description} </p>
                    <h2 class="title {if $group_banner_2.group == 3}secondTitleColor{else}{/if}">
                        {$group_banner_2.title} </h2>
                </div>
            </div>
        </a>
    {/if}

</section>

<style>
    .banner_item {
        width: 100%;
        height: 100%;
        margin: 0 !important;
    }
    .firstDescColor{
        color:white;
    }
    .firstTitleColor{
        color: white;
    }
    .secondDescColor{
        color:#593412;
    }
    .secondTitleColor{
        color: #75C05B;
    }


    .banner_item_container {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        width: 100%;
        height: 100%;
    }

    .secondcolor {}

    .banner_item_detail {
        display: flex;
        justify-content: start;
        flex-direction: column;
        align-items: start;
        height: 100%;
        width: 100%;
        padding-top: 3rem;
    }

    .banner_item_detail .shortDescription {

        font-family: 'Raleway';
        font-style: italic;
        font-weight: 500;
        font-size: 19px;
        line-height: 35px;
        text-transform: uppercase;
        margin: 0 !important;
        color: rgba(255, 255, 255, 0.71);
    }

    .banner_item_detail .title {

        font-family: 'Arimo';
        font-style: normal;
        font-weight: 700;
        font-size: 27px;
        line-height: 36px;
        letter-spacing: -0.03em;

        color: #6D4621;


    }
</style>