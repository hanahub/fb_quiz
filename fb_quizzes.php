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
        
        add_filter( 'query_vars', array( $this, 'query_vars' ) );
        add_filter( 'the_content', array( $this, "the_content" ) );
        //add_filter( 'the_title', array( $this, "the_title" ) );
    }
    
    function register_styles() {        
        wp_register_style( 'fb-quizzes-style', FBQUIZ_URL . 'assets/front-end/style.css' );
        wp_enqueue_style( 'fb-quizzes-style' );
        
        wp_register_style( 'fb-global-style', FBQUIZ_URL . 'assets/global.css' );
        wp_enqueue_style( 'fb-global-style' );     
    }     
    
    function register_scripts() {
        
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
        wp_register_script( 'fb-blockui-script', FBQUIZ_URL . 'assets/jquery-blockui/jquery.blockUI.min.js', array('jquery') );
        wp_enqueue_script( 'fb-blockui-script' );
        
        wp_register_script( 'fb-datatables-script', FBQUIZ_URL . 'assets/jquery-datatables/jquery.dataTables.min.js', array('jquery') );
        wp_enqueue_script( 'fb-datatables-script' );
        
        wp_register_script( 'fb-quizzes-script', FBQUIZ_URL . 'assets/admin/admin-script.js', array('jquery', 'jquery-ui-sortable') );
        wp_enqueue_script( 'fb-quizzes-script' );
    }
    
    function plugin_activation() {        
        
    }
    
    function query_vars( $query_vars ){
        $query_vars[] = 'quiz_id';
        return $query_vars;
    }
    
    function plugin_deactivation() {
        //wp_clear_scheduled_hook('wpbdp_listings_expiration_check');
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
            'index.php?pagename=quizzes&quiz_id=$matches[1]',
            'top' );
    }    
    
    function admin_menu() {   
        
        add_menu_page( 'FB Quizzes', 'FB Quizzes', 'manage_options', 'quizzes_manager', 'my_custom_menu_page', 'dashicons-admin-post', 3 );     
        
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'All Quizzes', 'manage_options', 'all_quizzes', array( $this, 'render_all_quizzes_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'Add New Quiz', 'manage_options', 'add_new_quiz', array( $this, 'render_new_quiz_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'All Questions', 'manage_options', 'all_questions', array( $this, 'render_all_questions_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'Add New Question', 'manage_options', 'add_new_question', array( $this, 'render_new_question_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'Reporting', 'manage_options', 'reporting', 'addnew_page_callback' );        
        
        remove_submenu_page('quizzes_manager', 'quizzes_manager');
    }
    
    public function is_quiz() {
        global $wp_query;
                                  
        $name = $wp_query->get('name');
        $quiz_id = $wp_query->get('quiz_id');
        
        if ($name == "quizzes") {
            if (is_numeric($quiz_id)) {
                return $quiz_id;
            }
        }
        
        return 0;
    }
    
    
    function the_content($content) {
        $quiz_id = $this->is_quiz();
        
        if ($quiz_id > 0) {  
            return $this->quiz_page($quiz_id);
        } else {
            return $content;
        }   
    }
    
    function the_title($title) {
        $quiz_id = $this->is_quiz();
        if ($quiz_id > 0) {
            return "";
        } else {
            return $content;
        }   
    }
    
    /* Display Quiz page on front-end */
    public function quiz_page($quiz_id) {
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/quiz.php' );
        $html = ob_get_clean();        
        return $html;
    }
    
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
