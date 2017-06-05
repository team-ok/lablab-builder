<?php

/**
 * Sets the column width options to be provided to each lablab builder module.
 *  
 * @link       https://github.com/team-ok
 * @since      1.0.0
 * @author     Timo Klemm
 *
 * @package    Lablab_Builder
 * @subpackage Lablab_Builder/includes
 */

class Lablab_Column_Width_Options {

	/**
	 * The selectable column width options that each module will provide.
	 * @since 	1.0.0
	 * @access 	private
	 * @var   	array
	 */
	private $column_width_options = array();

	/**
	 * The default column width option (will be pre-selected when a new module is added).
	 * @since 	1.0.0
	 * @access 	private
	 * @var   	array
	 */
	private $column_width_default_option;

	/**
	 * The translatable text strings to be used by the grid layout javascript.
	 * @since     1.0.0
	 * @access    private
	 * @var       array
	 */
	private $column_width_text_strings;


	public function __construct(){

		$this->set_column_width_options();
		$this->set_column_width_default_option();
		$this->set_column_width_text_strings();
	}
	
	/**
	 * Set column width options to be provided to each module. First look for user defined options. Alternately set default ones.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_column_width_options(){

		// allow filtering of column width options
		// array keys have to be of format "number hyphen number" (representing a fraction)
		// when custom width options are set, it's neccessary to also provide custom css width classes (width classes provided by uikit only match the default options)
		$options = apply_filters( 'lablab_column_width_options', null );

		if ( is_array($options) ){
			// check if filtered array keys are of format "number hyphen number". Important for dynamic calculations of free space.
			$options = Lablab_Builder_Utilities::preg_grep_keys('/^\d+[-]\d+$/', $options);
		}

		// default
		if ( empty($options) || ! is_array($options) ){
		
			$options = array(
				'1-10' => '10%',
				'1-6' => '16.66%',
				'1-5' => '20%',
				'1-4' => '25%',
				'3-10' => '30%',
				'1-3' => '33.33%',
				'2-5' => '40%',
				'1-2' => '50%',
				'3-5' => '60%',
				'2-3' => '66.66%',
				'7-10' => '70%',
				'3-4' => '75%',
				'4-5' => '80%',
				'5-6' => '83.33%',
				'9-10' => '90%',
				'1-1' => '100%'
			);
		}

		$this->column_width_options = $options;
	}

	/**
	 * Get column width options.
	 * @return    array    The column width option array with keys of type 'number hyphen number' and labels as values. 
	 */
	public function get_column_width_options(){
		
		return $this->column_width_options;
	}


	/**
	 * Set a default width option that will be used when a new module is added to the page.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_column_width_default_option(){
		
		$default = get_field('lablab_column_width_default_option', 'options');

		// if options aren't set yet, set default value
		if ( $default === null ){

			$default = '1-4';

			// cache default value (prevent acf empty value caching)
			if ( function_exists('acf_set_cache') ){
				acf_set_cache('get_value/post_id=options/name=lablab_column_width_default_option', $default);
			}
		}

		$this->column_width_default_option = $default;
	}

	/**
	 * Get the default width option that will be used when a new module is added to the page.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_column_width_default_option(){

		return $this->column_width_default_option;
	}


	/**
	 * Set the translatable column width text strings.
	 */
	private function set_column_width_text_strings(){

		$this->column_width_text_strings = array(
			'currentWidth' => __('Current width', 'lablab'),
			'fillRow' => __('Fills up this row', 'lablab'),
			'fitRow' => __('Fits in this row', 'lablab'),
			'selfToNextRow' => __('Drops to the next row', 'lablab'),
			'followingToNextRow' => __('Pushes following elements to the next row', 'lablab'),
			'fillPrevRow' => __('Fills up the previous row', 'lablab'),
			'selfToPrevRow' => __('Moves to the previous row', 'lablab'),
		);

		$this->column_width_text_strings = apply_filters( 'lablab_column_width_text_strings', $this->column_width_text_strings );
	}


	/**
	 * Get the translatable column width text strings.
	 */
	public function get_column_width_text_strings(){

		return $this->column_width_text_strings;
	}

}