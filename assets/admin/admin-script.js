$ = jQuery;
function fb_add(data) {
    fb_block('.fb-wrap');
    $.post(ajaxurl, data, function(response) {
        var result = JSON.parse(response);
        if (result['status'] == 1) {
            fb_unblock('.fb-wrap');                    
            window.location.href = result['redirect_url'];
        }
    });
}

$(".fb-checklist span > a").live("click", function(e) {    
    $(this).closest("span").remove();
});

$(".fb_submitdelete").live("click", function(e) {
    var r = confirm("Are you sure you want to do this?");
    if (r == false) {
        e.preventDefault();
        return;
    }
});

$("#fb_delete_selected").live("click", function(e) {        
    if ($(".fb-check-item:checked").length == 0) return;
    var r = confirm("Are you sure you want to do this?");
    if (r == false) {
        e.preventDefault();
        return;
    }
    
    url = trash_url;
    $(".fb-check-item:checked").each(function(i, obj) {
        url = url + '&id[]=' + $(obj).val();
    });    
    window.location.href = url;
});

