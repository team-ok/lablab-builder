<?php 

/**
 * Builds modules from given acf field definitions to be loaded in lablab builder.
 * Field definitions may be provided either in a json or a php file.
 *
 * @link       https://github.com/team-ok
 * @since      1.0.0
 * @author     Timo Klemm
 * 
 * @package    Lablab_Builder
 * @subpackage Lablab_Builder/includes
 */

abstract class Lablab_Module_Builder {

	/**
	 * The module title (or acf flexible content layout label).
	 * Will also be used as the base for generating name and/or key values, if these aren't specified in the module subclass.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $title    The module title.
	 */
	public $title;


	/**
	 * The acf field name of the module. Also used to internally identify a module.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $name    The acf field name of the module.
	 */
	public $name;


	/**
	 * The acf field key of the module.
	 * This must be a unique string that starts with 'field_'.
	 * Will be generated automatically based on the title property if no value is set in the subclass.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $key    The acf field key of the module.
	 */
	public $key;


	/**
	 * The version number of the module. Used when enqueuing styles and scripts (for cache busting purposes)
	 * 
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    The current version of the module.
	 */
	public $version;


	/**
	 * The module-specific acf fields of a lablab builder module. 
	 * Must be an absolute path to either a folder containing a single acf field group json or php file or the absolute path to such a file.
	 * 
	 * In case of a php file (allows translation and dynamic settings) the script should not use the acf function 'acf_add_local_field_group', but instead return only the value of the 'fields'-key of the local field group array exported by acf.
	 * 
	 * In case of a json file the file exported by acf can be used without any modifications.
	 * 
	 * @since 	1.0.0
	 * @access 	public
	 * @var   	array    $fields    The module-specific acf fields of the module.
	 */
	public $fields;


	/**
	 * The css file(s) to be included only when the module is being used.
	 * May be either an absolute path to a folder containing css files
	 * or a url of a single file.
	 * In case of a folder each css file found will be included.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $public_css    The css file(s) to be included only when the module is being used.
	 */
	public $public_css;


	/**
	 * The css file(s) to be included only on admin pages.
	 * May be either an absolute path to a folder containing css files or a url of a single file.
	 * In case of a folder each css file found will be included.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       string    $admin_css    The css file(s) to be included only on admin pages.
	 */
	public $admin_css;


	/**
	 * The javascript file(s) to be included on public pages, but only when the module is being used.
	 * May be either an absolute path to a folder containing js files or a url of a single file.
	 * In case of a folder each js file found will be included.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       string    $public_js    The javascript file(s) to be included on public pages, but only when the module is being used.
	 */
	public $public_js;


	/**
	 * The javascript file(s) to be included only on admin pages.
	 * May be either an absolute path to a folder containing js files or a url of a single file.
	 * In case of a folder each js file found will be included.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       string    $admin_js    The javascript file(s) to be included only on admin pages.
	 */
	public $admin_js;


	/**
	 * The uikit core components to be included (array of uikit core component names).
	 * For a list of available uikit core components, see https://getuikit.com/v2/docs/core.html
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       array    $uikit_core    The uikit core components to be included.
	 */
	public $uikit_core;


	/**
	 * The uikit add-on components to be included (array of uikit add-on component names).
	 * For a list of available uikit add-on components, see https://getuikit.com/v2/docs/components.html
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       array    $uikit_addons    The uikit add-on components to be included.
	 */
	public $uikit_addons;


	/**
	 * The less fragment(s) to be added to the beans uikit less compiler.
	 * May be either an absolute path to a folder containing less files or a an absolute path to such a file.
	 * In case of a folder each valid less file found will be added to the compiler
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       string    $less_fragments    The less fragment(s) to be added to the beans uikit less compiler.
	 */
	public $less_fragments;


	/**
	 * The absolute path to a partial template file that both retrieves content data and prints the output.
	 * Will be included as needed by the lablab builder loop object and therefore has access to the properties of that object.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       string     $template_path    The absolute path to a partial template file that both retrieves content data and prints the output.
	 */
	public $template_path;


	/**
	 * A field that allows to set the width of a grid column. Gets attached to each module. 
	 * This property is not module-specific and therefore can't be overwritten by a subclass.
	 * @since 	1.0.0
	 * @access 	private
	 * @var   	array    $column_width_field    A field that allows to set the width of a grid column.
	 */
	private $column_width_field;


	/**
	 * The module to be loaded as a flexible content layout in lablab builder.
	 * @since 	1.0.0
	 * @access 	private
	 * @var   	array    $module    The module to be loaded as a flexible content layout in lablab builder.
	 */
	private $module;


	/**
	 * Register a module.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param    array    $module_class_names    The names of all registered lablab module classes before registering this module.
	 * @return   array    						 The names of all registered lablab module classes after registering this module.
	 */
	public static function register($module_class_names){

		$module_class_names[] = get_called_class();

		return $module_class_names;
	}


	/**
	 * Enqueue javascript files only on admin pages.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_admin_scripts(){

		$this->enqueue_assets( $this->admin_js, 'js' );
		
	}


	/**
	 * Enqueue stylesheets only on admin pages.
	 * 
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_admin_styles(){

		$this->enqueue_assets( $this->admin_css, 'css' );

	}


	/**
	 * Enqueue javascript files on public pages, but only when the module is being used.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_public_scripts(){

		$this->enqueue_assets( $this->public_js, 'js' );
		
	}


	/**
	 * Enqueue stylesheets on public pages, but only when the module is being used.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_public_styles(){

		$this->enqueue_assets( $this->public_css, 'css' );

	}


	/**
	 * Enqueue css stylesheets or javascript files of the module.
	 * The asset source and type are set in the module subclass.
	 * The default values of dependencies, the media type (when enqueuing stylesheets) and the in_footer setting (when enqueuing javascript files) can be overwritten dynamically by hooking to a filter.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string    $asset_src    The absolute path to a folder containing asset files or the url of a single file.
	 * @param    string    $type         The type of asset to be enqueued. May be either 'css' or 'js'.
	 */
	private function enqueue_assets( $asset_src, $type ){

		if ( empty( $asset_src ) || empty( $type ) ){
			
			return;
		}

		$asset_url = '';
		$dependencies = array();
		$media = 'all';
		$in_footer = false;

		// if a path to a directory is provided
		if ( is_dir( $asset_src ) ){

			$dir = opendir( $asset_src );

			while ( false !== ( $file = readdir( $dir ) ) ):

				// skip if file is not of given type
				if ( pathinfo( $file, PATHINFO_EXTENSION ) !== $type ){

					continue;
				}

				// get url of this asset's directory and append the asset filename
				$asset_url = plugin_dir_url( trailingslashit( $asset_src ) . $file ) . $file;

				// use filename based enqueue handle to prevent multiple enqueues of the same file (e.g. if two modules need to include the same css files)
				$asset_handle = str_replace( '.' . $type, '', $file );

			endwhile;

		// if a url is provided
		} elseif ( pathinfo( $asset_src, PATHINFO_EXTENSION ) === $type && filter_var( $asset_src, FILTER_VALIDATE_URL ) !== false ){

			// use filename based enqueue handle to prevent multiple enqueues of the same file (e.g. if two modules need to include the same js files)
			$asset_handle = substr( strrchr( $asset_src, '/' ), 1, - ( strlen( $type ) + 1 ) );

			$asset_url = $asset_src;

		}

		if ( $asset_url ){

			// allow filtering of dependencies
			// $asset_handle and $type may be used in a conditional statement for dynamic filtering
			$dependencies = (array) apply_filters( 'lablab_enqueue_dependencies', $dependencies, $asset_handle, $type );

			// enqueue asset
			if ( $type === 'css' ){

				// allow filtering of media type
				// $asset_handle may be used in a conditional statement for dynamic filtering
				$media = apply_filters( 'lablab_enqueue_media_type', $media, $asset_handle );

				wp_enqueue_style( $asset_handle, esc_url_raw( $asset_url ), $dependencies, $this->version, $media );

			} elseif ( $type === 'js' ) {

				// allow filtering of 'in_footer' setting
				// $asset_handle may be used in a conditional statement for dynamic filtering
				$in_footer = apply_filters( 'lablab_enqueue_in_footer', $in_footer, $asset_handle );

				wp_enqueue_script( $asset_handle, esc_url_raw( $asset_url ), $dependencies, $this->version, $in_footer );
			}
		}

	}


	/**
	 * Enqueue uikit components to the beans compiler.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function enqueue_uikit_scripts(){

		if ( ! empty( $this->uikit_core ) ) {

			beans_uikit_enqueue_components( (array) $this->uikit_core );
		}	

		if ( ! empty( $this->uikit_addons ) ) {

			beans_uikit_enqueue_components( (array) $this->uikit_addons, 'add-ons' );
		}
	}


	/**
	 * Add less fragments to the beans compiler.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_less_fragments(){

		// bail early if no less fragments provided
		if ( empty( $this->less_fragments ) ){

			return;
		}

		// if a path to a directory is provided
		if ( is_dir( $this->less_fragments ) ){

			$dir = opendir( $this->less_fragments );

			while ( false !== ( $fragment = readdir( $dir ) ) ):

				// skip if it's not a less file
				if ( pathinfo( $fragment, PATHINFO_EXTENSION ) !== 'less' ){

					continue;
				}

				$fragment_path = trailingslashit( $this->less_fragments ) . $fragment;

				beans_compiler_add_fragment( 'uikit', $fragment_path, 'less' );

			endwhile;

		// if a path to a file is provided
		} elseif ( pathinfo( $this->less_fragments, PATHINFO_EXTENSION ) === 'less' && file_exists( $this->less_fragments ) ){

			beans_compiler_add_fragment( 'uikit', $this->less_fragments, 'less' );

		}

	}


	/**
	 * Define ajax hooks for logged-in users.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_ajax_hooks(){
		// to be overwritten by module subclass
	}


	/**
	 * Define ajax hooks for visitors.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function public_ajax_hooks(){
		// to be overwritten by module subclass
	}


	/**
	 * Build the column width select field.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @return    array    An associative array holding the field definitions for the grid column width select field that every module incorporates.
	 */
	private function build_column_width_field(){

		$options = new Lablab_Column_Width_Options();

		$column_width_field = array(
			'key' => 'field_lablab_column_width_' . $this->name,
			'label' => __('Column Width', 'lablab'),
			'name' => 'lablab-column-width',
			'type' => 'select',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => $options->get_column_width_options(),
			'default_value' => $options->get_column_width_default_option(),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 1,
			'ajax' => 0,
			'return_format' => 'array',
			'placeholder' => '',
		);

		// allow filtering
		$__column_width_field = apply_filters('lablab_column_width_field', $column_width_field);

		if ( ! empty( $__column_width_field ) && is_array( $__column_width_field ) ){

			$column_width_field = $__column_width_field;
		}

		return $column_width_field;
	}


	/**
	 * Build module fields out of acf field group data extracted from a json or php file.
	 *
	 * @since     1.0.0
	 * @access    private
	 * @return    array    A multidimensional associative array holding the module field definitions.
	 */
	private function build_module_fields(){

		// bail early if no field definitions provided
		if ( ! isset( $this->fields ) ){
			return;
		}

		$fields = null;

		// if a path to a directory is provided
		if ( is_dir( $this->fields ) ){

			$dir = opendir( $this->fields );

			while( false !== ( $file = readdir($dir)) ):
		    	
		    	$extension = pathinfo( $file, PATHINFO_EXTENSION );

		    	if ( $extension  === 'json' ){

		    		$fields = $this->validate_json_fields( $file, $this->fields );

		    	} elseif ( $extension === 'php' ){

		    		$fields = $this->validate_php_fields( $file, $this->fields );

		    	}

		    	if ( $fields ){

		    		// stop after first valid field definition file is found
		    		break;
		    	}

		    endwhile;

		    	
		// if a path to a file is provided
		} elseif ( file_exists( $this->fields ) ) {

			$extension = pathinfo( $this->fields, PATHINFO_EXTENSION );
    		
    		if ( $extension === 'json' ){

	    		$fields = $this->validate_json_fields( $this->fields );

	    	} elseif ( $extension === 'php' ){

	    		$fields = $this->validate_php_fields( $this->fields );

	    	}

	    }

	    return $fields;
	}


	/**
	 * Validate provided json file. Check if there are acf fields defined.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    string    $file    Path to a json file.
	 * @param    string    $dir     Path to a directory containing a json file.
	 * @return   array|null         Either an array containing acf field data or null if validation fails.
	 */
	private function validate_json_fields( $file, $dir = null ){
    	
    	if ( isset( $dir ) ){
    		$file = trailingslashit( $dir ) . $file;
    	}

    	// read json
    	$json = file_get_contents( $file );
    	
    	if ( empty($json) ) {

    		return;
    	}
    	
    	// decode
    	$json = json_decode($json, true);

    	// if it's an automatically generated acf json file (field definitions not wrapped in an array)
    	if ( isset( $json['fields']) ) {

    		return $json['fields'];

    	// if it's an acf json export file (field definitions wrapped in an array)
    	} elseif ( isset( $json[0]['fields'] ) ){

    		return $json[0]['fields'];
    	}

    	return;
	}


	/**
	 * Load provided php field definition file. Check if it returns an array that has all the required keys.
	 *
	 * @since    1.0.0
	 * @param    string    $file    Path to a php file or name of such a file (if $dir is set)
	 * @param    string    $dir     Path to a directory 
	 * @return   array|null         Either an array containing acf field data or null if validation fails.
	 */
	private function validate_php_fields( $file, $dir = null ){

		if ( isset( $dir ) ){
    		$file = trailingslashit( $dir ) . $file;
    	}

    	// use output buffering to suppress any possible output of included file
    	ob_start();
    		$fields = include $file;
    	ob_end_clean();

    	if ( ! is_array( $fields ) ){
    		return;
    	}

    	$required_keys = array_flip( array( 'key', 'name', 'label', 'type' ) );

    	foreach ( $fields as &$field ) {

    		$missing_keys = array_diff_key( $required_keys, $field );

    		if ( ! empty( $missing_keys ) ){
    			return;
    		}

    		// add 'field_'-prefix to value of $field['key'] if it's missing
    		if ( strpos( $field['key'], 'field_' ) !== 0 ){
    			$field['key'] = 'field_' . $field['key'];
    		}
    	}

    	return $fields;
	}


	/**
	 * Build the module to be used in lablab builder. A module consists of a column width field and an acf field group defined in the child class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return   array    An associative array holding the fields required for defining an acf flexible content layout.
	 */
	private function build_module(){

		// bail early if a required property is missing
		if ( ! isset( $this->title ) || empty( $this->name ) || empty( $this->key ) ){

			return;
		}

		$this->column_width_field = $this->build_column_width_field();
		$this->fields = $this->build_module_fields();

		// exit if there are no module-specific fields 
		if ( empty( $this->fields ) || ! is_array( $this->fields ) ){

			return;
		}
			
		// build the module and return it
		return array(
			'key' => $this->key,
			'name' => $this->name,
			'label' => $this->title,
			'display' => 'block',
			'sub_fields' => array_merge( array($this->column_width_field), $this->fields ),
		);
		
	}


	/**
	 * Get the module fields.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   array|null    An array of module fields (acf flexible content layout) to be used in lablab builder or null if a module could not successfully be built.
	 */
	public function get_module_fields(){
		
		$this->module = $this->build_module();

		return $this->module;
	}
}