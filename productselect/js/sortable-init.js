$(document).ready(function () {
    var fixHelper = function (e, ui) {
        ui.children().each(function () {
            $(this).width($(this).width());
        });
        return ui;
    };

    var $sortable = $("#sortable");

    if ($sortable.length > 0) {
        $sortable.sortable({
            helper: fixHelper,
            axis: "y",
            update: function (event, ui) {
                var order = $sortable.find("tr").map(function () {
                    return $(this).data("id");
                }).get();
                console.log("New order:", order);
                updatePositions(order);
            },
        }).disableSelection();
    } else {
        console.warn("#sortable not found");
    }
    function updatePositions(order) {
        var btn = $('.btn_add_condition');
        btn.attr("disable", "true")
        $.ajax({
            url: urlProductSelect,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ order: order }),
            success: function (response) {
                console.log('Success', response.message);
                var ajax_confirmation = $('#ajax_confirmation');
                ajax_confirmation.show().text(response.message);
                setTimeout(() => {
                    ajax_confirmation.hide();
                }, 5000);
            },
            error: function (error) {
                console.error('Error:', error);
            }
        });
    }
    function updateFormAction(url) {
        $('#deleteProduct').attr('action', url);
    }
});
