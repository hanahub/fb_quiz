<?php

    global $wpdb, $FB_TABLE, $FB_URL, $user_ID;
    
    $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " WHERE id=" . $quiz_id );
    $quiz = $dumb[0];            
    
    $rows = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['answers'] . " WHERE quiz_id=" . $quiz_id . " AND student_id=" . $user_ID);
?>

<div class="fb_wrap">
    <h3 class="fb_title"><?php echo $quiz->title; ?></h3>
    <table class="quizzes" id="quizzes-table">
        <thead>
            <tr>                
                <th id="column-attempt-id" class=""></th>
                <th id="column-quiz-title" class="">Quiz Title</th>
                <th id="column-quiz-attempts" class="">Total number of attempts</th>
                <th id="column-quiz-action" class=""></th>                
            </tr>
        </thead>
        <tbody id="the-list">
        </tbody>
    </table>
</div>    