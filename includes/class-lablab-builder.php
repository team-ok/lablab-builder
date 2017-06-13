<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, general hooks, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       https://github.com/team-ok
 * @since      1.0.0
 * @author     Timo Klemm
 *
 * @package    Lablab_Builder
 * @subpackage Lablab_Builder/includes
 */

class Lablab_Builder {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Lablab_Builder_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	protected $module_loader;

	/**
	 * All registered lablab builder modules.
	 * 
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $modules    An array that holds all registered lablab builder module objects.
	 */
	protected $modules;


	protected $field_builder;


	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'lablab_builder';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->setup();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->set_dynamic_less_vars();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Lablab_Builder_Loader. Orchestrates the hooks of the plugin.
	 * - Lablab_Builder_i18n. Defines internationalization functionality.
	 * - Lablab_Builder_Admin. Defines all hooks for the admin area.
	 * - Lablab_Builder_Public. Defines all hooks for the public side of the site.
	 * - Lablab_Builder_Loop. Controls looping through the stored content data, including the module templates and generating the html output.
	 * - Lablab_Column_Width_Options. Sets the column width options to be provided to each module.
	 * - Lablab_Module_Builder (abstract). Builds standardized modules from given acf field definitions to be loaded in lablab builder.
	 * - Lablab_Module_Loader. Loads all registered modules into lablab builder.
	 * - Lablab_Field_Builder. Sets up the lablab builder main acf field group, including all registered modules as flexible content layouts.
	 * - Lablab_Builder_Utilities. Provides static utility methods that can be used throughout lablab builder.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * Create an instance of the module loader which will hold all registered modules.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lablab-builder-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lablab-builder-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-lablab-builder-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-lablab-builder-public.php';
		
		/**
		 * The class responsible for looping through the stored content data, including the module templates and generating the html output.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-lablab-builder-loop.php';		

		/**
		 * The class responsible for setting the column width options to be provided to each module.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lablab-column-width-options.php';

		/**
		 * The abstract class responsible for building modules from given acf field definitions to be loaded in lablab builder.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lablab-module-builder.php';

		/**
		 * The class responsible for loading all registered modules into lablab builder.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lablab-module-loader.php';

		/**
		 * The class responsible for setting up the plugin's main acf field group.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lablab-field-builder.php';


		/**
		 * The class responsible for setting up global LESS vars dynamically
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lablab-dynamic-less-vars.php';

		/**
		 * A class providing static utility methods that can be used throughout lablab builder.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-lablab-builder-utilities.php';


		$this->loader = new Lablab_Builder_Loader();
		
		$this->module_loader = new Lablab_Module_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Lablab_Builder_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Lablab_Builder_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}


	private function setup(){

		$this->loader->add_action( 'acf/init', $this, 'load_modules', 5 );
		$this->loader->add_action( 'acf/init', $this, 'build_fields' );
		$this->loader->add_action( 'acf/init', $this, 'register' );
	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Lablab_Builder_Admin( $this->get_plugin_name(), $this->get_version(), $this->module_loader );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'check_requirements' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'plugin_deactivated_admin_notice' );
		$this->loader->add_action( 'acf/init', $plugin_admin, 'set_column_width_options' );
		$this->loader->add_action( 'acf/init', $plugin_admin, 'load_modules' );
		$this->loader->add_action( 'acf/init', $plugin_admin, 'register_ajax' );
		$this->loader->add_action( 'acf/input/admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'acf/input/admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );
		$this->loader->add_filter( 'acf/load_field/name=lablab_assoc_post_types', $plugin_admin, 'selectable_post_types' );
		$this->loader->add_action( 'acf/init', $plugin_admin, 'add_options_page_fields' );
		$this->loader->add_filter( 'acf/fields/flexible_content/layout_title', $plugin_admin, 'fc_grid_module_title_html', 10, 4 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Lablab_Builder_Public( $this->get_plugin_name(), $this->get_version(), $this->module_loader );

		$this->loader->add_action( 'acf/init', $plugin_public, 'load_modules' );
		$this->loader->add_action( 'acf/init', $plugin_public, 'register_ajax' );
		$this->loader->add_action( 'wp', $plugin_public, 'loop' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'beans_uikit_enqueue_scripts', $plugin_public, 'enqueue_uikit_scripts' );
		$this->loader->add_action( 'beans_post_content_append_markup', $plugin_public, 'add_loop_content' );

	}


	/**
	 * Load and store all registered lablab builder module objects.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function load_modules(){

		$this->module_loader->run();
		$this->modules = $this->module_loader->get_modules();
	}


	/**
	 * Build the fields that make up the lablab builder acf field group.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function build_fields(){

		$this->field_builder = new Lablab_Field_Builder( $this->modules );
		$this->field_builder->run();
	}


	/**
	 * Register lablab builder as acf field group.
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	public function register(){

		if ( ! empty( $this->field_builder->get_fields() ) ){

			acf_add_local_field_group( $this->field_builder->get_fields() );
		}
	}

	/**
	 * Get style settings from Lablab Builder Options Page and set up global LESS vars to be used by modules.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function set_dynamic_less_vars(){

		$dynamic_less_vars = new Lablab_Dynamic_LESS_Vars();

		$this->loader->add_action( 'init', $dynamic_less_vars, 'load_options_styles' );
		$this->loader->add_action( 'beans_uikit_enqueue_scripts', $dynamic_less_vars, 'enqueue_options_styles_less_fragment', 5 );
		$this->loader->add_filter( 'beans_uikit_euqueued_styles_args', $dynamic_less_vars, 'add_compiler_cache_version' );
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}


	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Lablab_Builder_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}


	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}