
function fb_add(data) {
    fb_block('.fb-wrap');
    jQuery.post(ajaxurl, data, function(response) {
        var result = JSON.parse(response);
        if (result['status'] == 1) {
            fb_unblock('.fb-wrap');                    
            window.location.href = result['redirect_url'];
        }
    });
}
