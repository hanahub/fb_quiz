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
 
    public function __construct() { 
        register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
        register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
        
        add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_scripts' ) ); 
        
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }    
    
    public function register_plugin_styles() { 
        wp_register_style( 'fb-quizzes-style', FBQUIZ_URL . 'assets/style.css' );
        wp_enqueue_style( 'fb-quizzes-style' );     
    }     
    
    public function register_plugin_scripts() {     
        wp_register_script( 'fb-quizzes-script', FBQUIZ_URL . 'assets/script.js' );
        wp_enqueue_script( 'fb-quizzes-script' );     
    }
    
    public function plugin_activation() {
        //add_action('init', array($this, 'flush_rules'), 11);
        //$this->_invalidate_pages_cache();
    }
    
    public function plugin_deactivation() {
        //wp_clear_scheduled_hook('wpbdp_listings_expiration_check');
    }
    
    function init() {
        
    }    
    
    function admin_menu() {   
        
        add_menu_page( 'FB Quizzes', 'FB Quizzes', 'manage_options', 'quizzes_manager', 'my_custom_menu_page', 'dashicons-admin-post', 3 );     
        
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'All Quizzes', 'manage_options', 'all_quizzes', array( $this, 'all_quizzes_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'Add New Quiz', 'manage_options', 'add_new_quiz', 'meals_manager_callback' );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'All Questions', 'manage_options', 'all_questions', array( $this, 'all_questions_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'Add New Question', 'manage_options', 'add_new_question', array( $this, 'add_new_question_page' ) );
        add_submenu_page( 'quizzes_manager', 'FB Quizzes', 'Reporting', 'manage_options', 'reporting', 'addnew_page_callback' );        
        
        remove_submenu_page('quizzes_manager', 'quizzes_manager');
    }
    
    function all_quizzes_page() {
        
    }
    
    function add_new_question_page() {
        ob_start();
        include( FBQUIZ_TEMPLATES_PATH . '/add_new_question.php' );
        $html = ob_get_clean();        
        echo $html;
    }
    
}

$quizzes = new FB_Quizzes();
