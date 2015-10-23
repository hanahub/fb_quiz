<?php
    $GLOBALS['FB_TABLES'] = array(
        'questions_cat' => 'fb_questions_cat',
        'questions' => 'fb_questions',
        'quizzes' => 'fb_quizzes'
    );
    
    $GLOBALS['QA_URL'] = admin_url( 'admin.php?page=' . 'all_questions' );
    $GLOBALS['QN_URL'] = admin_url( 'admin.php?page=' . 'add_new_question' );
    $GLOBALS['UA_URL'] = admin_url( 'admin.php?page=' . 'all_quizzes' );
    $GLOBALS['UN_URL'] = admin_url( 'admin.php?page=' . 'add_new_quiz' );

?>
