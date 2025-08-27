<div class="container">
    <h1>{$output}</h1>
    <h1>Configurate the Banner Discount</h1>
    <div class="row">
        <div class="col-sm-12">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-control-label">Choose product that is in discount! </label>
                    <select name="discount_product" class="form-control custom-select">
                        {foreach from=$products item=product}
                            <option value="{$product.id_product}">{$product.name}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-control-label">Banner Image </label>
                    <input name="banner_image" class="form-control" type="file" placeholder="Image" required>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>

            </form>
        </div>
    </div>
</div>