<?php

/**
 * The public-facing functionality of lablab builder.
 *
 * @link       https://github.com/team-ok
 * @since      1.0.0
 * @author     Timo Klemm
 * 
 * @package    Lablab_Builder
 * @subpackage Lablab_Builder/public
 */

class Lablab_Builder_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $loop;

	private $modules;

	private $active_modules;

	private $module_loader;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $version           The version of this plugin.
	 * @param    object    $module_loader     The module loader object.  
	 */
	public function __construct( $plugin_name, $version, $module_loader ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->module_loader = $module_loader;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/lablab-builder-public.css', array(), $this->version, 'all' );

		// modules
		foreach ( $this->active_modules as $active_module ){
			
			if ( isset( $this->modules[$active_module] ) ){

				$this->modules[$active_module]->enqueue_public_styles();
			}
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/lablab-builder-public.js', array( 'jquery' ), $this->version, false );

		// modules
		foreach ( $this->active_modules as $active_module ){
			
			if ( isset( $this->modules[$active_module] ) ){

				$this->modules[$active_module]->enqueue_public_scripts();
			}
		}
	}

	public function enqueue_options_styles_less_fragment(){

		beans_compiler_add_fragment( 'uikit', array( array( $this, 'get_options_styles_as_less_fragment' ) ), 'less' );
	}

	public function get_options_styles(){

		$this->options_styles = array(
			'lablab-highlight-color' => ( ! empty( get_field( 'lablab_highlight_color', 'options' ) ) ? get_field( 'lablab_highlight_color', 'options' ) : '#2179bd' ),
		);
	}

	public function get_options_styles_as_less_fragment(){
		return '@lablab-highlight-color: ' . $this->options_styles['lablab-highlight-color'] . ';';
	}

	public function add_compiler_cache_version( $args ){
		$args['lablab_options_styles'] = substr(md5( @serialize( $this->options_styles ) ), 0, 7);

		return $args;
	}


	/**
	 * Enqueue module specific uikit scripts and less fragments. Requires the Beans framework.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_uikit_scripts(){

		foreach ( $this->active_modules as $active_module ){
			
			if ( isset( $this->modules[$active_module] ) ){
				
				$this->modules[$active_module]->enqueue_uikit_scripts();

				$this->modules[$active_module]->add_less_fragments();
			}
		}

	}

	public function register_ajax(){

		foreach ( $this->modules as $module ){

			$module->public_ajax_hooks();
		}		
	}

	public function add_loop_content(){

		echo $this->loop->get_content();
	}


	/**
	 * Store an array of all registered lablab builder modules.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function load_modules(){

		$this->modules = $this->module_loader->get_modules();
	}


	public function loop(){

		$this->loop = new Lablab_Builder_Loop( $this->modules );

		$this->loop->run_loop();

		$this->active_modules = array_keys( $this->loop->get_active_modules() );

		// sort array to prevent Beans Compiler from recompiling
		// when only the order of modules in the loop did change
		sort( $this->active_modules );
	}

}
