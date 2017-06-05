<?php

/**
 * Provides static utility methods that can be used throughout lablab builder.
 * 
 * @link       https://github.com/team-ok
 * @since      1.0.0
 * @author     Timo Klemm
 *
 * @package    Lablab_Builder
 * @subpackage Lablab_Builder/public
 */

class LabLab_Builder_Utilities {


	/**
	 * Get the custom excerpt of a given post or generate one if none exists.
	 * @param    array   $args   Array of arguments to control the generation of the excerpt.
	 * @return   string          The (maybe shortened) post excerpt.
	 */
	public static function get_excerpt( $args ){

		$defaults = array(
			'limit' => 200,
			'readmore' => true,
			'readmore_string' => '(...)',
			'class' => 'lablab-excerpt',
			'source' => 'excerpt',
			'suffix' => '...',
			'wrap_p' => true,
			'post' => null // maybe WP_Post object, Post-ID (int) or null (uses global $post)
		);

		$args = wp_parse_args( $args, $defaults );

		$post = get_post( $args['post'] );

		// bail early if not a WP_Post object
		if ( ! $post ){
			return '';
		}

		if ( $args['source'] !== 'excerpt' || empty( $post->post_excerpt ) ){

			$excerpt = $post->post_content;

		} else {

			$excerpt = $post->post_excerpt;
		}

	    $excerpt = preg_replace( " (\[.*?\])", '', $excerpt );

		$excerpt = strip_shortcodes( $excerpt );

		$excerpt = wp_strip_all_tags( $excerpt, true );

	    $excerpt = self::shorten_text( $excerpt, $args['limit'], $args['suffix'] );

		if ( $args['wrap_p'] ){
			$excerpt = '<p class="' . $args['class'] . '">' . $excerpt . '</p>';
		}

		if ( $args['readmore'] ){

			$permalink = get_permalink( $post );
			$excerpt .= '<p class="' . $args['class'] . '-readmore"><a class="uk-text-small more-link" href="' . $permalink . '">' . $args['readmore_string'] . '<i class="uk-icon-angle-double-right uk-margin-small-left"></i></a></p>';
		}

	    return $excerpt;
	}


	/**
	 * Shorten text to a given character count without breaking words.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param    string    $text      The text string to be shortened.
	 * @param    integer   $limit     The character limit.
	 * @param    string    $suffix    An optional suffix string to be appended to the shortened text.
	 * @return   string               The shortened text string.
	 */
	public static function shorten_text($text = '', $limit = 200, $suffix = null){
		
		if ( is_numeric($limit) && strlen($text) > $limit ){
		    $text = substr($text, 0, $limit);
		    $text = substr($text, 0, strripos($text, " "));
		    $text = trim(preg_replace( '/\s+/', ' ', $text));
		    if ($suffix){
		    	$text = $text . $suffix;
		    }
		}

	    return $text;
	}


	/**
	 * Match array keys against given pattern.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param    string    $pattern    Regex pattern to match keys against.
	 * @param    array     $input      The array to be tested.
	 * @param    integer   $flags      May be set to PREG_GREP_INVERT to invert the matching.
	 * @return   array                 An array holding only those elements of array $input whose keys did match the given pattern.
	 */
	public static function preg_grep_keys($pattern, $input, $flags = 0) {

    	return array_flip( preg_grep($pattern, array_flip($input), $flags ) );
	}

}