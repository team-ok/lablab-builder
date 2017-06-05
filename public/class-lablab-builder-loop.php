<?php

/**
 * Controls looping through the stored content data, including the module templates and generating the html output.
 * 
 * @link       https://github.com/team-ok
 * @since      1.0.0
 * @author     Timo Klemm
 *
 * @package    Lablab_Builder
 * @subpackage Lablab_Builder/public
 */

class Lablab_Builder_Loop {

	/**
	 * All registered lablab builder modules.
	 * 
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $modules    An associative array of all registered lablab builder modules with the name of a module as key and the module object as value.
	 */
	private $modules;


	/**
	 * The currently active (used in the loop) modules.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $active_modules    An associative array of the modules that are currently in use. The array key is the module's name and the value represents the module's counter (the number of times it has been called).
	 */
	private $active_modules;


	/**
	 * The maximum width of the theme's main content wrapper.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $content_width    The maximum width of the theme's main content wrapper.
	 */
	private $content_width;


	/**
	 * The html output of the loop.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $content    The stored html output of the loop to be appended to the page at a later time.
	 */
	private $content;



	public function __construct( $modules ){

		$this->modules = $modules;
		$this->active_modules = array();
		$this->content_width = (int) get_field( 'lablab_max_content_wrapper_width', 'options' );
		$this->content = '';
	}


	/**
	 * Loop through the stored content data, include the module templates and generate the html output.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function run_loop(){

		if ( ! have_rows( 'lablab-content-area' ) ){

			return;
		}

		ob_start();

		$section_index = 0;

		while ( have_rows( 'lablab-content-area' ) ) : the_row();

			beans_open_markup_e( "lablab_section[{$section_index}]", "section", array(
				"class" => "uk-block",
				)
			);

			if ( have_rows( 'lablab-content-elements' ) ):

				beans_open_markup_e( "lablab_grid[{$section_index}]", "div", array(
					"class" => "uk-grid",
					"data-uk-grid-margin" => false
					) 
				);

				while ( have_rows( 'lablab-content-elements' ) ) : the_row();

					$column_width = get_sub_field( 'lablab-column-width' ); // array
					$current_module = get_row_layout();

					beans_open_markup_e( "lablab_column[{$column_width["value"]}]", "div" , array(
						"class" => "uk-width-medium-".$column_width["value"],
						) 
					);
						beans_open_markup_e( "lablab_module[{$current_module}]", "div", array(
							"class" => $current_module,
							)
						);

						if ( isset( $this->modules[$current_module] ) && file_exists( $this->modules[$current_module]->template_path ) ){

							// increment active modules counter
							if ( ! array_key_exists( $current_module, $this->active_modules ) ){

								$this->active_modules[$current_module] = 1;

							} else {

								$this->active_modules[$current_module]++;
							}

							// include the template file of the current module
							include $this->modules[$current_module]->template_path;

						}

						beans_close_markup_e( "lablab_module[{$current_module}]", "div" );

					beans_close_markup_e( "lablab_column[{$column_width["value"]}]", "div" );

				endwhile;

				beans_close_markup_e( "lablab_grid[{$section_index}]", "div" );

			endif;

			beans_close_markup_e( "lablab_section[{$section_index}]", "section" );

			$section_index++;

		endwhile;

		$this->content = ob_get_clean();
	}


	public function get_content(){

		return $this->content;
	}	


	public function get_active_modules(){

		return $this->active_modules;
	}


}