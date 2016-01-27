<?php

    global $wpdb, $FB_TABLE, $user_ID;
    
    $student_id = $user_ID;
    $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " WHERE id=" . $quiz_id );
    
    if (count($dumb) == 0)  {
        echo '<p>error</p>';
        return;
    }
    
    
    $q_data = $dumb[0];
    
    $q_title = stripcslashes($q_data->title);
    $q_description = stripcslashes($q_data->description);
    $q_questions = unserialize($q_data->questions);
    $q_num_of_questions = $q_data->num_of_questions;
    $q_connected_to = unserialize($q_data->connected_to);
    $q_passing_percentage = $q_data->passing_percentage;
    $q_layout = $q_data->layout;
    $q_allow_skipping = $q_data->allow_skipping;        
    $q_immediate_feedback = $q_data->immediate_feedback;
    $q_random_questions = $q_data->random_questions;
    $q_random_choices = $q_data->random_choices;        
    
?>

<div class="fb_wrap">
    <h3 class="fb_quiz_title"><?php echo $q_title . ' (Passing ' . $q_passing_percentage . '%)'; ?></h3>
    <p class="fb_quiz_description"><?php echo $q_description; ?></p>
    
    <?php
        
        $questions = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['questions'] . " WHERE id IN (" . implode(", ", $q_questions) . ") ORDER BY FIELD(id, " . implode(", ", $q_questions) . ")" );
        
        $i = 1;
        if ($q_random_questions == 1) shuffle($questions);
        
        foreach ($questions as $q) {
            $choices = unserialize($q->choices);
            echo '<div class="fb_row">';
                echo '<div class="fb_question_title">' . $i . '. ' . stripslashes($q->title) . ' (' . $q->points . ' points)</div>';
                echo '<ul id="fb_question_' . $q->id . '" data-id="' . $q->id . '" data-type="' . $q->type . '" class="fb_question_content ' . $q->type . '">';
                
                $j = 1;
                if ($q_random_choices == 1) shuffle($choices['choices']);
                if ($q->type == 'sorting') {
                    
                    $q_choices = unserialize($q->choices);                    
                    while ($choices['choices'] == $q_choices['choices']) {                        
                        shuffle($choices['choices']);                        
                    }
                    foreach ($choices['choices'] as $choice) {
                        
                        $choice_id = "fb_choice_{$i}_{$j}";
                        $choice_html = '
                                <li order-no="' . $j . '" data-id="' . $choice[0] . '"><div class="fb_choice_wrapper"><a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle"><i class="fb-icon icon-resize-vertical"></i></a></div>
                                <label class="fb-move ui-state-default ui-sortable-handle" for="' . $choice_id . '">' . $choice[1] . '</label></li>
                            ';
                        
                        echo $choice_html;
                        $j++;
                    }
                    
                } else {                    
                    
                    if ($q->type == 'single') $input_type = 'radio';
                    else $input_type = 'checkbox';
                    
                    foreach ($choices['choices'] as $choice) {
                        
                        $choice_id = "fb_choice_{$i}_{$j}";
                        $choice_html = '
                                <li order-no="' . $j . '" data-id="' . $choice[0] . '"><div class="fb_choice_wrapper"><input class="fb_radio_checkbox" type="' . $input_type . '" name="fb_question_' . $i . '" id="' . $choice_id . '"/></div><label for="' . $choice_id . '">' . $choice[1] . '</label></li>
                            ';
                        
                        echo $choice_html;
                        $j++;
                    }
                }
                echo '</ul>';                
            echo '</div>';
            
            $i++;
        }
    ?>
    
    <div class="fb_quiz_footer">
        <p class="finish_quiz_text">You must answer all the question in order to finish the quiz.</p>
        <input type="button" id="fb_submit" value="Finish" disabled/>
        <input type="hidden" id="fb_quiz_id" value="<?php echo $quiz_id; ?>"/>
        <input type="hidden" id="fb_student_id" value="<?php echo $student_id; ?>"/>
    </div>
</div>
<script>
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
        
        if (num_questions == num_answered.length) {
            var params = { 'quiz_id': quiz_id, 'student_id': student_id, 'answers': answers };
            fb_add_answer(params);
        }
        
    });
    
    var num_answered = [];
    var num_questions = get_num_questions();
    $(".fb_radio_checkbox").click(function(e) {        
        var type = $(this).attr('type');
        var name = $(this).attr('name');
        
        if (type == 'checkbox') {
            if ($("input[name=" + name + "]:checked").length > 0)  {
                if (num_answered.indexOf(name) < 0) num_answered.push(name);
            } else {
                num_answered.remove(name);
            }
        } else {
            if (num_answered.indexOf(name) < 0) num_answered.push(name);
        }
        
        if (num_questions == num_answered.length) {
            $("#fb_submit").removeAttr("disabled");
            $(".finish_quiz_text").hide();
        } else {
            $("#fb_submit").attr("disabled", true);
            $(".finish_quiz_text").show();
        }
    });
    
    function get_num_questions() {
        var questions = $(".fb_question_content");
        var num_questions = 0;        
        for (i = 0; i < questions.length; i++) {
            qid = $(questions[i]).attr("data-id");
            qtype = $(questions[i]).attr("data-type");
            
            if (qtype != 'sorting') num_questions ++;
        }
        return num_questions;
    }
    
    
});
</script>


