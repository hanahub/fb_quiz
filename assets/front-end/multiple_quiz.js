angular.module('MultipleQuizModule', ['ngAnimate'])
.controller('MultipleQuizController', ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
    
    var config = { headers: {      
            'Accept': 'application/json;odata=verbose'
        }
    };
    
    
    var result = [], answersViewed = [];
    var answer_id = 0;    
    
    $scope.questionNum = 1;    
    $scope.allowSkip = 0;
    $scope.answer = [];
    $scope.allViewed = 0;
    $scope.updateAnswer = 0;
    $scope.quizResult = '';
    
    function init() {      
        var url = ajaxurl + '?action=fb_get_quiz&qid=' + $scope.quiz_id;        
        
        //fb_block(".fb_wrap");
        jQuery(".fb_wrap").hide();
        $http.get(url, config).success(function(response) {
            $scope.quiz = response['result'];            
            
            $scope.questions = $scope.quiz['questions'];
            $scope.question = $scope.questions[$scope.questionNum - 1];
            $scope.quizTitle = $scope.quiz['title'] + ' (Passing ' + $scope.quiz['passing_percentage'] + '%)';        
            $scope.quesionTitle = '<span class="fb_question_title">' + $scope.questionNum + '. ' + $scope.question['title'] + '</span><span class="fb_points">(' + $scope.question['points'] + ' points)</span>';
            $scope.layout = $scope.quiz['layout'];
            $scope.allowSkip = $scope.quiz['allow_skipping'];                
            $scope.immediateFeedback = $scope.quiz['immediate_feedback'];
            $scope.showTimer = $scope.quiz['show_timer'];
            
            for (i = 0; i < $scope.questions.length; i++) {
                var answers = [];
                answers[0] = [];
                answers[1] = $scope.questions[i].choices['choices'].map(function(value, index) {
                    return value[0];
                });
                
                result.push({'qid' : $scope.questions[i].id, 'answers' : answers});
            }
            
            handleSortableQuestion();
            
            $timeout(function () {
                //fb_unblock(".fb_wrap");
                jQuery(".fb_wrap").css("visibility", "visible");
                jQuery(".fb_wrap").fadeIn(300);
                
                startTimer(jQuery("#fb_timer"));
            }, 200);  
            
        }).error(function(data, status, headers, config) {
            fb_error();
        });
    }
    
    function handleSortableQuestion() {
        $timeout(function () {
            jQuery( ".fb_question_content.sorting" ).sortable({
                create: function(event, ui) {
                    var choices = jQuery(this).children("li");
                    var sorting_answer = [];
                    choices.each(function(j, obj) {
                        choice_id = jQuery(obj).attr("data-id");                                                
                        sorting_answer.push(choice_id);
                    });
                    
                    result[$scope.questionNum - 1].answers[0] = sorting_answer;
                    $scope.answer[$scope.questionNum - 1] = sorting_answer;
                },
                update: function(event, ui) {
                    var choices = jQuery(this).children("li");
                    var sorting_answer = [];
                    choices.each(function(j, obj) {
                        choice_id = jQuery(obj).attr("data-id");                                                
                        sorting_answer.push(choice_id);
                    });
                    
                    result[$scope.questionNum - 1].answers[0] = sorting_answer;
                    $scope.answer[$scope.questionNum - 1] = sorting_answer;
                    
                    var question_updated = [];
                    for (i = 0; i < sorting_answer.length; i++) {
                        for (j = 0; j < $scope.questions[$scope.questionNum - 1].choices['choices'].length; j++) {
                            if (sorting_answer[i] == $scope.questions[$scope.questionNum - 1].choices['choices'][j][0])
                                question_updated.push($scope.questions[$scope.questionNum - 1].choices['choices'][j]);
                        }    
                    }
                    
                    $scope.questions[$scope.questionNum - 1].choices['choices'] = question_updated;
                    
                }
            });
        });
    }
    
    angular.element(document).ready(function () {
        init();    
    });
    
    $scope.changeQuestion = function (n) {
        if ($scope.allowSkip == 0 || $scope.questionNum == n + 1) return;
        $scope.questionNum = n + 1;
        $scope.question = $scope.quiz['questions'][n];
        $scope.quesionTitle = $scope.questionNum + '. ' + $scope.question['title'] + ' (' + $scope.question['points'] + ' points)';
        
        handleSortableQuestion();
        jQuery(".fb_quiz_right").fadeOut(10);
        $scope.answer[n] = result[n].answers[0];        
        $timeout(function() {
            jQuery(".fb_quiz_right").fadeIn(300);    
        }, 1);
    }
    
    $scope.nextQuestion = function () {
        if ( $scope.questionNum == $scope.quiz['questions'].length ) return;
        $scope.question = $scope.quiz['questions'][$scope.questionNum];
        $scope.questionNum ++;
        $scope.quesionTitle = $scope.questionNum + '. ' + $scope.question['title'] + ' (' + $scope.question['points'] + ' points)';
        
        handleSortableQuestion();                    
    }    
    
    $scope.answerClicked = function () {
        var choices = jQuery('.fb_question_content').children("li");
        var answer = [];
        choices.each(function(j, obj) {
            choice_id = jQuery(obj).attr("data-id");                                                
            if (jQuery(obj).find('input').is(":checked")) {
                answer.push(choice_id);
            }
        });
        
        result[$scope.questionNum - 1].answers[0] = answer;
        $scope.answer[$scope.questionNum - 1] = answer;        
    }
    
    $scope.answerButtonClicked = function () {
        qid = $scope.question['id'];                        
        var data = {'action' : 'fb_evaluate_answer', 'id' : qid, 'answer': $scope.answer[$scope.questionNum - 1], 'num': $scope.questionNum, 
                    'quiz_id': $scope.quiz_id, 'student_id': $scope.student_id, 'answers': result, 'answer_id': answer_id};
        jQuery.get(ajaxurl, data, function(response) {
            var res = JSON.parse(response);
            var dumb = res['result'];            
            answer_id = res['answer_id'];
             
            if (res['status'] == 1) {
                $scope.$apply(function() {
                    $scope.question['answered'] = fb_answer_html(dumb);    
                });
            }
            
            if (answersViewed.indexOf(qid) < 0) answersViewed.push(qid);
            if (answersViewed.length == $scope.questions.length) {
                $scope.allViewed = 1;
                clearInterval(timerIntervalId);
                jQuery.get(ajaxurl, {'action' : 'fb_get_quiz_result', 'answer_id' : answer_id, 'time_taken' : totalSeconds}, function(response2) {
                    var res2 = JSON.parse(response2);
                    $scope.$apply(function() {
                        $scope.quizResult = res2['result'];
                    });
                });
            }
        });
    } 
    
    $scope.submitAnswers = function () {
        var params = { 'quiz_id': $scope.quiz_id, 'student_id': $scope.student_id, 'answers': result };
        fb_add_answer(params);
    }
    
}]).filter('trustAsHtml', function ($sce) {
    return function(text) {
      return $sce.trustAsHtml(text);
    };
});

function fb_error() {
    alert("ajax error");
}