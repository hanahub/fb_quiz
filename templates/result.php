<?php

    global $wpdb, $FB_TABLE, $FB_URL, $quizzes;
    
    
    $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['answers'] . " WHERE id=" . $result_id );
    
    if (count($dumb) == 0)  {
        echo '<p>error</p>';
        return;
    }
    
    $a_data = $dumb[0];
    $a_answers = unserialize($a_data->answers);
    $quiz_id = $a_data->quiz_id;
    
    $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " WHERE id=" . $quiz_id );
    $q_data = $dumb[0];
    
    $output = array();
    $i = 1;
    $correct_count = $incorrect_count = 0;
    $total_count = count($a_answers);
    $correct_points = $total_points = 0;
    
    foreach ($a_answers as $answer) {
        
        $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['questions'] . " WHERE id=" . $answer['qid'] );
        $q = $dumb[0];
        $choices = unserialize($q->choices);
        
        $student_answer = $answer['answers'][0];
        $correct = 'Wrong';
        if ( $student_answer == $choices['correct'] ) {
            $correct = 'Correct';
            $correct_count ++;
            $correct_points += $q->points;
        } else {
            $correct = 'Wrong';
            $incorrect_count ++;
        }
        
        $total_points += $q->points;
        
        $student_choices = array();
        $correct_choices = array();        
        foreach ($student_answer as $ch) {
            $student_choices[] = $quizzes->findChoiceName($choices['choices'], $ch);
        }
        foreach ($choices['correct'] as $ch) {
            $correct_choices[] = $quizzes->findChoiceName($choices['choices'], $ch);
        }
        
        array_push($output, array(
                        'correct'           => $correct,
                        'title'             => $i . '. ' . $q->title . ' (' . $q->points . ' points)',
                        'student_choices'   => $student_choices,
                        'correct_choices'   => $correct_choices,
                        'explanation'       => $q->correct_explanation
                    )
                );
        $i ++;        
    }
    
    $result_percentage = round((100 / $total_points) * $correct_points);
    if ($result_percentage >= $q_data->passing_percentage) {
        $result_status = 'Passed';
    } else {
        $result_status = 'Failed';
    }
    
    $a_title = $q_data->title . ' Results &nbsp;&nbsp;&nbsp;&nbsp;' . $correct_points . '/' . $total_points . ' points (' . $result_percentage . '%) ' . $result_status;
            
?>

<div class="fb_wrap">
    <h3 class="fb_quiz_title"><?php echo $a_title; ?></h3>    
    
    <?php
        foreach ($output as $o) {
    ?>            
            <div class="fb_row">
                <div class="fb_result_status"><?php echo $o['correct']; ?></div>
                <div class="fb_result_content">
                    <div class="fb_question_title"><?php echo $o['title']; ?></div>
                    <div class="fb_answers">
                        <div class="fb_student_answers">
                            <label>- Studnet Answers</label>
                            <ul><li><?php echo implode('</li><li>', $o['student_choices']); ?></li></ul>
                        </div>
                        <?php if ($o['correct'] != 'Correct') :?>
                        <div class="fb_correct_answers">
                            <label>- Correct Answers</label>
                            <ul><li><?php echo implode('</li><li>', $o['correct_choices']); ?></li></ul>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="fb_explanation">
                        <fieldset>
                            <legend>Correct Answer Explanation</legend>
                            <p><?php echo $o['explanation']; ?></p>
                        </fieldset>
                    </div>
                    
                </div>
            </div>
    <?php            
        }
    ?>
    
    <div class="fb_quiz_footer">
        <input type="button" id="fb_take_quiz_again" value="Take Quiz Again" onclick="javascript: location.href = '<?php echo $FB_URL['quizzes'] . $quiz_id; ?>';"/>
        <input type="button" id="fb_back" value="Back"/>
        <input type="hidden" id="fb_quiz_id" value="<?php echo $quiz_id; ?>"/>
        <input type="hidden" id="fb_student_id" value="<?php echo $student_id; ?>"/>
    </div>
</div>


