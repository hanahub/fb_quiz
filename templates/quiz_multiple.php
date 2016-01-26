<?php
    global $user_ID;
    $student_id = $user_ID;
    wp_enqueue_script('angular', '//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js', array(), '1.4.7', false);
    wp_register_script( 'fb-multiple-quiz-script', FBQUIZ_URL . 'assets/front-end/multiple_quiz.js');
    wp_enqueue_script( 'fb-multiple-quiz-script' );
?>

<div class="fb_wrap multiple" ng-app="MultipleQuizModule" ng-controller="MultipleQuizController">    
    <h3 class="fb_quiz_title">{{ quizTitle }}</h3>
    <p ng-bind-html="description | trustAsHtml" class="fb_quiz_description"></p>
    
    <div class="fb_quiz_content">        
        <div class="fb_quiz_left">
            <a ng-if="allowSkip == 1" ng-click="changeQuestion($index)" class="fb_quiz_nav" ng-class="{'fb_noskip': allowSkip == 0, 'fb_active': questionNum == $index + 1}" ng-repeat="question in questions track by $index">{{$index + 1}}</a>
            <a ng-if="allowSkip == 0" class="fb_quiz_nav" ng-class="{'fb_noskip': allowSkip == 0, 'fb_active': questionNum == $index + 1}" ng-repeat="question in questions track by $index">{{$index + 1}}</a>
        </div>
        <div class="fb_quiz_right">
            <div class="fb_row fb_question">
                <div class="fb_question_title">{{ quesionTitle }}</div>
                <ul data-id="{{ question['id'] }}" data-type="{{ question['type'] }}" class="fb_question_content {{ question['type'] }}" ng-if="question['type'] == 'sorting'">
                    <li order-no="{{ $index }}" data-id="{{ choice[0] }}" ng-repeat="choice in question['choices']['choices'] track by $index">
                        <div class="fb_choice_wrapper"><a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle"><i class="fb-icon icon-resize-vertical"></i></a></div>
                        <label class="fb-move ui-state-default ui-sortable-handle">{{ choice[1] }}</label>
                    </li>                    
                </ul>
                <ul data-id="{{ question['id'] }}" data-type="{{ question['type'] }}" class="fb_question_content {{ question['type'] }}" ng-if="question['type'] != 'sorting'">
                    <li order-no="{{ $index }}" data-id="{{ choice[0] }}" ng-repeat="choice in question['choices']['choices'] track by $index">
                        <div class="fb_choice_wrapper" ng-if="question['type'] == 'multiple'">
                            <input ng-checked="initCheck(choice[0])" ng-click="answerClicked()" type="checkbox" name="fb_question_{{ $index }}" id="fb_choice_{{ $index }}"/>
                        </div>                        
                        <div class="fb_choice_wrapper" ng-if="question['type'] == 'single'">
                            <input ng-checked="initCheck(choice[0])" ng-click="answerClicked()" type="radio" name="fb_question[]" id="fb_choice_{{ $index }}"/>
                        </div>                        
                        <label for="fb_choice_{{ $index }}" ng-bind-html="choice[1] | trustAsHtml"></label>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="fb-row" ng-if="allowSkip == 0 && questionNum < quiz['questions'].length">
            <input type="button" id="fb_next_question" value="Next" ng-click="nextQuestion()"/>
        </div>
    </div>
    
    <div class="fb_quiz_footer">
        <input type="button" id="fb_submit" value="Finish" ng-click="submitAnswers()"/>
        <input type="hidden" id="fb_quiz_id" value="<?php echo $quiz_id; ?>" ng-init="quiz_id=<?php echo $quiz_id; ?>"/>
        <input type="hidden" id="fb_student_id" value="<?php echo $student_id; ?>" ng-init="student_id=<?php echo $student_id; ?>"/>
    </div>
</div>


