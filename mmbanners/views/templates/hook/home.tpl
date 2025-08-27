

{if !empty($banners)}
<style>
    .banner_container {
        display: flex;
        gap: 10px;
        min-height: 300px;
        margin-block: 5rem;
    }
    .banner_item {
        width: 50%;
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        box-shadow: inset 308px 0px 50px rgba(227, 227, 227);
    }
    .banner_item_container {
        max-width: 85%;
        margin-left: auto;
        width: 100%;
    }
    .banner_item_container .shortDescription{
        font-family: 'Rubik';
        font-style: normal;
        font-weight: 400;
        font-size: 19.2264px;
        color: #FF8301;
        mix-blend-mode: normal;
    }
    .banner_item_container .title{
        font-family: 'Rubik';
        font-style: normal;
        font-weight: 400;
        font-size: 33.6462px;
        color: #030C1A;
        mix-blend-mode: normal;
    }
    .banner_item_container .description{
        font-family: 'Rubik';
        font-style: normal;
        font-weight: 400;
        font-size: 19.2264px;
        color: #030C1A;
        mix-blend-mode: normal;
    }
    .banner_item_detail {
        display: flex;
        justify-content: center;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 2.5rem;
    }
    @media screen and (max-width: 850px){
        .banner_container{
            flex-direction: column;
        }
        .banner_container .banner_item{
            min-height: 300px;
            width: 88%;
            margin-inline: auto;
        }
    }
</style>
<section class="banner_container section_container">
    {foreach from=$banners item=banner}
        <a href="{$banner.url}" class="banner_item" style="background-image: url('{$banner.image}')" alt="{$banner.product_name}">
           <div class="banner_item_container">
            <div class="banner_item_detail">
                <h4 class="shortDescription">{$banner.shortDescription} </h4>
                <h2 class="title">{$banner.title} </h2>
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="187" height="10" viewBox="0 0 187 10" fill="none">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0 0.0546875H187V9.05469H0V0.0546875Z" fill="url(#pattern0_251_754)"/>
                    <defs>
                        <pattern id="pattern0_251_754" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0_251_754" transform="scale(0.00534759 0.111111)"/>
                        </pattern>
                        <image id="image0_251_754" width="187" height="9" xlink:href="data:image/png;base64,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"/>
                    </defs>
                </svg>
            </div>
            <p class="description">{$banner.description}</p>
           </div>
        </a>
        {foreachelse}
        <h4>Aucun baniere n'a été trouvé.</h4>
    {/foreach}
    <div></div>
</section>
{/if}