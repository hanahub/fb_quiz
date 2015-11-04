<?php
    $GLOBALS['FB_TABLE'] = array(
        'questions_cat' => 'fb_questions_cat',
        'questions' => 'fb_questions',
        'quizzes' => 'fb_quizzes',
        'answers' => 'fb_answers',
        'quiz_relationships' => 'fb_quiz_relationships'
    );
    
    $GLOBALS['FB_URL'] = array(
        'qa' => admin_url( 'admin.php?page=' . 'all_questions' ),
        'qn' => admin_url( 'admin.php?page=' . 'add_new_question' ),
        'ua' => admin_url( 'admin.php?page=' . 'all_quizzes' ),
        'un' => admin_url( 'admin.php?page=' . 'add_new_quiz' ),
        'quizzes' => home_url( '/quizzes/' ),
        'my_quizzes' => home_url( '/my-quizzes/' ),
        'results' => home_url( '/results/' )
    );
 

?>
