<?php

class Lablab_Dynamic_LESS_Vars {


	protected $options_styles;


	public function enqueue_options_styles_less_fragment(){

		beans_compiler_add_fragment( 'uikit', array( array( $this, 'get_options_styles_as_less_fragment' ) ), 'less' );
	}

	public function load_options_styles(){

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

}