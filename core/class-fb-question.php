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
        add_action( 'wp_ajax_fb_edit_question', array( $this, 'edit_question' ) );
        
        
    }
    
    /* Add question category */
    function add_category() {
        global $wpdb, $FB_TABLE;
        $cat = $_REQUEST['cat'];
        
        $wpdb->insert( $FB_TABLE['questions_cat'], array('name' => $cat), array('%s'));
        echo json_encode(array(status => 1, id => $wpdb->insert_id));
        die();
    }
    
    /* Add new question info */
    function add_question() {
        global $wpdb, $FB_TABLE, $FB_URL;
        $p = $_REQUEST['params'];
        
        $created_at = $updated_at = date('Y-m-d H:i:s', time());
        
        $wpdb->insert( $FB_TABLE['questions'],
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
        echo json_encode(array(status => 1, id => $wpdb->insert_id, redirect_url => $FB_URL['qn'] . '&id=' . $wpdb->insert_id . '&action=edit'));
        die();
    }
    
    /* Edit existing question info */
    function edit_question() {
        global $wpdb, $FB_TABLE, $FB_URL;
        
        $id = $_REQUEST['id'];
        $p = $_REQUEST['params'];
        
        $updated_at = date('Y-m-d H:i:s', time());
        
        $result = $wpdb->update( $FB_TABLE['questions'],
                    array(
                            'author'                => $p['author'],
                            'title'                 => $p['title'],
                            'type'                  => $p['type'],
                            'points'                => $p['points'],
                            'cats'                  => serialize($p['cats']),
                            'correct_explanation'   => $p['correct_explanation'],
                            'status'                => $p['status'],
                            'choices'               => serialize($p['choices']),                            
                            'updated_at'            => $updated_at
                        ),
                    array('id' => $id),
                    array('%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s'),
                    array('%d')
                );               
        echo json_encode(array(status => 1, result => $result, redirect_url => $FB_URL['qn'] . '&id=' . $id . '&action=edit'));
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
