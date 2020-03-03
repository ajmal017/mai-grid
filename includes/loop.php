<?php

// Mai loop.
add_action( 'genesis_before_loop', function() {

	// Bail if not an archive.
	// TODO: This is the archive helper function in mai-engine.
	if ( ! ( is_home() || is_archive() || is_tax() || is_search() || is_date() || is_author() ) ) {
		return;
	}

	// Remove loop and entry elements.
	remove_action( 'genesis_loop', 'genesis_do_loop' );
	add_action( 'genesis_loop', 'mai_do_archive_loop' );

	// $config   = new Mai_Settings_Config( 'archive' );
	// $defaults = $config->get_defaults();
	// $args     = wp_parse_args( (array) get_option( 'maiengine' ), $defaults );

	// // Archive loop open.
	// add_action( 'genesis_before_while', function() use ( $args ) {

	// 	mai_do_entries_open( $args );

	// }, 100 );

	// // Archive loop close.
	// add_action( 'genesis_after_endwhile', function() use ( $args ) {

	// 	mai_do_entries_close( $args );

	// }, 0 );

});

function mai_do_archive_loop() {

	$args = mai_get_template_args();

	if ( have_posts() ) {

		// Enqueue entries CSS.
		mai_enqueue_asset( 'entries', 'css' );

		/**
		 * Fires inside the standard loop, before the while() block.
		 *
		 * @since 2.1.0
		 */
		do_action( 'genesis_before_while' );

		mai_do_entries_open( $args );

		while ( have_posts() ) {

			the_post();

			global $post;
			mai_do_entry( $post, $args );

		} // End of one post.

		mai_do_entries_close( $args );

		/**
		 * Fires inside the standard loop, after the while() block.
		 *
		 * @since 1.0.0
		 */
		do_action( 'genesis_after_endwhile' );

	} else { // If no posts exist.

		/**
		 * Fires inside the standard loop when they are no posts to show.
		 *
		 * @since 1.0.0
		 */
		do_action( 'genesis_loop_else' );

	} // End loop.

}

// TODO: Make this work for singular too.
function mai_get_template_args() {

	// Bail if not an archive.
	// TODO: This is the archive helper function in mai-engine.
	if ( ! ( is_home() || is_archive() || is_tax() || is_search() || is_date() || is_author() ) ) {
		return [];
	}

	$settings = new Mai_Entry_Settings( 'archive' );
	$name     = mai_get_archive_args_name();
	$key      = $name ? sprintf( 'mai_%s_archive', $name ): 'mai_post_archive';
	$args     = wp_parse_args( get_option( $key, [] ), $settings->defaults );

	return apply_filters( 'mai_template_args', $args );
}

function mai_get_archive_args_name() {

	if ( is_home() ) {

		return 'post';

	} elseif ( is_category() ) {

		// TODO: Check if category is supported in config?
		return 'category';

	} elseif ( is_tag() ) {

		return 'post_tag';

	} elseif ( is_tax() ) {

		return get_query_var( 'taxonomy' );

	} elseif ( is_post_type_archive() ) {

		return get_query_var( 'post_type' );

	} elseif ( is_search() ) {

		return 'search';

	} elseif ( is_author() ) {

		return 'author';

	} elseif ( is_date() ) {

		return 'date';

	}

	return false;

}
