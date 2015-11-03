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
