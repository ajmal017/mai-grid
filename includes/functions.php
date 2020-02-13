<?php


/**
 * Get a grid entry.
 *
 * @param   object  The (post, term, user) entry object.
 * @param   object  The object to get the entry.
 *
 * @return  string
 */
function mai_do_entry( $entry, $args ) {

	// Open.

	// Loop through our elements.
	foreach( $args['show'] as $element ) {
		$function = "mai_do_$element";
		$function( $entry, $args );
	}

	// Close.
}

function mai_do_entry_title( $entry, $args ) {

	// Genesis hook.
	if ( mai_entry_needs_genesis_hooks( $args ) ) {
		// TODO, entry header or something?.
	}

	// Content.
	switch ( $args['type'] ) {
		case 'post':
			$title = strip_shortcodes( get_the_content( null, false, $entry ) );
			break;
		case 'term':
			$title = ''; // TODO.
			break;
		case 'user':
			$title = ''; // TODO.
			break;
		default:
			$title = '';
	}

	// Output.
	// genesis_markup(
	// 	[
	// 		'open'    => '<div %s>',
	// 		'close'   => '</div>',
	// 		'context' => 'entry-content',
	// 		'content' => $content,
	// 		'echo'    => false,
	// 		'params'  => [
	// 			'args'  => $args,
	// 			'entry' => $entry,
	// 		],
	// 	]
	// );

	// Genesis hook.
	if ( mai_entry_needs_genesis_hooks( $args ) ) {
		// TODO, entry header or something?.
	}
}

/**
 * Display the post content.
 *
 * Initially based off of genesis_do_post_content().
 *
 * @return  void
 */
function mai_do_entry_content( $entry, $args ) {

	// Genesis hook.
	if ( mai_entry_needs_genesis_hooks( $args ) ) {
		do_action( 'genesis_entry_before_entry_content' );
	}

	// Content.
	switch ( $args['type'] ) {
		case 'post':
			$content = strip_shortcodes( get_the_content( null, false, $entry ) );
			break;
		case 'term':
			$content = term_description( $entry->term_id );
			break;
		case 'user':
			$content = get_the_author_meta( 'description', $entry->ID );
			break;
		default:
			$content = '';
	}

	// Limit.
	if ( $args['content_limit'] > 0 ) {
		$content = mai_get_content_limit( $content, $args['content_limit'] );
	}

	// Output.
	genesis_markup(
		[
			'open'    => '<div %s>',
			'close'   => '</div>',
			'context' => 'entry-content',
			'content' => $content,
			'echo'    => false,
			'params'  => [
				'args'  => $args,
				'entry' => $entry,
			],
		]
	);

	// Genesis hook.
	if ( mai_entry_needs_genesis_hooks( $args ) ) {
		do_action( 'genesis_entry_after_entry_content' );
	}
}

/**
 * Display the post excerpt.
 *
 * Initially based off of genesis_do_post_content().
 *
 * @return  void
 */
function mai_do_excerpt( $args ) {

	// Genesis hook, only if content isn't showing since that has the hook already.
	if ( mai_entry_needs_genesis_hooks( $args ) && ! in_array( 'content', $args['show'] ) ) {
		do_action( 'genesis_entry_before_entry_content' );
	}

	// Excerpt.
	switch ( $args['type'] ) {
		case 'post':
			$excerpt = get_the_excerpt();
			break;
		case 'term':
			$excerpt = ''; // TODO (intro text).
			break;
		case 'user':
			$excerpt = ''; // TODO (possibly not an option for users).
			break;
		default:
			$excerpt = '';
	}

	// Limit.
	if ( $args['content_limit'] > 0 ) {
		$excerpt = mai_get_content_limit( $excerpt, $args['content_limit'] );
	}

	// Output.
	genesis_markup(
		[
			'open'    => '<div %s>',
			'close'   => '</div>',
			'context' => 'entry-excerpt',
			'content' => $excerpt,
			'echo'    => false,
			'params'  => [
				'args'  => $args,
				'entry' => $entry,
			],
		]
	);

	// Genesis hook, only if content isn't showing since that has the hook already.
	if ( mai_entry_needs_genesis_hooks( $args ) && ! in_array( 'content', $args['show'] ) ) {
		do_action( 'genesis_entry_after_entry_content' );
	}
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

function mai_entry_needs_genesis_hooks( $args ) {
	return ( 'post' === $args['type'] ) && in_array( $args['context'], [ 'singular', 'archive' ] );
}
