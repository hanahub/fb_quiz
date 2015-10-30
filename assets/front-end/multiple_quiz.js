angular.module('MultipleQuizModule', [])
.controller('MultipleQuizController', ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
    
    var config = { headers: {      
            'Accept': 'application/json;odata=verbose'
        }
    };
    
    var question_num = 0;    
    
    function init() {      
        var url = ajaxurl + '?action=fb_get_quiz&qid=' + $scope.quiz_id;
        
        fb_block(".fb_wrap");
        $http.get(url, config).success(function(response) {
            $scope.activeQuiz = response['result'];            
            
            $scope.activeQuestionId = $scope.activeQuiz['questions'][question_num]['id'];
            url = ajaxurl + '?action=fb_get_question&qid=' + $scope.activeQuestionId;
            $http.get(url, config).success(function(response) {
                
                $scope.activeQuestion = response['result'];
                
                $scope.title = $scope.activeQuiz['title'] + ' (Passing ' + $scope.activeQuiz['passing_percentage'] + '%)';
                $scope.description = $scope.activeQuiz['description'];
                $scope.questions = $scope.activeQuiz['questions'];
                $scope.layout = $scope.activeQuiz['layout'];                                
                
                $timeout(function () { 
                    jQuery( ".fb_question_content.sorting" ).sortable();
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
    
    
}]).filter('trustAsHtml', function ($sce) {
    return function(text) {
      return $sce.trustAsHtml(text);
    };
});

function fb_error() {
    alert("ajax error");
}