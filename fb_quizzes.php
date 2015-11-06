<?php

/*
   Plugin Name: FB Quizzes
   Description: FB Quizzes
   Author: Valentin Marinov
*/

define( 'FBQUIZ_PATH', plugin_dir_path( __FILE__ ) );
define( 'FBQUIZ_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'FBQUIZ_TEMPLATES_PATH', FBQUIZ_PATH . 'templates' );



class FB_Quizzes {
 
    public $fb_question = null;
    public $fb_quiz = null;
    
    public function __construct() { 
        register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
        register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
        
        add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_scripts' ) ); 
        
        add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) ); 
        
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        
        add_filter( 'body_class', array( $this, "body_class" ) );
        add_filter( 'query_vars', array( $this, 'query_vars' ) );
        add_filter( 'the_content', array( $this, "the_content" ) );
        
        add_action( 'wp_ajax_fb_add_answer', array( $this, 'add_answer' ) );
        add_action( 'wp_ajax_nopriv_fb_add_answer', array( $this, 'add_answer' ) );        
        
        if (!is_admin()) { 
            add_action( 'wp_head', array( $this, 'ajaxurl' ) );
        }
        
        add_filter( 'the_title', array( $this, "the_title" ) );
    }
    
    function register_styles() {        
        wp_register_style( 'fb-quizzes-style', FBQUIZ_URL . 'assets/front-end/style.css' );
        wp_enqueue_style( 'fb-quizzes-style' );
        
        wp_register_style( 'fb-global-style', FBQUIZ_URL . 'assets/global.css' );
        wp_enqueue_style( 'fb-global-style' );     
    }     
    
    function register_scripts() {
        wp_register_script( 'fb-global-script', FBQUIZ_URL . 'assets/global.js');
        wp_enqueue_script( 'fb-global-script' );
        
        wp_register_script( 'fb-angular-script', FBQUIZ_URL . 'assets/angular/angular.min.js');
        wp_enqueue_script( 'fb-angular-script' );
        
        wp_register_script( 'fb-multiple-quiz-script', FBQUIZ_URL . 'assets/front-end/multiple_quiz.js');
        wp_enqueue_script( 'fb-multiple-quiz-script' );
        
        wp_register_script( 'fb-blockui-script', FBQUIZ_URL . 'assets/jquery-blockui/jquery.blockUI.min.js', array('jquery') );
        wp_enqueue_script( 'fb-blockui-script' );
        
        wp_register_script( 'fb-quizzes-script', FBQUIZ_URL . 'assets/front-end/script.js', array('jquery', 'jquery-ui-sortable') );
        wp_enqueue_script( 'fb-quizzes-script' );
        
    }    
    
    function register_plugin_styles() {         
        wp_register_style( 'fb-datatables-style', FBQUIZ_URL . 'assets/jquery-datatables/jquery.dataTables.min.css' );
        wp_enqueue_style( 'fb-datatables-style' );
        
        wp_register_style( 'fb-quizzes-style', FBQUIZ_URL . 'assets/admin/admin-style.css' );
        wp_enqueue_style( 'fb-quizzes-style' );    
        
        wp_register_style( 'fb-global-style', FBQUIZ_URL . 'assets/global.css' );
        wp_enqueue_style( 'fb-global-style' );      
    }     
    
    function register_plugin_scripts() {                    
        wp_register_script( 'fb-global-script', FBQUIZ_URL . 'assets/global.js');
        wp_enqueue_script( 'fb-global-script' );
        
        wp_register_script( 'fb-blockui-script', FBQUIZ_URL . 'assets/jquery-blockui/jquery.blockUI.min.js', array('jquery') );
        wp_enqueue_script( 'fb-blockui-script' );
        
        wp_register_script( 'fb-datatables-script', FBQUIZ_URL . 'assets/jquery-datatables/jquery.dataTables.min.js', array('jquery') );
        wp_enqueue_script( 'fb-datatables-script' );
        
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        
        wp_register_script( 'fb-quizzes-script', FBQUIZ_URL . 'assets/admin/admin-script.js', array('jquery', 'jquery-ui-sortable') );
        wp_enqueue_script( 'fb-quizzes-script' );
    }
    
    function plugin_activation() {
        
        global $user_ID;
        
        $my_quizzes['post_type']    = 'page';
        $my_quizzes['post_name']    = 'my-quizzes';
        $my_quizzes['post_content'] = '';
        $my_quizzes['post_parent']  = 0;
        $my_quizzes['post_author']  = $user_ID;
        $my_quizzes['post_status']  = 'publish';
        $my_quizzes['post_title']   = 'My Quizzes';

        $results['post_type']    = 'page';
        $results['post_name']    = 'results';
        $results['post_content'] = '';
        $results['post_parent']  = 0;
        $results['post_author']  = $user_ID;
        $results['post_status']  = 'publish';
        $results['post_title']   = 'Results';        
        
        $quizzes['post_type']    = 'page';
        $quizzes['post_name']    = 'quizzes';
        $quizzes['post_content'] = '';
        $quizzes['post_parent']  = 0;
        $quizzes['post_author']  = $user_ID;
        $quizzes['post_status']  = 'publish';
        $quizzes['post_title']   = 'Quizzes';        
        
        if (!get_page_by_path("my-quizzes")) $pageid = wp_insert_post($my_quizzes);
        if (!get_page_by_path("results")) $pageid = wp_insert_post($results);
        if (!get_page_by_path("quizzes")) $pageid = wp_insert_post($quizzes);        
    }
    
    function plugin_deactivation() {
        
    }
    
    function query_vars( $query_vars ) {                
        $query_vars[] = 'fb_id';    
        return $query_vars;
    }    
    
    function body_class( $classes ) {        
        if ($this->is_quiz())
            $classes[] = 'single-quiz';
        if ($this->is_result())
            $classes[] = 'single-result';
        
        return $classes;
    }
    
    function ajaxurl() {
    ?>
        <script type="text/javascript">
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            var site_url = '<?php echo site_url(); ?>';
        </script>
    <?php
    }
    
    function init() {        
    
        require_once( FBQUIZ_PATH . 'core/globals.php' );
        
        if (is_admin()) {
            require_once( FBQUIZ_PATH . 'core/class-fb-question.php' );
            require_once( FBQUIZ_PATH . 'core/class-fb-quiz.php' );
            $this->fb_question = new FB_Question();            
            $this->fb_quiz = new FB_Quiz();            
        }
        
        add_rewrite_rule(
            'quizzes/([0-9]+)/?$',
            'index.php?pagename=quizzes&fb_id=$matches[1]',
            'top' );
        
        add_rewrite_rule(
            'results/([0-9]+)/?$',
            'index.php?pagename=results&fb_id=$matches[1]',
            'top' );
            
        add_rewrite_rule(
            'my-quizzes/([0-9]+)/?$',
            'index.php?pagename=my-quizzes&fb_id=$matches[1]',
            'top' );            
    }    
    
    function admin_menu() {   
        
        add_menu_page( 'FB Quizzes', 'FB Quizzes', 'manage_options', 'quizzes_manager', 'my_custom_menu_page', 'dashicons-admin-post', 3 );     
        
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'All Quizzes', 'manage_options', 'all_quizzes', array( $this, 'render_all_quizzes_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'Add New Quiz', 'manage_options', 'add_new_quiz', array( $this, 'render_new_quiz_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'All Questions', 'manage_options', 'all_questions', array( $this, 'render_all_questions_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'Add New Question', 'manage_options', 'add_new_question', array( $this, 'render_new_question_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'Reporting', 'manage_options', 'reporting', array( $this, 'render_reporting_page' ) );
        
        remove_submenu_page('quizzes_manager', 'quizzes_manager');
    }
    
    public function is_quiz() {
        global $wp_query;                         
        $name = $wp_query->get('name');
        $quiz_id = $wp_query->get('fb_id');
        
        if ($name == "quizzes") {
            if (is_numeric($quiz_id)) {
                return $quiz_id;
            }
        }        
        return 0;
    }    
    
    public function is_result() {
        global $wp_query;                         
        $name = $wp_query->get('name');
        $result_id = $wp_query->get('fb_id');
        
        if ($name == "results") {
            if (is_numeric($result_id)) {
                return $result_id;
            }
        }        
        return 0;
    }    
    
    function the_content($content) {
        global $wp_query;
        if ( is_page( 'quizzes' ) ) {        
            $quiz_id = $this->is_quiz();
            if ($quiz_id > 0) {  
                return $this->quizzes_page($quiz_id);
            }
        } else if ( is_page('results') ) {
            $result_id = $wp_query->get("fb_id");
            return $this->result_page($result_id);
        } else if ( is_page('my-quizzes') ) {
            $quiz_id = $wp_query->get("fb_id");
            return $this->my_quizzes_page($quiz_id);
        }
                 
        return $content;
    }
    
    function the_title($title) {        
        if (in_the_loop() && is_page(array("quizzes", "results", "my-quizzes"))) {
            return "";
        } else {
            return $title;
        }   
    }
    
    /* Display Quiz page on front-end */
    public function quizzes_page($quiz_id) {
        global $wpdb, $FB_TABLE;
        ob_start();
        $dumb = $wpdb->get_results("SELECT * FROM " . $FB_TABLE['quizzes'] . " WHERE id=" . $quiz_id );         
        include( FBQUIZ_TEMPLATES_PATH . '/quiz_' . $dumb[0]->layout . '.php' );
        $html = ob_get_clean();        
        return $html;
    }
    
    /* Display Result page on front-end */
    public function result_page($result_id) {
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/result.php' );
        $html = ob_get_clean();        
        return $html;
    }
    
    /* Display My Quizzes page on front-end */
    public function my_quizzes_page($quiz_id) {
        ob_start();
        if (!empty($quiz_id) && is_numeric($quiz_id)) {
            include( FBQUIZ_TEMPLATES_PATH . '/my-quizzes_single.php' );
        } else {
            include( FBQUIZ_TEMPLATES_PATH . '/my-quizzes.php' );
        }
        
        $html = ob_get_clean();        
        return $html;
    }
    
    /* Render Reporting page on dashboard */
    public function render_reporting_page() {
        ob_start();
        $quiz_id = $_REQUEST['quiz']; 
        if (!empty($quiz_id) && is_numeric($quiz_id)) {
            include( FBQUIZ_TEMPLATES_PATH . '/reporting_detail.php' );
        } else {
            include( FBQUIZ_TEMPLATES_PATH . '/reporting.php' );    
        }
        
        $html = ob_get_clean();        
        echo $html;
    }
    
    /* Render Quizzes list page on dashboard */
    function render_all_quizzes_page() {
        $this->fb_quiz->all_quizzes_page();
    }
    
    /* Render New Quiz page */
    function render_new_quiz_page() {
        $this->fb_quiz->new_quiz_page();
    }
    
    /* Render All Questions page */
    function render_all_questions_page() {
        $this->fb_question->all_questions_page();
    }
    
    /* Render New/Edit Question page */
    function render_new_question_page() {
        $this->fb_question->new_question_page();
    }
    
    /* Save new answer into answers table */
    function add_answer() {
        global $wpdb, $FB_TABLE, $FB_URL;
        $p = $_REQUEST['params'];
        
        $created_at = $updated_at = date('Y-m-d H:i:s', time());
        
        $wpdb->insert( $FB_TABLE['answers'],
                    array(
                            'quiz_id'               => $p['quiz_id'],
                            'student_id'            => $p['student_id'],                            
                            'answers'               => serialize($p['answers']),                            
                            'created_at'            => $created_at
                        ),
                    array('%d', '%d', '%s', '%s')
                );               
        echo json_encode(array(status => 1, id => $wpdb->insert_id, redirect_url => $FB_URL['results'] . '/' . $wpdb->insert_id));
        die();
    }
    
    public function findChoiceName($arr, $value) {
        foreach ($arr as $a) {
            if ($a[0] == $value) return $a[1];
        }
        return ' - ';
    }
    
    public function getTotalAttempts($quiz_id, $student_id = null) {
        global $wpdb, $FB_TABLE;
        
        if ($student_id != null) {
            $add_query = " AND student_id={$student_id}";
        } else {
            $add_query = "";
        }                                                                                                            
        $dumb = $wpdb->get_results("SELECT id FROM " . $FB_TABLE['answers'] . " WHERE quiz_id={$quiz_id}{$add_query}");
        return $wpdb->num_rows;
    }
    
    public function getLastScore($quiz_id, $student_id) {
        global $wpdb, $FB_TABLE;
        
        $dumb = $wpdb->get_results("SELECT score, result FROM " . $FB_TABLE['answers'] . " WHERE student_id={$student_id} AND quiz_id={$quiz_id} ORDER BY id DESC");                        
        return $dumb[0]->score;
    }
    
    public function getHighestScore($quiz_id, $student_id) {
        global $wpdb, $FB_TABLE;
        
        $dumb = $wpdb->get_results("SELECT max(score) as score, result FROM " . $FB_TABLE['answers'] . " WHERE student_id={$student_id} AND quiz_id={$quiz_id}");
        return $dumb[0]->score;
    }
    
    
    
    public function getAverageScore($quiz_id) {
        global $wpdb, $FB_TABLE;
        $dumb = $wpdb->get_results("SELECT AVG(score) as result FROM " . $FB_TABLE['answers'] . " WHERE quiz_id={$quiz_id}");
        return round($dumb[0]->result);
    }
    
}

$quizzes = new FB_Quizzes();


//add_action( 'template_redirect', 'my_plugin_template_redirect_intercept' );
function my_plugin_template_redirect_intercept() {
    global $wp_query;    
    $name = $wp_query->get('name');
    $id = $wp_query->get('page');
    if ($name == "quizzes") {
        if (is_int($id)) {
            echo "XXXXXXXXXX22222222222"; exit();
        }
    }
}



//add_filter( 'page_template', 'wpa3396_page_template', 130 );
function wpa3396_page_template( $page_template )
{
    global $wp_query; echo $wp_query->get("quiz_id");
    if ( is_page( 'quiz' ) ) {
        $page_template = dirname( FILE ) . '/custom-page-template.php';
    }
    return $page_template;
}
