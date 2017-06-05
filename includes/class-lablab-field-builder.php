<?php

/**
 * Sets up the lablab builder main acf field group, including all registered modules as flexible content layouts.
 * 
 * @link       https://github.com/team-ok
 * @since      1.0.0
 * @author     Timo Klemm
 *
 * @package    Lablab_Builder
 * @subpackage Lablab_Builder/includes
 */

class Lablab_Field_Builder {

	/**
	 * A Module builder object that contains all the modules to be loaded in lablab builder.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      object
	 */
	protected $modules;


	protected $module_fields;


	/**
	 * The acf flexible content field array that holds the content elements (modules) or a message field if no modules are found.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array
	 */
	protected $content_elements = array();

	/**
	 * The acf repeater field array that holds the content area.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array
	 */
	protected $content_area = array();


	/**
	 * An array that holds the lablab builder field group.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array
	 */
	protected $field_group = array();
	

	public function __construct( $modules ){

		$this->modules = $modules;
		$this->module_fields = array();
	}


	/**
	 * Build the content elements field and include the registered modules.
	 * Alternately (if no registered modules are found) build a message field to print an error message.
	 *
	 * @since    1.0.0
	 * @access   protected
	 */
	protected function build_content_elements(){

		foreach ( $this->modules as $module ) {

			$module_fields = $module->get_module_fields();

			if ( ! empty( $module_fields ) ){
				$this->module_fields[] = $module_fields;
			}
		}

		if ( ! empty( $this->module_fields ) ){
			
			$this->content_elements = 
				array (
					'key' => 'field_lablab_content_elements',
					'label' => __('Content Elements', 'lablab'),
					'name' => 'lablab-content-elements',
					'type' => 'flexible_content',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => 'lablab-content-elements',
						'id' => '',
					),
					'button_label' => __('Add Module', 'lablab'),
					'layouts' => $this->module_fields,
				);

			// allow filtering
			$this->content_elements = apply_filters( 'lablab_content_elements', $this->content_elements );

		} else {
			
			$this->content_elements = 
				array (
					'key' => 'field_lablab_no_modules_message',
					'label' => __('No modules'),
					'type' => 'message',
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'message' => __('Sorry, no registered modules found.', 'lablab'),
				);

			// allow filtering
			$this->content_elements = apply_filters( 'lablab_no_modules_message', $this->content_elements );
		}
	}


	/**
	 * Build the content area field and include the content elements field.
	 *
	 * @since     1.0.0
	 * @access    protected
	 */
	protected function build_content_area(){

		$this->content_area = 
			array (
				'key' => 'field_lablab_content_area',
				'label' => __('Content Area', 'lablab'),
				'name' => 'lablab-content-area',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => 'lablab-content-area',
					'id' => '',
				),
				'collapsed' => '',
				'layout' => 'block',
				'button_label' => __('Add content area', 'lablab'),
				'sub_fields' => array (
					$this->content_elements,
				),
			);

		// allow filtering
		$this->content_area = apply_filters( 'lablab_content_area', $this->content_area );
	}


	/**
	 * Build the lablab builder field group and include the content area field.
	 * 
	 * @since     1.0.0
	 * @access    protected
	 */
	protected function build_field_group(){

		$this->field_group = 
			array (
				'key' => 'group_lablab_builder',
				'title' => 'Lablab Builder',
				'fields' => array (
					$this->content_area,
				),
				'location' => $this->get_assoc_post_types(),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => 1,
				'description' => '',
			);

		// allow filtering
		$this->field_group = apply_filters( 'lablab_builder_field_group', $this->field_group );
	}


	/**
	 * Get the post types that lablab builder should be associated with.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @return   array    A multidimensional array holding acf location settings for the lablab builder field group.
	 */
	protected function get_assoc_post_types(){

		$post_types = get_field( 'lablab_assoc_post_types', 'options' );

		// if options aren't set yet, set default value
		if ( $post_types === null ){

			$post_types = array('post', 'page');

			// cache default value (prevent acf empty value caching)
			if ( function_exists('acf_set_cache') ){
				acf_set_cache('get_value/post_id=options/name=lablab_assoc_post_types', $post_types);
			}

		}

		$assoc_post_types = array();

		if ( ! empty( $post_types ) ):

			foreach ( $post_types as $post_type ){

				$assoc_post_types[] = array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => $post_type
					)
				);
			}

		endif;

		return $assoc_post_types;
	}


	/**
	 * Setup acf fields
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function run(){

		$this->build_content_elements();

		$this->build_content_area();

		$this->build_field_group();
	}


	public function get_fields(){

		return $this->field_group;
	}

}