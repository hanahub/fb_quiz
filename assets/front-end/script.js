jQuery(document).ready(function($) {
    
    $( ".fb_question_content.sorting" ).sortable();
    
    $("#fb_submit").click(function(e) {
        
        var quiz_id = $("#fb_quiz_id").val();
        var student_id = $("#fb_student_id").val();        
        var answers = [];
        
        var questions = $(".fb_question_content");        
        for (i = 0; i < questions.length; i++) {
            qid = $(questions[i]).attr("data-id");
            qtype = $(questions[i]).attr("data-type");
            
            dumb = [[], []];
            $choices_list = $(questions[i]).children("li");
            
            $choices_list.each(function(j, obj) {
                choice_id = $(obj).attr("data-id");                
                if (qtype != 'sorting') {
                    if ($(obj).find('input').is(":checked")) {
                        dumb[0].push(choice_id);
                    }
                } else {
                    dumb[0].push(choice_id);
                }
                order_no = $(obj).attr("order-no");
                dumb[1].push(order_no);
            });
            answers.push({'qid' : qid, 'answers' : dumb});
        }
        
        var params = { 'quiz_id': quiz_id, 'student_id': student_id, 'answers': answers };
        var data = {'action' : 'fb_add_answer', 'params' : params };
        $('.fb_wrap').block({ message: null, overlayCSS: {background: '#fff', opacity: 0.6} });    
        $.post(ajaxurl, data, function(response) {
            var result = JSON.parse(response);
            if (result['status'] == 1) {
                $('.fb_wrap').unblock();
                window.location.href = result['redirect_url'];
            }
        });
    });
});