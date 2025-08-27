{if !empty($banners)}
    <style>

        .banner_flash .banner_item{
            box-shadow: none !important;
            align-items: end;
        }
        .section_heading .short_description {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 400;
            font-size: 15.8231px;
            line-height: 30px;
            text-align: center;
            color: #FE8000;
            margin: 0;
        }

        .section_heading .title {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 500;
            font-size: 31px;
            text-align: center;
            color: #030C1A;
            margin: 0;
        }
        .section_heading {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            flex-direction: column;
        }

        .section_heading .description {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 400;
            font-size: 15px;
            line-height: 25px;
            text-align: center;
            color: #363F4D;
            margin: 0;
            max-width: 610px;
        }
        .banner_flash_container {
            max-width: 85%;
            margin-left: 2rem;
            width: 100%;
            margin-bottom: 1.5rem;
        }
        .banner_flash_detail{
            display: flex;
            flex-direction: column;
            gap: 5px;
            max-width:75%;
        }
        .banner_flash_detail .title_block svg{
            position: absolute;
            top: 0;
            left: 7rem;
        }
        .banner_flash_detail .title_block {
          position:relative;
        }
        .banner_flash_detail .title {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 600;
            font-size: 32px;
            line-height: 120%;
            color: #FFFFFF;
            margin:0;
        }
        .banner_flash_detail .description {
            font-style: normal;
            font-weight: 400;
            font-size: 16px;
            line-height: 21px;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.71);
            margin: 0;
        }
        .banner_flash{
            gap:25px;
        }
        .vente_flash_container {
            display: flex;
            gap: 50px;
            flex-direction: column;
            margin-block: 5rem;
        }
        .vente_flash_container .banner_flash{
            margin-block:0 ;
        }
        .section_container.banner_flash {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }
        .section_container.banner_flash .banner_item{
            height:300px;
        }

        @media screen and (max-width: 850px){

            .vente_flash_container .banner_flash {
                flex-direction: column;
                height: 600px;
                max-width: 100%;
                width: 90%;
            }
            .vente_flash_container .banner_item{
                width: 100%;
                min-height: 300px;
            }
        }
    </style>
<section class="vente_flash_container section_container">
    <div class="section_heading ">
    
        <p class="short_description">{$header.header_shortDescription}</p>
        <h1 class="title">{$header.header_title}</h1>
        <h3 class="description">{$header.header_description}</h3>
    </div>
    <section class=" section_container banner_flash">
        {foreach from=$banners item=banner}
            <div class="banner_item" style="background-image: url('{$banner.image}')" alt="{$banner.category_name}">
                <div class="banner_flash_container ">
                    <div class="banner_flash_detail">
                        <a href="{$banner.url}" class="title_block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 35 35" fill="none">
                                <path d="M35 17.5C35 27.165 27.165 35 17.5 35C7.83502 35 0 27.165 0 17.5C0 7.83502 7.83502 0 17.5 0C27.165 0 35 7.83502 35 17.5Z" fill="white"/>
                                <path d="M19.7197 13.5H15.2803C14.8661 13.5 14.5303 13.1642 14.5303 12.75C14.5303 12.3358 14.8661 12 15.2803 12H21.2803C21.8326 12 22.2803 12.4477 22.2803 13V19C22.2803 19.4142 21.9445 19.75 21.5303 19.75C21.1161 19.75 20.7803 19.4142 20.7803 19V14.5608L13.591 21.7501C13.2981 22.0429 12.8232 22.0429 12.5303 21.7501C12.2374 21.4572 12.2374 20.9823 12.5303 20.6894L19.7197 13.5Z" fill="#1E1E1E"/>
                            </svg>
                            <h2 class="title">{$banner.title|unescape:'html' nofilter} </h2>
                        </a>
                        <p class="description">{$banner.description}</p>
                    </div>
                </div>
            </div>
        {/foreach}

    </section>
</section>
{/if}