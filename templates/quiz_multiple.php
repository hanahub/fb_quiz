<?php
    global $user_ID, $FB_URL;
    
    $student_id = $user_ID;
    wp_enqueue_script('angular', '//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular.min.js', array(), '1.4.7', false);
    wp_enqueue_script('angular-animate', '//ajax.googleapis.com/ajax/libs/angularjs/1.4.7/angular-animate.min.js', array(), '1.4.7', false);
    wp_register_script( 'fb-multiple-quiz-script', FBQUIZ_URL . 'assets/front-end/multiple_quiz.js');
    wp_enqueue_script( 'fb-multiple-quiz-script' );    
    
?>

<div class="fb_wrap multiple" ng-app="MultipleQuizModule" ng-controller="MultipleQuizController" style="visibility: hidden;">    
    <h3 class="fb_quiz_title">{{ quizTitle }}</h3>
    <p ng-bind-html="description | trustAsHtml" class="fb_quiz_description"></p>
    <div class="fb_result" ng-if="immediateFeedback == 1 && allViewed == 1" ng-bind-html="quizResult | trustAsHtml"></div>
    <div class="fb_timer" id="fb_timer" ng-show="showTimer == 1">00:00</div>
    <div class="fb_quiz_content">        
        <div class="fb_quiz_left">                                                                                                 
            <a ng-if="allowSkip == 1" ng-click="changeQuestion($index)" class="fb_quiz_nav" ng-class="{'fb_noskip': allowSkip == 0, 'fb_answered' : answer[$index].length > 0, 'fb_active': questionNum == $index + 1}" ng-repeat="question in questions track by $index">{{$index + 1}}</a>
            <a ng-if="allowSkip == 0" class="fb_quiz_nav" ng-class="{'fb_noskip': allowSkip == 0, 'fb_answered' : answer[$index].length > 0, 'fb_active': questionNum == $index + 1}" ng-repeat="question in questions track by $index">{{$index + 1}}</a>
        </div>
        <div class="fb_quiz_right">
            <div class="fb_row fb_question" ng-class="question['type']" id="question_{{question['id']}}">
                <div class="fb_question_wrap" ng-if="!question['answered']">
                    <div class="fb_question_title" ng-bind-html="quesionTitle | trustAsHtml"></div>
                    <ul data-id="{{ question['id'] }}" data-type="{{ question['type'] }}" class="fb_question_content {{ question['type'] }}" ng-if="question['type'] == 'sorting'">
                        <li order-no="{{ $index }}" data-id="{{ choice[0] }}" ng-repeat="choice in question['choices']['choices'] track by $index">
                            <div class="fb_choice_wrapper">
                                <a href="javascript:void(0)" class="fb-move ui-state-default ui-sortable-handle">
                                    <i class="fb-icon icon-resize-vertical"></i>
                                    <label class="fb-move ui-state-default ui-sortable-handle">{{ choice[1] }}</label>
                                </a>
                            </div>
                        </li>                    
                    </ul>
                    <ul data-id="{{ question['id'] }}" data-type="{{ question['type'] }}" class="fb_question_content {{ question['type'] }}" ng-if="question['type'] != 'sorting'">
                        <li order-no="{{ $index }}" data-id="{{ choice[0] }}" ng-repeat="choice in question['choices']['choices'] track by $index">
                            <div class="fb_choice_wrapper checkbox" ng-if="question['type'] == 'multiple'">
                                <input ng-checked="answer[questionNum-1].indexOf(choice[0]) > -1" ng-click="answerClicked()" type="checkbox" name="fb_question_{{ $index }}" id="fb_choice_{{ $index }}"/>
                                <label for="fb_choice_{{ $index }}" ng-bind-html="choice[1] | trustAsHtml"></label>
                            </div>                        
                            <div class="fb_choice_wrapper radio" ng-if="question['type'] == 'single'">
                                <input data-x="{{answer[questionNum-1].indexOf(choice[0])}}" ng-checked="answer[questionNum-1].indexOf(choice[0]) > -1" ng-click="answerClicked()" type="radio" name="fb_question[]" id="fb_choice_{{ $index }}"/>
                                <label for="fb_choice_{{ $index }}" ng-bind-html="choice[1] | trustAsHtml"></label>
                            </div>                        
                            
                        </li>
                    </ul>
                </div>
                <div class="fb_answer_button_wrap fade clearfix" ng-if="immediateFeedback == 1 && !question['answered'] && (answer[questionNum-1].length > 0 || question['type'] == 'sorting')"><a class="fb_answer_button fb_link" ng-click="answerButtonClicked()">Answer</a></div>
                <div class="fb_answer_wrap fade" ng-if="!!question['answered']" ng-bind-html="question['answered'] | trustAsHtml"></div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="fb-row" ng-if="allowSkip == 0 && questionNum < quiz['questions'].length">
            <input type="button" id="fb_next_question" value="Next" ng-click="nextQuestion()"/>
        </div>
    </div>
    
    <div class="fb_quiz_footer">
        <input type="button" id="fb_submit" value="Finish" ng-click="submitAnswers()" ng-if="immediateFeedback != 1"/>
        
        <input type="button" id="fb_take_quiz_again" value="Take Quiz Again" onclick="javascript: location.href = '<?php echo $FB_URL['quizzes'] . $quiz_id; ?>';" ng-if="immediateFeedback == 1 && allViewed == 1"/>
        <input type="button" id="fb_view_all" value="View All Quiz Results" onclick="javascript: location.href = '<?php echo $FB_URL['my_quizzes']; ?>';" ng-if="immediateFeedback == 1 && allViewed == 1"/>
        
        <input type="hidden" id="fb_quiz_id" value="<?php echo $quiz_id; ?>" ng-init="quiz_id=<?php echo $quiz_id; ?>"/>
        <input type="hidden" id="fb_student_id" value="<?php echo $student_id; ?>" ng-init="student_id=<?php echo $student_id; ?>"/>
    </div>
</div>


