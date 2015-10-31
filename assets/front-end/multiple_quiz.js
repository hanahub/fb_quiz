angular.module('MultipleQuizModule', [])
.controller('MultipleQuizController', ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
    
    var config = { headers: {      
            'Accept': 'application/json;odata=verbose'
        }
    };
    
    var result = [];
    
    $scope.questionNum = 1;    
    
    function init() {      
        var url = ajaxurl + '?action=fb_get_quiz&qid=' + $scope.quiz_id;
        
        fb_block(".fb_wrap");
        $http.get(url, config).success(function(response) {
            $scope.quiz = response['result'];            
            
            $scope.questionId = $scope.quiz['questions'][$scope.questionNum - 1]['id'];
            url = ajaxurl + '?action=fb_get_question&qid=' + $scope.questionId;
            $http.get(url, config).success(function(response) {
                $scope.quizTitle = $scope.quiz['title'] + ' (Passing ' + $scope.quiz['passing_percentage'] + '%)';        
                $scope.question = response['result'];
                $scope.quesionTitle = $scope.questionNum + '. ' + $scope.question['title'] + ' (' + $scope.question['points'] + ' points)';
                $scope.questions = $scope.quiz['questions'];
                $scope.layout = $scope.quiz['layout'];
                
                for (i = 0; i < $scope.questions.length; i++) {
                    var answers = [];
                    answers[0] = [];
                    answers[1] = $scope.questions[i].choices['choices'].map(function(value, index) {
                        return value[0];
                    });
                    
                    result.push({'qid' : $scope.questions[i].id, 'answers' : answers});
                }
                
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
                        }
                    });                    
                });                
                
                fb_unblock(".fb_wrap");
            }).error(function(data, status, headers, config) {
                fb_error();
            });    
            
        }).error(function(data, status, headers, config) {
            fb_error();
        });
        
        
    }
    
    angular.element(document).ready(function () {
        init();    
    });
    
    $scope.changeQuestion = function (n) {
        $scope.questionNum = n + 1;
        $scope.question = $scope.quiz['questions'][n];
        $scope.quesionTitle = $scope.questionNum + '. ' + $scope.question['title'] + ' (' + $scope.question['points'] + ' points)';
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