<?php

    global $wpdb, $FB_TABLE;
    
    $student_id = 1;
    $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " WHERE id=" . $quiz_id );
    
    if (count($dumb) == 0)  {
        echo '<p>error</p>';
        return;
    }
    
    
    $q_data = $dumb[0];
    
    $q_title = $q_data->title;
    $q_layout = $q_data->layout;
    
    $q_description = $q_data->description;
    $q_questions = unserialize($q_data->questions);
    $q_num_of_questions = $q_data->num_of_questions;
    $q_connected_to = unserialize($q_data->connected_to);
    $q_passing_percentage = $q_data->passing_percentage;
    
    $q_allow_skipping = $q_data->allow_skipping;        
    $q_immediate_feedback = $q_data->immediate_feedback;
    $q_random_questions = $q_data->random_questions;
    $q_random_choices = $q_data->random_choices;        
?>

<div class="fb_wrap {{ layout }}" ng-app="MultipleQuizModule" ng-controller="MultipleQuizController">    
    <h3 class="fb_quiz_title">{{ title }}</h3>
    <p ng-bind-html="description | trustAsHtml" class="fb_quiz_description"></p>
    
    <div class="fb_quiz_content">
        <div class="fb_quiz_left">
            <a href="javascript:void(0)" class="fb_quiz_nav" ng-class="{'fb_active': $index == 0}" ng-repeat="question in questions track by $index">{{$index + 1}}</a>
        </div>
        <div class="fb_quiz_right">
            <div class="fb_row fb_question" ng-repeat="question in questions track by $index">
                <div class="fb_question_title">{{ ($index + 1) + '. ' + question['title'] + ' (' + question['points'] + ' points)'; }}</div>
                <ul data-type="{{ question['type'] }}" class="fb_question_content {{ question['type'] }}" ng-if="question['type'] == 'sorting'">
                    <li order-no="{{ $index }}" data-id="{{ choice[0] }}" ng-repeat="choice in question['choices']['choices'] track by $index">
                        <div class="fb_choice_wrapper"><a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle"><i class="fb-icon icon-resize-vertical"></i></a></div>
                        <label class="fb-move ui-state-default ui-sortable-handle">{{ choice[1] }}</label>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="fb_quiz_footer">
        <input type="button" id="fb_submit" value="Finish"/>
        <input type="hidden" id="fb_quiz_id" value="<?php echo $quiz_id; ?>" ng-model="quiz_id" ng-init="quiz_id=<?php echo $quiz_id; ?>"/>
        <input type="hidden" id="fb_student_id" value="<?php echo $student_id; ?>"/>
    </div>
</div>


