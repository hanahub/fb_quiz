<?php

/**
 * FB_Quiz Class
 *
 * Handles all quiz related functionalities
 *
 */    
class FB_Quiz {
    
    /**
    * Adds various ajax handlers for quiz actions    
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */
    function __construct() {                                                    
        add_action( 'wp_ajax_fb_add_quiz', array( $this, 'add_quiz' ) );
        add_action( 'wp_ajax_fb_edit_quiz', array( $this, 'edit_quiz' ) );
        add_action( 'wp_ajax_fb_get_quiz', array( $this, 'get_quiz' ) );
        add_action( 'wp_ajax_fb_remove_relationship', array( $this, 'remove_relationship' ) );
        add_action( 'wp_ajax_fb_remove_connection', array( $this, 'remove_connection' ) );
        
        add_action( 'wp_ajax_fb_get_random_questions_by_category', array( $this, 'get_random_questions_by_category' ) );
        
    }
    
    /**
    * Displays all quizzes page
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    public function all_quizzes_page() {        
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/all-quizzes.php' );
        $html = ob_get_clean();        
        echo $html;
    }
    
    /**
    * Displays new quiz page
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    public function new_quiz_page() {        
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/add-new-quiz.php' );
        $html = ob_get_clean();        
        echo $html;
    }
    
    /**
    * Returns 3 random questions in a specific category
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    function get_random_questions_by_category() {
        global $wpdb, $FB_TABLE, $FB_URL;
        $id = $_REQUEST['id'];
        $num_questions = $_REQUEST['num_questions'];
        
        $dumb = $wpdb->get_results("SELECT id, title FROM " . $FB_TABLE['questions'] . " WHERE cats like '%" . '"' . $id . '"' . "%' ORDER BY RAND() limit " . $num_questions );        
        echo json_encode(array(status => 1, rows => $dumb));
        die();
    }
    
    /**
    * Stores new quiz info into quiz table
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
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
                    array('%s', '%s', '%d', '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%d', '%s', '%s', '%s')
                );
        
        $quiz_id = $wpdb->insert_id;
        if (isset($p['connected_to'])) {
            foreach ($p['connected_to'] as $pid) {
                $wpdb->insert( $FB_TABLE['connect_relationships'], 
                        array(
                                'quiz_id'       => $quiz_id,
                                'post_id'       => $pid,
                                'created_at'    => $created_at
                            ),
                        array('%d', '%d', '%s')
                );
            }
        }
        
        if (isset($p['questions'])) {
            foreach ($p['questions'] as $qid) {
                $wpdb->insert( $FB_TABLE['quiz_relationships'], 
                        array(
                                'quiz_id'       => $quiz_id,
                                'question_id'       => $qid,
                                'created_at'    => $created_at
                            ),
                        array('%d', '%d', '%s')
                );
            }
        }
        
        echo json_encode(array(status => 1, id => $quiz_id, redirect_url => $FB_URL['un'] . '&id=' . $quiz_id . '&action=edit'));
        die();
    }
    
    /**
    * Updates existing quiz info
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
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
                    array('%s', '%s', '%d', '%s', '%d', '%d', '%s', '%d', '%d', '%d', '%d', '%s', '%s'),
                    array('%d')
                );        
        
        if (count($p['connected_to']) > 0) {
            foreach ($p['connected_to'] as $pid) {            
                $res = $wpdb->query("SELECT id FROM {$FB_TABLE['connect_relationships']} WHERE quiz_id={$id} AND post_id={$pid}");
                if ($res == 0) {
                    $wpdb->insert( $FB_TABLE['connect_relationships'], 
                            array(            
                                    'quiz_id'       => $id,
                                    'post_id'       => $pid,
                                    'created_at'    => $updated_at
                                ),
                            array('%d', '%d', '%s')
                    );
                }
            }
        }
        if (count($p['questions']) > 0) {
            foreach ($p['questions'] as $qid) {
                $res = $wpdb->query("SELECT id FROM {$FB_TABLE['quiz_relationships']} WHERE quiz_id={$id} AND question_id={$qid}");
                if ($res == 0) {
                    $wpdb->insert( $FB_TABLE['quiz_relationships'], 
                            array(
                                    'quiz_id'       => $id,
                                    'question_id'   => $qid,
                                    'created_at'    => $updated_at
                                ),
                            array('%d', '%d', '%s')
                    );
                }
            }
        }
        
        echo json_encode(array(status => 1, result => $result, redirect_url => $FB_URL['un'] . '&id=' . $id . '&action=edit'));
        die();
    }
    
    /**
    * Returns quiz info as JSON
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */
    public function get_quiz() {
        global $wpdb, $FB_TABLE;
        $id = $_REQUEST['qid'];
        
        $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " WHERE id=" . $id );
        $result = $dumb[0];        
        $q_random_questions = $result->random_questions;                                                                                                   
        $q_random_choices = $result->random_choices;        
        
        $q_questions = unserialize($result->questions);                                                                                                    
        if ($q_random_questions == 1) shuffle($q_questions);
        
        $questions = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['questions'] . " WHERE id IN (" . implode(", ", $q_questions) . ") ORDER BY FIELD(id, " . implode(", ", $q_questions) . ")" );        
        
        $i = 0;
        for ($i = 0; $i < count($questions); $i ++) {        
            $questions[$i]->choices = unserialize($questions[$i]->choices);            
            $questions[$i]->title = stripslashes($questions[$i]->title);
            unset($questions[$i]->choices['correct']);
            
            if ($questions[$i]->type == 'sorting') {                                
                $q_choices = $questions[$i]->choices;                                   
                while ($q_choices['choices'] == $questions[$i]->choices['choices']) {
                    shuffle($questions[$i]->choices['choices']);                        
                }
                
            } else {
                if ($q_random_choices == 1) shuffle($questions[$i]->choices['choices']);
            }
            
            $questions[$i]->cats = unserialize($questions[$i]->cats);
            
        }
        $result->questions = $questions;
        
        echo json_encode(array(status => 1, result => $result));
        die();
    }
    
    /**
    * Deletes a quiz from quiz table
    * @param int quiz ID
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    public function delete_quiz($id) {
        global $wpdb, $FB_TABLE;
        
        $wpdb->delete("{$FB_TABLE['quiz_relationships']}", array('quiz_id' => $id));        
        $wpdb->delete("{$FB_TABLE['quizzes']}", array('id' => $id));
    }
    
    /**
    * Ajax handler to remove a question in a quiz     
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    public function remove_relationship() {
        global $wpdb, $FB_TABLE;
        
        $id = $_REQUEST['id'];
        $p = $_REQUEST['params'];
        
        $result = $wpdb->delete($FB_TABLE['quiz_relationships'], array('quiz_id' => $id, 'question_id' => $p['question_id']), array('%d', '%d'));
        
        echo json_encode(array(status => 1, result => $result));
        die();
    } 
    
    /**
    * Returns all post IDs connected to a quiz
    * @param int quiz ID
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */
    public function get_connections($quiz_id) {
        global $wpdb, $FB_TABLE;
        $sql = "SELECT post_id FROM {$FB_TABLE['connect_relationships']} WHERE quiz_id={$quiz_id} order by id desc";            
        $q_connected_to = $wpdb->get_results($sql);
        
        return $q_connected_to;
    }
    
    /**
    * Ajax handler to remove a connection in a quiz     
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    public function remove_connection() {
        global $wpdb, $FB_TABLE;
        
        $id = $_REQUEST['id'];
        $p = $_REQUEST['params'];
        
        $result = $wpdb->delete($FB_TABLE['connect_relationships'], array('quiz_id' => $id, 'post_id' => $p['post_id']), array('%d', '%d'));
        
        echo json_encode(array(status => 1, result => $result));
        die();
    }
}


  
?>
