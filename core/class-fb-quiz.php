<?php

/**
 * FB_Question Class
 *
 * The FB_Question class stores question data and categories as well as handling question related functionalities. 
 *
 */    
class FB_Quiz {
    
    function __construct() {                                                    
        add_action( 'wp_ajax_fb_add_quiz', array( $this, 'add_quiz' ) );
        add_action( 'wp_ajax_fb_get_random_questions_by_category', array( $this, 'get_random_questions_by_category' ) );
        
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
    
    /* Returns 3 random questions in a specific category */
    function get_random_questions_by_category() {
        global $wpdb, $FB_TABLES, $QN_URL;
        $id = $_REQUEST['id'];
        $num_questions = $_REQUEST['num_questions'];
        
        $dumb = $wpdb->get_results("SELECT id, title FROM " . $FB_TABLES['questions'] . " WHERE cats like '%" . '"' . $id . '"' . "%' ORDER BY RAND() limit " . $num_questions );        
        echo json_encode(array(status => 1, rows => $dumb));
        die();
    }
    
    
    /* Add new quiz info */
    function add_quiz() {
        global $wpdb, $FB_TABLES, $UN_URL;
        $p = $_REQUEST['params'];
        
        $created_at = $updated_at = date('Y-m-d H:i:s', time());
        
        $wpdb->insert( $FB_TABLES['quizzes'],
                    array(
                            'title'                 => $p['title'],
                            'description'           => $p['description'],
                            'questions'             => serialize($p['questions']),
                            'num_of_questions'      => $p['num_of_questions'],
                            'connected_to'          => serialize($p['connected_to']),
                            'passing_percentage'    => $p['passing_percentage'],
                            'layout'                => $p['layout'],
                            'allow_skipping'        => $p['allow_skipping'],
                            'immediate_feedback'    => $p['immediate_feedback'],
                            'random_questions'      => $p['random_questions'],
                            'random_choices'        => $p['random_choices'],                            
                            'status'                => $p['status'],
                            'created_at'            => $created_at,
                            'updated_at'            => $updated_at
                        ),
                    array('%s', '%s', '%s', '%d', '%s', '%d', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s')
                );               
        echo json_encode(array(status => 1, id => $wpdb->insert_id, redirect_url => $UN_URL . '&id=' . $wpdb->insert_id . '&action=edit'));
        die();
    }
}
  
?>
