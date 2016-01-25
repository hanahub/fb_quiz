<?php
    $GLOBALS['FB_TABLE'] = array(
        'questions_cat' => 'fbq_questions_cat',
        'questions' => 'fbq_questions',
        'quizzes' => 'fbq_quizzes',
        'answers' => 'fbq_answers',
        'quiz_relationships' => 'fbq_quiz_relationships',
        'connect_relationships' => 'fbq_connect_relationships'
    );
    
    $GLOBALS['FB_URL'] = array(
        'qa' => admin_url( 'admin.php?page=' . 'all_questions' ),
        'qn' => admin_url( 'admin.php?page=' . 'add_new_question' ),
        'ua' => admin_url( 'admin.php?page=' . 'all_quizzes' ),
        'un' => admin_url( 'admin.php?page=' . 'add_new_quiz' ),
        'reporting' => admin_url( 'admin.php?page=reporting' ),
        'quizzes' => home_url( '/quizzes/' ),
        'my_quizzes' => home_url( '/my-quizzes/' ),
        'results' => home_url( '/results/' )
    );
    
    function fb_date($d) {
        $pd = strtotime($d);
        return date('m-d-Y H:i', $pd);
    }
 

?>
