<?php

/**
 * FB_Question Class
 *
 * The FB_Question class stores question data and categories as well as handling question related functionalities. 
 *
 */    
class FB_Quiz {
    
    function __construct() {
        /*add_action( 'wp_ajax_fb_add_cat', array( $this, 'add_category' ) );
        add_action( 'wp_ajax_fb_add_question', array( $this, 'add_question' ) );
        add_action( 'wp_ajax_fb_edit_question', array( $this, 'edit_question' ) );
        */
        
    }   
    
    
    /* Display all questions page */
    public function all_questions_page() {
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/all-questions.php' );
        $html = ob_get_clean();        
        echo $html;
    }
    
    /* Display new quiz page */
    public function new_quiz_page() {        
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/add-new-quiz.php' );
        $html = ob_get_clean();        
        echo $html;
    }
}
  
?>
