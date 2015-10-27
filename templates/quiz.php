<?php

    global $wpdb, $FB_TABLES;
    
    $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLES['quizzes'] . " WHERE id=" . $quiz_id );
    
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
        foreach ($q_questions as $question) {
            
            print_r($question);
        }
    ?>
</div>


