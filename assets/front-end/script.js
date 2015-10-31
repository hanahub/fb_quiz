
function fb_add_answer(params) {
    var data = {'action' : 'fb_add_answer', 'params' : params };
    $ = jQuery;
    $('.fb_wrap').block({ message: null, overlayCSS: {background: '#fff', opacity: 0.6} });    
    $.post(ajaxurl, data, function(response) {
        var result = JSON.parse(response);
        if (result['status'] == 1) {
            $('.fb_wrap').unblock();
            window.location.href = result['redirect_url'];
        }
    });        
}

