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
        add_action( 'wp_ajax_fb_edit_quiz', array( $this, 'edit_quiz' ) );
        add_action( 'wp_ajax_fb_get_quiz', array( $this, 'get_quiz' ) );
        add_action( 'wp_ajax_fb_remove_relationship', array( $this, 'remove_relationship' ) );
        
        add_action( 'wp_ajax_fb_get_random_questions_by_category', array( $this, 'get_random_questions_by_category' ) );
        
    }
    
    /* Display all questions page */
    public function all_quizzes_page() {
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/all-quizzes.php' );
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
        global $wpdb, $FB_TABLE, $FB_URL;
        $id = $_REQUEST['id'];
        $num_questions = $_REQUEST['num_questions'];
        
        $dumb = $wpdb->get_results("SELECT id, title FROM " . $FB_TABLE['questions'] . " WHERE cats like '%" . '"' . $id . '"' . "%' ORDER BY RAND() limit " . $num_questions );        
        echo json_encode(array(status => 1, rows => $dumb));
        die();
    }
    
    
    /* Add new quiz info */
    function add_quiz() {
        global $wpdb, $FB_TABLE, $FB_URL;
        $p = $_REQUEST['params'];
        
        $created_at = $updated_at = date('Y-m-d H:i:s', time());
        
        $wpdb->insert( $FB_TABLE['quizzes'],
                    array(
                            'title'                 => $p['title'],
                            'description'           => $p['description'],
                            'author'                => $p['author'],
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
                    array('%s', '%s', '%d', '%s', '%d', '%s', '%d', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s')
                );               
        echo json_encode(array(status => 1, id => $wpdb->insert_id, redirect_url => $FB_URL['un'] . '&id=' . $wpdb->insert_id . '&action=edit'));
        die();
    }
    
    /* Edit existing quiz info */
    function edit_quiz() {
        global $wpdb, $FB_TABLE, $FB_URL;
        
        $id = $_REQUEST['id'];
        $p = $_REQUEST['params'];
        
        $updated_at = date('Y-m-d H:i:s', time());
        
        $result = $wpdb->update( $FB_TABLE['quizzes'],
                    array(
                            'title'                 => $p['title'],
                            'description'           => $p['description'],
                            'author'                => $p['author'],
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
                            'updated_at'            => $updated_at
                        ),
                    array('id' => $id),
                    array('%s', '%s', '%d', '%s', '%d', '%s', '%d', '%s', '%d', '%d', '%d', '%d', '%s', '%s'),
                    array('%d')
                );        
        
        echo json_encode(array(status => 1, result => $result, redirect_url => $FB_URL['un'] . '&id=' . $id . '&action=edit'));
        die();
    }
    
    /* Return quiz info as JSON */
    public function get_quiz() {
        global $wpdb, $FB_TABLE;
        $id = $_REQUEST['qid'];
        
        $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " WHERE id=" . $id );
        $result = $dumb[0];
        
        $q_questions = unserialize($result->questions);
        $questions = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['questions'] . " WHERE id IN (" . implode(", ", $q_questions) . ") ORDER BY FIELD(id, " . implode(", ", $q_questions) . ")" );        
        
        foreach ($questions as $q) {
            $q->choices = unserialize($q->choices);
            unset($q->choices['correct']);
            $q->cats = unserialize($q->cats);
        }
        $result->questions = $questions;
        
        echo json_encode(array(status => 1, result => $result));
        die();
    }
    
    /* Ajax call to remove a question in a quiz */
    public function remove_relationship() {
        global $wpdb, $FB_TABLE;
        
        $id = $_REQUEST['id'];
        $p = $_REQUEST['params'];
        
        $result = $wpdb->delete($FB_TABLE['quiz_relationships'], array('quiz_id' => $id, 'question_id' => $p['question_id']), array('%d', '%d'));
        
        echo json_encode(array(status => 1, result => $result));
        die();
    }        
}


  
?>
