<?php

// TEMPORARY TILL I'M USING MAI ENGINE.
add_action( 'after_setup_theme', function() {

	$sizes = [
		'landscape-xl' => mai_apply_aspect_ratio( 1920, '16:9' ),
		'landscape-lg' => mai_apply_aspect_ratio( 1280, '16:9' ),
		'landscape-md' => mai_apply_aspect_ratio( 896, '4:3' ),
		'landscape-sm' => mai_apply_aspect_ratio( 512, '4:3' ),
		'landscape-xs' => mai_apply_aspect_ratio( 256, '4:3' ),
		'portrait-md'  => mai_apply_aspect_ratio( 896, '3:4' ),
		'portrait-sm'  => mai_apply_aspect_ratio( 512, '3:4' ),
		'portrait-xs'  => mai_apply_aspect_ratio( 256, '3:4' ),
		'square-md'    => mai_apply_aspect_ratio( 896, '1:1' ),
		'square-sm'    => mai_apply_aspect_ratio( 512, '1:1' ),
		'square-xs'    => mai_apply_aspect_ratio( 256, '1:1' ),
		'tiny'         => mai_apply_aspect_ratio( 80, '1:1' ),
	];

	foreach( $sizes as $name => $values ) {
		add_image_size( $values[0], $values[1], $values[2] );
	}

});
function mai_apply_aspect_ratio( $width = 896, $ratio = '16:9' ) {
	$ratio       = explode( ':', $ratio );
	$x           = $ratio[0];
	$y           = $ratio[1];
	$height      = (int) $width / $x * $y;
	return [ $width, $height, true ];
}

/**
 * // Loop.
 * @link  https://github.com/studiopress/genesis/blob/master/lib/structure/loops.php#L64
 * // Post.
 * @link  https://github.com/studiopress/genesis/blob/master/lib/structure/post.php
 */

// do_action( 'genesis_entry_header' );
// do_action( 'genesis_before_entry_content' );
// do_action( 'genesis_entry_content' );
// do_action( 'genesis_after_entry_content' );
// do_action( 'genesis_entry_footer' );

/**
 * Echo a grid entry.
 *
 * @param   object  The (post, term, user) entry object.
 * @param   object  The object to get the entry.
 *
 * @return  string
 */
function mai_do_entry( $entry, $args ) {
	$entry = new Mai_Entry( $entry, $args );
	$entry->render();
}

function mai_get_image_sizes() {
	$breakpoints = mai_get_breakpoints();
	return [
		'sm' => $breakpoints['xs'],
		'md' => $breakpoints['md'],
		'lg' => $breakpoints['xl'],
	];
}

function mai_get_breakpoints() {

	// "screen-xs": "400px", // mobile portrait
	// "screen-sm": "600px", // mobile landscape
	// "screen-md": "800px", // tablet portrait
	// "screen-lg": "1000px", // tablet landscape
	// "screen-xl": "1200px", // desktop

	return [
		'xs' => '',
		'sm' => 512,
		'md' => 768,
		'lg' => 1024,
		'xl' => 1152,
	];
}

function mai_get_breakpoint_columns( array $columns ) {

	$columns = wp_parse_args( $columns, [
		'xs' => 1,
		'sm' => 1,
		'md' => 1,
		'lg' => 1,
		'xl' => 1,
	]);

	return $content;

	// static $current_columns;
	// // Set the current column.
	// if ( ! isset( $current_columns ) ) {
	// 	$current_columns = $original_value;
	// }
	// // If using responsive settings, and have a value.
	// if ( $this->args['columns_responsive'] && is_numeric( $value ) ) {
	// 	$current_columns = $value;
	// 	return $current_columns;
	// }
	// $compare = is_numeric( $previous_value ) ? $previous_value : $current_columns;
	// switch ( $compare ) {
	// 	case 6:
	// 		$current_columns = 4;
	// 	break;
	// 	case 5:
	// 		$current_columns = 3;
	// 	break;
	// 	case 4:
	// 		$current_columns = 2;
	// 	break;
	// 	case 3:
	// 		$current_columns = 2;
	// 	break;
	// 	case 2:
	// 		$current_columns = 1;
	// 	break;
	// 	case 1:
	// 		$current_columns = 1;
	// 	break;
	// 	case 0:
	// 		$current_columns = 0;
	// 	break;
	// }
	// return absint( $current_columns );
}

/**
 * Return content stripped down and limited content.
 *
 * Strips out tags and shortcodes, limits the output to `$max_char` characters.
 *
 * @param   string  $content The content to limit.
 * @param   int     $limit   The maximum number of characters to return.
 *
 * @return  string  Limited content.
 */
function mai_get_content_limit( $content, $limit ) {

	// Strip tags and shortcodes so the content truncation count is done correctly.
	$content = strip_tags( strip_shortcodes( $content ), apply_filters( 'get_the_content_limit_allowedtags', '<script>,<style>' ) );

	// Remove inline styles / scripts.
	$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

	// Truncate $content to $limit.
	$content = genesis_truncate_phrase( $content, $limit );

	return $content;
}

// function mai_is_post_template( $args ) {
// 	return ( 'post' === $args['type'] ) && in_array( $args['context'], [ 'singular', 'archive' ] );
// }
