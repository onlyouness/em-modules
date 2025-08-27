<div class="up-faq">
    <h2 class="h2 title mt-6-t">
        Faq
        <hr>
    </h2>
    <div class="container">

        <div class="faq-container">
            {if !empty($faqs_section_1)}
                {foreach from=$faqs_section_1 item=faq}
                    <div class="faq-row">
                        <div class="faq-item">
                            <div class="faq-question">{$faq.question}</div>
                            <div class="icon-container"><i class="fas fa-chevron-right"></i></div>
                        </div>
                        <div class="faq-answer">
                            <p>{$faq.response}</p>
                        </div>
                    </div>
                {/foreach}
            {/if}
        </div>
        <div class="faq-container">
            {if !empty($faqs_section_2)}
                {foreach from=$faqs_section_2 item=faq}
                    <div class="faq-row">
                        <div class="faq-item">
                            <div class="faq-question">{$faq.question}</div>
                            <div class="icon-container"><i class="fas fa-chevron-right"></i></div>
                        </div>
                        <div class="faq-answer">
                            <p>{$faq.response}</p>
                        </div>
                    </div>
                {/foreach}
            {/if}

        </div>
    </div>
</div>

