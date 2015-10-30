function fb_block(selector) {
    jQuery(selector).block({ message: null, overlayCSS: {background: '#fff', opacity: 0.6} });    
}

function fb_unblock(selector) {
    jQuery(selector).unblock({fadeOut:  0});
}