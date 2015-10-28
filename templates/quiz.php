<?php

    global $wpdb, $FB_TABLE;
    
    $student_id = 1;
    $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " WHERE id=" . $quiz_id );
    
    if (count($dumb) == 0) return;
    
    
    $q_data = $dumb[0];
    
    $q_title = $q_data->title;
    $q_description = $q_data->description;
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
        foreach ($questions as $q) {
            $choices = unserialize($q->choices);
            echo '<div class="fb_row">';
                echo '<div class="fb_question_title">' . $i . '. ' . $q->title . ' (' . $q->points . ' points)</div>';
                echo '<ul id="fb_question_' . $q->id . '" data-id="' . $q->id . '" data-type="' . $q->type . '" class="fb_question_content ' . $q->type . '">';
                
                $j = 1;
                if ($q->type == 'sorting') {
                    
                    foreach ($choices['choices'] as $choice) {
                        
                        $choice_id = "fb_choice_{$i}_{$j}";
                        $choice_html = '
                                <li data-id="' . $choice[0] . '"><div class="fb_choice_wrapper"><a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle"><i class="fb-icon icon-resize-vertical"></i></a></div>
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
                                <li data-id="' . $choice[0] . '"><div class="fb_choice_wrapper"><input type="' . $input_type . '" name="fb_question_' . $i . '" id="' . $choice_id . '"/></div><label for="' . $choice_id . '">' . $choice[1] . '</label></li>
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
        <input type="button" id="fb_submit" value="Finish"/>
        <input type="hidden" id="fb_quiz_id" value="<?php echo $quiz_id; ?>"/>
        <input type="hidden" id="fb_student_id" value="<?php echo $student_id; ?>"/>
    </div>
</div>


