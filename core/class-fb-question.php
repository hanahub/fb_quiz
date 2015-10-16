<?php

/**
 * FB_Question Class
 *
 * The FB_Question class stores question data and categories as well as handling question related functionalities. 
 *
 */    
class FB_Question {
    
    function __construct() {
        add_action( 'wp_ajax_fb_add_cat', array( $this, 'add_category' ) );
        add_action( 'wp_ajax_fb_add_question', array( $this, 'add_question' ) );
    }
    
    /* Add question category */
    function add_category() {
        global $wpdb, $FB_TABLES;
        $cat = $_REQUEST['cat'];
        
        $wpdb->insert( $FB_TABLES['questions_cat'], array('name' => $cat), array('%s'));
        echo json_encode(array(status => 1, id => $wpdb->insert_id));
        die();
    }
    
    /* Add new question info */
    function add_question() {
        global $wpdb, $FB_TABLES;
        $p = $_REQUEST['params'];
        
        $created_at = $updated_at = date('Y-m-d H:i:s', time());
        
        $wpdb->insert( $FB_TABLES['questions'],
                    array(
                            'author'                => $p['author'],
                            'title'                 => $p['title'],
                            'type'                  => $p['type'],
                            'points'                => $p['points'],
                            'cats'                  => serialize($p['cats']),
                            'correct_explanation'   => $p['correct_explanation'],
                            'status'                => $p['status'],
                            'choices'               => serialize($p['choices']),
                            'created_at'            => $created_at,
                            'updated_at'            => $updated_at
                        ),
                    array('%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
                );
        echo json_encode(array(status => 1, id => $wpdb->insert_id));
        die();
        
        print_r($data);
        die();
    }
    
    /* Display all questions page */
    public function all_questions_page() {
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/all-questions.php' );
        $html = ob_get_clean();        
        echo $html;
    }
    
    /* Display new question page */
    public function new_question_page() {        
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/add-new-question.php' );
        $html = ob_get_clean();        
        echo $html;
    }
}
  
?>
