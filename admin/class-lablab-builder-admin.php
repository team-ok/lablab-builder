<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/team-ok
 * @since      1.0.0
 * @author     Timo Klemm
 * 
 * @package    Lablab_Builder
 * @subpackage Lablab_Builder/admin
 */

class Lablab_Builder_Admin {

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

	private $plugin_deactivated;

	private $column_width_options;

	private $module_loader;

	private $modules;
	

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version           The version of this plugin.
	 * @param    object    $module_loader     The module loader object.           
	 */
	public function __construct( $plugin_name, $version, $module_loader ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->module_loader = $module_loader;
	}

	/**
	 * Check the required installs for lablab builder and deactivate the plugin if something's missing.
	 * @since    1.0.0
	 */
	public function check_requirements(){

		if ( current_user_can( 'activate_plugins' ) && ( ! class_exists( 'acf_pro' ) || ! defined( 'BEANS_VERSION' ) ) ){

			deactivate_plugins( plugin_basename( plugin_dir_path( __DIR__ ) . 'lablab-builder.php' ) );

			$this->plugin_deactivated = true;

		}
	}


	/**
	 * Show an admin notice if the plugin is deactivated because of missing requirements.
	 * @since    1.0.0
	 */
	public function plugin_deactivated_admin_notice() {

		if ( $this->plugin_deactivated === true ): ?>
    		<div class="error">
    			<p>
        		<?php echo sprintf( __( '%1$s requires %2$s and a child theme based on the %3$s to work properly. Please make sure both of them are installed and activated before activating %1$s. For now, the plugin has been deactivated.', 'lablab' ), '<strong>Lablab Builder</strong>', '<strong><a href="https://www.advancedcustomfields.com/pro/">Advanced Custom Fields Pro</a></strong>', '<strong><a href="www.getbeans.io">Beans Framework</a></strong>' );
        		?>
        		</p>
        	</div>
	        
	        <?php
			if ( isset( $_GET['activate'] ) ){
				unset( $_GET['activate'] );
			}

		endif;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'lablab_admin', plugin_dir_url( __FILE__ ) . 'css/lablab-builder-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'lablab_acf_fc_modal', plugin_dir_url( __FILE__ ) . 'css/lablab-acf-fc-modal.css', array(), $this->version, 'all' );

		foreach ( $this->modules as $module ){

			$module->enqueue_admin_styles();
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'lablab_builder_admin', plugin_dir_url( __FILE__ ) . 'js/lablab-builder-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'lablab_acf_fc_modal', plugin_dir_url( __FILE__ ) . 'js/lablab-acf-fc-modal.js', array('jquery'), $this->version, false );
		wp_localize_script( 'lablab_acf_fc_modal', 'lablabModal', array(
				'editLayout' => __( 'Edit layout', 'lablab' ),
			) 
		);
		wp_enqueue_script( 'lablab_acf_fc_grid_layout', plugin_dir_url( __FILE__ ) . 'js/lablab-acf-fc-grid-layout.js', array('jquery'), $this->version, false );
		wp_localize_script( 'lablab_acf_fc_grid_layout', 'lablabColumnWidthOptions', array(
				'options' => $this->column_width_options->get_column_width_options(),
				'text' => $this->column_width_options->get_column_width_text_strings() 
			)
		);
		

		foreach ( $this->modules as $module ){

			$module->enqueue_admin_scripts();
		}

	}

	public function register_ajax(){

		foreach ( $this->modules as $module ){

			$module->admin_ajax_hooks();
		}		
	}

	public function set_column_width_options(){

		$this->column_width_options = new Lablab_Column_Width_Options();
	}

	/**
	 * Adds the lablab-builder options page to the WordPess admin menu.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_options_page(){

		if ( ! function_exists('acf_add_options_page') ){
			return;
		}
		
		acf_add_options_page( 
			array(
				'page_title' => __('Lablab Builder Options', 'lablab'),
				'menu_title' => 'Lablab Builder',
				'menu_slug' => 'lablab_builder',
				'capability' => 'manage_options',
				'icon_url' => 'dashicons-schedule',
			)
		);
	}


	/**
	 * Register plugin options fields.
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_options_page_fields(){

		require_once( plugin_dir_path( __DIR__ ) . 'includes/lablab-builder-options-fields.php' );

	}


	/**
	 * Dynamically populate the associated post types select field of the lablab builder options page with all public post types.
	 * 
	 * @since    1.0.0
	 * @param    array    $field    An array holding the field definitions of the associated post types select field.
	 * @return   array              An array holding the field definitions including the names of all post types as choices of the select field.
	 */
	public function selectable_post_types( $field ){

		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		if ( is_array( $post_types ) ){

			foreach ( $post_types as $post_type ) {
				
				$field['choices'][$post_type->name] = $post_type->label;

			}
		}

		return $field;
	}
	

	/**
	 * Filter acf's flexible content layout title rendering to add a column width label.
	 * 
	 * @since    1.0.0
	 * @param    string    $title    The layout title text.
	 * @param    array     $field    The flexible content field settings array.
	 * @param    array     $layout   The current layout settings array.
	 * @param    int       $i        The current layout index. 
	 *                               Will be 'acfcloneindex' if current layout is a cloneable prototype.
	 * @return   string              The layout title text with some html (the current column width label) added.
	 * 
	 */
	public function fc_grid_module_title_html( $title, $field, $layout, $i){

		if ($field['_name'] == 'lablab-content-elements' && is_numeric($i) ){

			// get width values (array of label and value) of current column 
			$column_width = get_sub_field( 'lablab-column-width' );

			if ( is_array( $column_width ) ){
				
				$html = $title;
				$html .= '<div class="lablab-fc-column-width-wrapper">';
					$html .= '<div class="lablab-fc-column-width">';
						$html .= $column_width['label'];
					$html .= '</div>';
				$html .= '</div>';

				return $html;
			}
		}

		return $title;

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

}
