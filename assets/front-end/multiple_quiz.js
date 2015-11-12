angular.module('MultipleQuizModule', [])
.controller('MultipleQuizController', ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
    
    var config = { headers: {      
            'Accept': 'application/json;odata=verbose'
        }
    };
    
    
    var result = [];
    
    $scope.questionNum = 1;    
    $scope.allowSkip = 0;
    
    function init() {      
        var url = ajaxurl + '?action=fb_get_quiz&qid=' + $scope.quiz_id;        
        
        fb_block(".fb_wrap");
        $http.get(url, config).success(function(response) {
            $scope.quiz = response['result'];            
            
            $scope.questions = $scope.quiz['questions'];
            $scope.question = $scope.questions[$scope.questionNum - 1];
            $scope.quizTitle = $scope.quiz['title'] + ' (Passing ' + $scope.quiz['passing_percentage'] + '%)';        
            $scope.quesionTitle = $scope.questionNum + '. ' + $scope.question['title'] + ' (' + $scope.question['points'] + ' points)';
            $scope.layout = $scope.quiz['layout'];
            $scope.allowSkip = $scope.quiz['allow_skipping'];                
            
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
                fb_unblock(".fb_wrap");
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
                },
                update: function(event, ui) {
                    var choices = jQuery(this).children("li");
                    var sorting_answer = [];
                    choices.each(function(j, obj) {
                        choice_id = jQuery(obj).attr("data-id");                                                
                        sorting_answer.push(choice_id);
                    });
                    
                    result[$scope.questionNum - 1].answers[0] = sorting_answer;
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
        if ($scope.allowSkip == 0) return;
        $scope.questionNum = n + 1;
        $scope.question = $scope.quiz['questions'][n];
        $scope.quesionTitle = $scope.questionNum + '. ' + $scope.question['title'] + ' (' + $scope.question['points'] + ' points)';
        
        handleSortableQuestion();        
        $scope.answer = result[n].answers[0];
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
    }
    
    $scope.initCheck = function(id) {
        if (typeof $scope.answer == 'undefined') return '';
        for (i = 0; i < $scope.answer.length; i++) {
            if ($scope.answer[i] == id) return "checked";
        }
        return '';
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