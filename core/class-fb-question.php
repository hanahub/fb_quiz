<?php

/**
 * FB_Question Class
 *
 * The FB_Question class stores question data and categories as well as handling question related functionalities. 
 *
 */    
class FB_Question {
    
    /**
    * Adds various ajax handlers for question actions    
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */
    function __construct() {
        add_action( 'wp_ajax_fb_add_cat', array( $this, 'add_category' ) );
        add_action( 'wp_ajax_fb_add_question', array( $this, 'add_question' ) );
        add_action( 'wp_ajax_fb_edit_question', array( $this, 'edit_question' ) );        
        add_action( 'wp_ajax_fb_get_question', array( $this, 'get_question' ) );
    }
    
    /**
    * Adds question category
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    function add_category() {
        global $wpdb, $FB_TABLE;
        $cat = $_REQUEST['cat'];
        
        $wpdb->insert( $FB_TABLE['questions_cat'], array('name' => $cat), array('%s'));
        echo json_encode(array(status => 1, id => $wpdb->insert_id));
        die();
    }
    
    /**
    * Stores new question info into question table
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    function add_question() {
        global $wpdb, $FB_TABLE, $FB_URL;
        $p = $_REQUEST['params'];
        
        $created_at = $updated_at = date('Y-m-d H:i:s', time());
        
        $wpdb->insert( $FB_TABLE['questions'],
                    array(
                            'author'                => $p['author'],
                            'title'                 => stripslashes($p['title']),
                            'type'                  => $p['type'],
                            'points'                => $p['points'],
                            'cats'                  => serialize($p['cats']),
                            'correct_explanation'   => $p['correct_explanation'],
                            'status'                => $p['status'],
                            'choices'               => serialize($p['choices']),
                            'number_of_choices'     => $p['number_of_choices'],
                            'created_at'            => $created_at,
                            'updated_at'            => $updated_at
                            
                        ),
                    array('%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s')
                );               
        echo json_encode(array(status => 1, id => $wpdb->insert_id, redirect_url => $FB_URL['qn'] . '&id=' . $wpdb->insert_id . '&action=edit'));
        die();
    }
    
    /**
    * Updates existing question info
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
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
                            'number_of_choices'     => $p['number_of_choices'],
                            'updated_at'            => $updated_at
                        ),
                    array('id' => $id),
                    array('%d', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s'),
                    array('%d')
                );               
        echo json_encode(array(status => 1, result => $result, redirect_url => $FB_URL['qn'] . '&id=' . $id . '&action=edit'));
        die();
    }
    
    /**
    * Deletes a question info from question table
    * @param int question ID
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    public function delete_question($id) {
        global $wpdb, $FB_TABLE;
        
        $dumb = $wpdb->get_results("SELECT id, questions FROM " . $FB_TABLE['quizzes'] . " WHERE questions LIKE '%" . '"' . $id . '"' . "%'", ARRAY_A );        
        foreach ($dumb as $row) {
            $questions = unserialize($row['questions']);
            if (($key = array_search($id, $questions)) !== false)
                unset($questions[$key]);
            
            $result = $wpdb->update( $FB_TABLE['quizzes'],
                    array(                            
                            'questions' => serialize($questions)
                        ),
                    array('id' => $row['id']),
                    array('%s'),
                    array('%d')
                );
        }
        
        $wpdb->delete("{$FB_TABLE['questions']}", array('id' => $id));
    }
    
    /**
    * Returns question info as JSON
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    public function get_question() {
        global $wpdb, $FB_TABLE;
        $id = $_REQUEST['qid'];
        
        $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['questions'] . " WHERE id=" . $id );
        $result = $dumb[0];
        $result->cats = unserialize($result->cats);
        $result->choices = unserialize($result->choices);
        unset($result->choices['correct']);
        
        echo json_encode(array(status => 1, result => $result));
        die();
    }
    
    /**
    * Displays all questions page
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    public function all_questions_page() {
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/all-questions.php' );
        $html = ob_get_clean();        
        echo $html;
    }
    
    /**
    * Displays new questions page
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    public function new_question_page() {        
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/add-new-question.php' );
        $html = ob_get_clean();        
        echo $html;
    }
    
    /**
    * Prints quizzes connected to a question
    * @param int question ID
    * @author Valentin Marinov <dev.valentin2013@gmail.com>
    */    
    public function print_quizzes_connected($id) {        
        global $wpdb, $FB_TABLE;
        $dumb = $wpdb->get_results("SELECT title FROM " . $FB_TABLE['quizzes'] . " WHERE questions LIKE '%" . '"' . $id . '"' . "%'", ARRAY_A );
        echo implode(", ", array_column($dumb, 'title'));
    }
}
  
?>
