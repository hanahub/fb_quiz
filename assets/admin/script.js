jQuery(document).ready(function($) {
    $("#fb-button-add").click(function(e) {
        var choice = $("#fb-input-choice").val();
        var row = '';
        
        if (choice != '') {
            if ($('.fb-choice').length == 0) {
                row = '<div class="fb-choices-header"><span class="fb-correct">Correct</span></div><div class="clear"></div>';
            }
            row += ' \
                    <div class="fb-choice"> \
                        <a href="javascript:void(0)" class="fb-remove"><i class="fb-icon icon-minus-squared"></i></a> \
                        <a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle"><i class="fb-icon icon-resize-vertical"></i></a> \
                        <span class="fb-choice-name ui-state-default ui-sortable-handle">' + choice + '</span> \
                        <input type="radio" name="fb-correct-choice" class="fb-correct-choice"/> \
                    </div> \
                   ';
            $("#fb-choices").append(row);
                        
            
        } else {
            alert("Please enter a value to add.")
        }
        
        $("#fb-input-choice").val('');
    });
    
    $(".fb-remove").live("click", function(e) {
        $(this).parents(".fb-choice").fadeOut(300, function() {
            $(this).parents(".fb-choice").remove();    
        });
        
    });
    
    $("#fb-category-add-toggle").click(function(e) {
        $("#category-add").show();
    });
    
    /**/
    
    $("#fb-category-add-submit").click(function(e) {
        var cat = $('#fb-newcategory').val().trim();        
        if (cat != '') {
            var data = {'action' : 'fb_add_cat', 'cat' : cat};
            
            $.post(ajaxurl, data, function(response) {
                var result = JSON.parse(response);
                var row = '';
                //$('.fb-wrap' ).unblock(); 
                if (result['status'] == 1) {
                    $row = $("#categorychecklist li:eq(0)").clone();
                    $row.find("input").attr("value", result['id']);
                    $row.find("input").attr("checked", "checked");
                    $row.find("span").html(' ' + cat);
                    $("#categorychecklist").append($row);
                    $('#fb-newcategory').val('');
                }
            });
        }
    });
    
    $("#fb-publish").click(function(e) {
        var title = $("#fb-question-title").hasClass("tmce-active") ? tinyMCE.get("fb-question-title").getContent() : $("#fb-question-title").val();
        var correct_explanation = $("#fb-correct-explanation").hasClass("tmce-active") ? tinyMCE.get("fb-correct-explanation").getContent() : $("#fb-correct-explanation").val();        
        var type = $("#fb-question-type").val();
        var status = "publish";
        var points = $("#fb-point").val();
        var author = $("#fb-author").val();
        var cats = [];
        var $cats_list = $('#categorychecklist input[type="checkbox"]');
        $cats_list.each(function(i, obj) {
            if ($(obj).is(":checked")) {
                cats.push($(obj).val());
            }
        });
        
        var choices = {'choices': [], 'correct': []};
        var $choices_list = $("#fb-choices .fb-choice");
        $choices_list.each(function(i, obj) {
            choices['choices'].push([i + 1, $(obj).find('.fb-choice-name').html()]);
            if ($(obj).find('.fb-correct-choice').is(":checked")) {
                choices['correct'] = i + 1;
            }
        });
        
        var params = { 'title': title, 'correct_explanation': correct_explanation, 'type': type, 'status': status, 'points': points, 'author': author, 'cats': cats, 'choices': choices };
        var data = {};
        if ($("#fb-edit-id").val() != '')
            data = {'action' : 'fb_edit_question', 'params' : params, 'id' : $("#fb-edit-id").val()};
        else
            data = {'action' : 'fb_add_question', 'params' : params };
        
        $( '.fb-wrap' ).block({ message: null, overlayCSS: {background: '#fff', opacity: 0.6} });    
        $.post(ajaxurl, data, function(response) {
            var result = JSON.parse(response);
            if (result['status'] == 1) {
                $('.fb-wrap' ).unblock();
                window.location.href = result['redirect_url'];
            }
        });
    });
    
    $( "#fb-choices" ).sortable();
    $( "#fb-choices" ).disableSelection();
    
});