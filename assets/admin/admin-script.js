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

$( ".fb-data-table" ).DataTable({
    "aoColumnDefs": [
      { 'bSortable': false, 'aTargets': [ 0 ] }
    ],
    "order": [[ 1, "desc" ]],
    "pageLength": 25,    
    "searching": false,
    "dom": 'rtlp',    
    "language": {
        "paginate": {
          "previous": "<",
          "next": ">"
        }
    },
    "bJQueryUI": false,
});
$("#quizzes-table-wrap .dataTables_paginate, #questions-table-wrap .dataTables_paginate").append('<input type="button" id="fb_delete_selected" class="button" value="Delete Selected"/>');

var spanSorting = '<span class="arrow-hack sort">&nbsp;&nbsp;&nbsp;</span>',
    spanAsc = '<span class="arrow-hack asc">&nbsp;&nbsp;&nbsp;</span>',
    spanDesc = '<span class="arrow-hack desc">&nbsp;&nbsp;&nbsp;</span>';

$(".fb-data-table").on('click', 'th', function() {
    $(".fb-data-table thead th").each(function(i, th) {
        $(th).find('.arrow-hack').remove();
        var html = $(th).html(),
            cls = $(th).attr('class');
        switch (cls) {
            case 'sorting_asc' : 
                $(th).html(html+spanAsc); break;
            case 'sorting_desc' : 
                $(th).html(html+spanDesc); break;
            default : 
                $(th).html(html+spanSorting); break;
        }
    });     
});    
$(".fb-data-table th").first().click().click();

$("#fb_from_date").datepicker({
    dateFormat: 'yy-mm-dd'
});
$("#fb_to_date").datepicker({
    dateFormat: 'yy-mm-dd'
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

