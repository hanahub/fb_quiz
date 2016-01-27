function fb_block(selector) {
    jQuery(selector).block({ message: null, overlayCSS: {background: '#fff', opacity: 0.6} });    
}

function fb_unblock(selector) {
    jQuery(selector).unblock({fadeOut:  0});
}

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