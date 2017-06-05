<?php

/**
 * ACF field definitions for the lablab builder options page.
 * Included by Lablab_Builder_Admin::add_options_page_fields. 
 * 
 * @link       https://github.com/team-ok
 * @since      1.0.0
 * @author     Timo Klemm
 *
 * @package    Lablab_Builder
 * @subpackage Lablab_Builder/includes
 * @see        Lablab_Builder_Admin::add_options_page_fields
 */

acf_add_local_field_group(array (
	'key' => 'group_lablab_builder_options',
	'title' => __('Lablab Builder Options', 'lablab'),
	'fields' => array (
		array (
			'key' => 'field_5931697c8ce2e',
			'label' => __('General', 'lablab'),
			'name' => '',
			'type' => 'tab',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'placement' => 'top',
			'endpoint' => 0,
		),
		array (
			'key' => 'field_59313c5c0a9f2',
			'label' => __('Select Post Type', 'lablab'),
			'name' => 'lablab_assoc_post_types',
			'type' => 'select',
			'instructions' => __('Select the post type(s) you want lablab builder to be associated with.', 'lablab'),
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array (), // gets dynamically populated by a filter (see Lablab_Builder_Admin::associate_post_types)
			'default_value' => array ('page', 'post'),
			'allow_null' => 0,
			'multiple' => 1,
			'ui' => 1,
			'ajax' => 0,
			'return_format' => 'value',
			'placeholder' => '',
		),
		array (
			'key' => 'field_591ef736f9bc0',
			'label' => __('Design', 'lablab'),
			'name' => '',
			'type' => 'tab',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'placement' => 'top',
			'endpoint' => 0,
		),
		array (
			'key' => 'field_58c9350802cc2',
			'label' => __('Default Column Width', 'lablab'),
			'name' => 'lablab_column_width_default_option',
			'type' => 'select',
			'instructions' => __('Set the default column width option (will be pre-selected when a new module is added).', 'lablab'),
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			// get choices from stored column width object (property of Lablab_Builder_Admin)
			'choices' => $this->column_width_options->get_column_width_options(),
			'default_value' => array (
				0 => '1-4',
			),
			'allow_null' => 0,
			'multiple' => 0,
			'ui' => 0,
			'ajax' => 0,
			'return_format' => 'value',
			'placeholder' => '',
		),
		array (
			'key' => 'field_5904f974db3be',
			'label' => __('Max Content Wrapper Width', 'lablab'),
			'name' => 'lablab_max_content_wrapper_width',
			'type' => 'number',
			'instructions' => __('Set the maximum width (in pixels) of the theme\'s main content wrapper.', 'lablab'),
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 1200,
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'min' => '',
			'max' => '',
			'step' => '',
		),
		array (
			'key' => 'field_591ef429ce61c',
			'label' => __('Highlight Color', 'lablab'),
			'name' => 'lablab_highlight_color',
			'type' => 'color_picker',
			'instructions' => __('Set the color to be used for highlighting.', 'lablab'),
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '#2179bd',
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'lablab_builder',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));