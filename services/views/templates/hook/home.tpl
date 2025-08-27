{if $services}
    
    <style>
        .services {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 45px;
            margin-block: 5rem;
        }
    
        .section_heading {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            flex-direction: column;
        }
    
        .section_content {
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 20px;
            width: 100%;
        }
    
        .bg_container {
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            height: 500px;
            display: flex;
            justify-content: center;
            align-items: end;
        }
    
        .service_container {
            width: 33%;
        }
    
        .content_container {
            width: 75%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            flex-direction: column;
            margin-bottom: 30px;
            margin-inline: auto;
            z-index: 2;
        }
    
        .section_heading .short_description {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 400;
            font-size: 15.8231px;
            line-height: 30px;
            text-align: center;
            color: #FE8000;
            margin-bottom: 0;
        }
    
        .section_heading .title {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 500;
            font-size: 31px;
            text-align: center;
            color: #030C1A;
            margin-bottom: 0;
        }
    
        .section_heading .description {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 400;
            font-size: 15px;
            line-height: 25px;
            text-align: center;
            color: #363F4D;
            margin-bottom: 0;
            max-width: 610px;
        }
    
        .service_sous_title {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 400;
            font-size: 19.2264px;
            text-align: center;
            color: #FF8301;
            margin: 0;
        }
    
        .service_title {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 500;
            font-size: 30px;
            line-height: 30px;
            text-align: center;
            color: #FFFFFF;
            margin-bottom: 20px;
        }
    
        .description_content .service_description {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 400;
            font-size: 14px;
            line-height: 25px;
            flex-grow: 0;
            text-align: center;
            color: white;
            margin: 0;
        }
    
        .description_button {
            padding: 10px 20px;
            background: #FE8000;
            border-radius: 4px;
            border: none;
            color: white;
            font-size: 14px;
            cursor: pointer;
        }
        .services{
            margin-top: 2rem;
        }
    
        @media only screen and (max-width: 850px) {
            .section_content {
                flex-wrap: wrap;
            }
    
            .service_container {
                width: 45%;
            }
        }
        @media only screen and (max-width: 650px) {
            .section_content {
                flex-wrap: wrap;
            }
    
            .service_container {
                width: 80%;
            }
        }
    </style>
    
    <section class="services section_container">
        <div class="section_heading">
            <p class="short_description">{$section.short_description}</p>
            <h1 class="title">{$section.title}</h1>
            <h3 class="description">{$section.description}</h3>
        </div>
        <div class="section_content">
            {foreach $services as $service}
                <article class="service_container">
                    <div class="bg_container overlay_container"
                        style="background-image:url('/modules/services/img/{$service.image}'); position: relative;">
                        <div class="content_container">
                            <h3 class="service_sous_title">Service</h3>
                            <h1 class="service_title">{$service.title}</h1>
                            <div class="description_content" style="display: none;">
                                <p class="service_description">{$service.description}</p>
                            </div>
                            <button type="button" class="description_button">{l s="Read More" mod="services"}</button>
                        </div>
                        <!-- Article-specific overlay -->
                        <div class="article_overlay"
                            style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1;">
                        </div>
                    </div>
                </article>
            {/foreach}
        </div>
    </section>
    
    <script>
        document.querySelectorAll('.description_button').forEach(button => {
            button.addEventListener('click', function() {
                // Find the parent overlay container and associated elements
                const overlayContainer = this.closest('.bg_container');
                const descriptionContent = overlayContainer.querySelector('.description_content');
                const articleOverlay = overlayContainer.querySelector('.article_overlay');
    
                // Toggle visibility
                if (descriptionContent.style.display === 'none' || descriptionContent.style.display ===
                    '') {
                    descriptionContent.style.display = 'block';
                    articleOverlay.style.display = 'block'; // Show overlay
                    this.textContent = 'Read Less'; // Update button text
                } else {
                    descriptionContent.style.display = 'none';
                    articleOverlay.style.display = 'none'; // Hide overlay
                    this.textContent = 'Read More'; // Revert button text
                }
            });
        });
    </script>
    {/if}