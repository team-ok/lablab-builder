<?php

/**
 * Loads all registered modules into lablab builder.
 *
 * @link       https://github.com/team-ok
 * @since      1.0.0
 * @author     Timo Klemm
 *
 * @package    Lablab_Builder
 * @subpackage Lablab_Builder/includes
 */

class Lablab_Module_Loader {

	private $modules;

	private $module_classes;

	public function __construct(){

		$this->module_classes = array();

		$this->modules = array();

	}

	public function run(){

		$this->module_classes = apply_filters( 'lablab_builder_modules', $this->module_classes );

		if ( ! empty( $this->module_classes ) && is_array( $this->module_classes ) ){
			
			foreach ( $this->module_classes as $module_class ){
				
				if ( is_subclass_of( $module_class, 'Lablab_Module_Builder' ) ){

					// create module object
					$module = new $module_class();
					
					// store module objects
					$this->modules[$module->name] = $module;
				}
			}
		}
	}


	public function get_modules(){

		return $this->modules;
	}
}