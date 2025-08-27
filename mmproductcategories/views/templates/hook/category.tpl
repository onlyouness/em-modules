<div class="categories">
    <div class="cat_item">
        <span class="label">{l s="SKU" mod="mmproductcategories"}:</span>
        <div class="value">{$ref}</div>
    </div>
    <div class="cat_item">
        <span class="label">{l s="Category" mod="mmproductcategories"}:</span>
        <div class="value">
            {foreach from=$categories item=category name=catLoop}
                <span>{$category.name}</span>
                {if !$smarty.foreach.catLoop.last}, {/if}
            {/foreach}
        </div>
    </div>
</div>