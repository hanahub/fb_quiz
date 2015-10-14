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
                        <a href="javascript:void(0)" class="fb-move"><i class="fb-icon icon-resize-vertical"></i></a> \
                        <span>' + choice + '</span> \
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
        $(this).parents(".fb-choice").remove();
    });
});