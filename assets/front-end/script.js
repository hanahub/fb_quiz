
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

function fb_answer_html(dumb) {
    var your_answer = "";
    var correct_answer = "";
    for (i = 0; i < dumb['student_choices'].length; i++) {
        your_answer += '<li>' + dumb['student_choices'][i] + '</li>';
    }
    
    if (dumb['correct'] == 'Incorrect') {
        correct_answer = '<div class="fb_correct_answers"><label>Correct Answer</label>';
        correct_answer += '<ul>';
        for (i = 0; i < dumb['correct_choices'].length; i++) {                    
            correct_answer += '<li>' + dumb['correct_choices'][i] + '</li>';
        }
        correct_answer += '</ul>';
        correct_answer += '</div>';
    }
    
    var html = ' \
            <div class="fb_your_answer"> \
                <div class="fb_result_content"> \
                    <div class="fb_question_title">' + dumb['title'] + '</div> \
                    <div class="fb_answers"> \
                        <div class="fb_student_answers"> \
                            <label>Your Answer</label> \
                            <ul>' + your_answer + '</ul> \
                        </div> \
                        ' + correct_answer + ' \
                    </div> \
                    <div class="fb_explanation"> \
                        <fieldset> \
                            <legend>Correct Answer Explanation</legend> \
                            <p>' + dumb['explanation'] + '</p> \
                        </fieldset> \
                    </div> \
                </div> \
            </div> \
        ';
        
    return html;    
}

var totalSeconds = 0;
var timerIntervalId;
function startTimer(display) {
    var minutes, seconds;
    timerIntervalId = setInterval(function () {
        ++totalSeconds;
        minutes = parseInt(totalSeconds / 60, 10);
        seconds = parseInt(totalSeconds % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.text(minutes + ":" + seconds);        
    }, 1000);
}
