/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*/

$(document).ready(function(){
    if($('#ETS_HOTDEALS_HOOK_TO').val()=='custom_hook')
    {
       $('#ETS_HOTDEALS_HOOK_TO').parent().find('p').show(); 
    }
    else
    {
        $('#ETS_HOTDEALS_HOOK_TO').parent().find('p').hide();
    }
    $('#ETS_HOTDEALS_HOOK_TO').change(function(){
        if($(this).val()=='custom_hook')
        {
           $(this).parent().find('p').show(); 
        }
        else
        {
            $(this).parent().find('p').hide();
        }
    });
    $('input[name="ETS_HOTDEALS_SELECT_DISCOUNTED_PRODUCTS"]').click(function(){
        if($(this).val()==1)
        {
            $('.product_list_id').slideUp(200);
            $('.product_count').slideDown(200);
            $('.form-group.product-order').slideDown(200);
        }
        else
        {
            $('.product_count').slideUp(200);
            $('.product_list_id').slideDown(200);
            $('.form-group.product-order').slideUp(200);
        }      
    }); 
    if($('input[name="ETS_HOTDEALS_SELECT_DISCOUNTED_PRODUCTS"]:checked').val()==1)
    {
        $('.product_list_id').slideUp(200);
        $('.product_count').slideDown(200);
        $('.form-group.product-order').slideDown(200);
    }
    else
    {
        $('.product_count').slideUp(200);
        $('.product_list_id').slideDown(200);
        $('.form-group.product-order').slideUp(200);
    }
    $('input[name="ETS_HOTDEALS_DISPLAY_TYPE"]').click(function(){
        if($(this).val()=='carousel_slider')
            $('.display_slider').slideDown(200);
        else
            $('.display_slider').slideUp(200);
    }); 
    if($('input[name="ETS_HOTDEALS_DISPLAY_TYPE"]:checked').val()=='carousel_slider')
        $('.display_slider').slideDown(200);
    else
        $('.display_slider').slideUp(200);
    
    SearchProduct();
    $('.product_list_id .col-lg-8').append($('#productlist').html());

    $('input[name=ETS_HOTDEALS_AUTO_PLAY_SLIDER]').change(function () {
        if($(this).val() == 1){
            $(this).closest('form').find('.autoplay_slider').removeClass('hide');
        }
        else{
            $(this).closest('form').find('.autoplay_slider').addClass('hide');
        }
    });
});
$(document).on('click','.delete-product-id',function(e){
   var idProduct= $(this).attr('data-id');
   $this= $(this);
   var productIds=$('#ETS_HOTDEALS_PRODUCT_IDS').val().split(",");
   var indexProduct = productIds.indexOf(idProduct);
   if(indexProduct !=-1)
        productIds.splice(indexProduct, 1);
    productIds = productIds.join(',');
   $.ajax({
    	type: 'POST',
    	headers: { "cache-control": "no-cache" },
    	url: window.location.href,
    	async: true,
    	cache: false,
    	dataType : "json",
        data: {
            action: 'updateProductList',
            type: 'delete',
            productIds: productIds,
        },

    	success: function(jsonData)
    	{
            $this.closest('li').remove();
    		$('#ETS_HOTDEALS_PRODUCT_IDS').val(productIds);
            if(jsonData.status){
                showSuccessMessage(jsonData.msg);
            }
    	}
   });
    
});
Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
function SearchProduct()
{
    
    var input = $('#ETS_HOTDEALS_SEARCH_PRODUCT_IDS');
    var ybc_featuredcat_ajax_url='../modules/ets_hotdeals/ajax_products_list.php';
    $(input).autocomplete(ybc_featuredcat_ajax_url,{
		minChars: 1,
		autoFill: true,
		max:20,
		matchContains: true,
		mustMatch:true,
		scroll:false,
		cacheLength:0,
        source: function( request, response ) {
            response(function (data) {
                return data;
            });
        },
		formatItem: function(item) {
			return '<img src="'+item[2]+'" width="22px">'+item[1]+' - '+item[0]+' - '+item[3]+(item[5]?' (Ref:'+item[5]+')':'');
		}
	}).result(ybcAddAccessory);
}
var ybcAddAccessory = function(event, data, formatted)
{
	if (data == null)
		return false;
	var productId = data[1]+'-'+(data[4] ? data[4] : 0);
	var productName = data[0];
    var productImg =data[2];
    if(!$('#ETS_HOTDEALS_PRODUCT_IDS').val())
    {
        $('#ETS_HOTDEALS_PRODUCT_IDS').val(productId);
        $('.form-group.product-order').slideUp(200);
        $('.product_list_id .col-lg-8 ul').append('<li><img src="'+productImg+'" /><span class="product-search-info"><span class="product-name">'+productName+'</span>'+(data[3]!='Null'?'<span class="attribute-name"> '+data[3]+'</span>':'')+(data[5]?' (Ref:'+data[5]+')':'')+' </span><span class="delete-product-id" data-id="'+productId+'">delete</span></li>');
        $.ajax({
            	type: 'POST',
            	headers: { "cache-control": "no-cache" },
            	url: window.location.href,
            	async: true,
            	cache: false,
            	dataType : "json",
            	data: 'action=updateProductList&type=add&productIds='+$('#ETS_HOTDEALS_PRODUCT_IDS').val(),
            	success: function(jsonData)
            	{
                    if(jsonData.status){
                        showSuccessMessage(jsonData.msg);
                    }
            	}
           });
    }  
    else
    {
        var productIds=$('#ETS_HOTDEALS_PRODUCT_IDS').val().split(",");
        if(productIds.indexOf(productId)==-1)
        {
            productIds.push(productId);
            $('#ETS_HOTDEALS_PRODUCT_IDS').val(productIds.join(','));
            $('.product_list_id .col-lg-8 ul').append('<li><img src="'+productImg+'" /><span class="product-search-info"><span class="product-name">'+productName+'</span>'+(data[3]!='Null'?'<span class="attribute-name"> '+data[3]+'</span>':'')+(data[5]?' (Ref:'+data[5]+')':'')+'</span> <span class="delete-product-id" data-id="'+productId+'">delete</span></li>');
            $.ajax({
            	type: 'POST',
            	headers: { "cache-control": "no-cache" },
            	url: window.location.href,
            	async: true,
            	cache: false,
            	dataType : "json",
            	data: 'action=updateProductList&type=add&productIds='+$('#ETS_HOTDEALS_PRODUCT_IDS').val(),
            	success: function(jsonData)
            	{
                    if(jsonData.status){
                        showSuccessMessage(jsonData.msg);
                    }
            	}
           });
        }    
    }
    $('#ETS_HOTDEALS_SEARCH_PRODUCT_IDS').val('');
};