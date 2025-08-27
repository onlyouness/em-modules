{if isset($reassurances) }
<section class="reassurances_section">

    {foreach $reassurances as $res}
        <div class="res_item">
            <img src="/modules/mmreassurances/img/{$res.image}"  alt="{$res.title}"/>
            <h3>{$res.title}</h3>
            <p>{$res.description}</p>
        </div>
    {/foreach}
</section>
{/if}