<?php

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
		$function = "mai_do_entry_$element";


		$function( $entry, $args, $element );
	}

	// Close.
}

/**
 * Backwards compatibility for Genesis hooks.
 */
function mai_do_genesis_entry_header() {
	do_action( 'genesis_entry_header' );
}
function mai_do_genesis_before_entry_content() {
	do_action( 'genesis_before_entry_content' );
}
function mai_do_genesis_entry_content() {
	do_action( 'genesis_entry_content' );
}
function mai_do_genesis_after_entry_content() {
	do_action( 'genesis_after_entry_content' );
}
function mai_do_genesis_entry_footer() {
	do_action( 'genesis_entry_footer' );
}

/**
 * Display the post content.
 *
 * Initially based off of genesis_do_post_title().
 *
 * @return  void
 */
function mai_do_entry_title( $entry, $args ) {

	$link = false;

	// Title.
	switch ( $args['type'] ) {
		case 'post':

			// Not a block.
			if ( 'block' !== $args['block'] ) {

				// Singular and archive wrap and title text.
				if ( 'singular' === $args['context'] ) {
					$wrap  = 'h1';
					$title = genesis_entry_header_hidden_on_current_page() ? get_the_title() : '';
				} else {
					$wrap  = 'h2';
					$title = get_the_title();
				}

				// If HTML5 with semantic headings, wrap in H1.
				$wrap  = genesis_get_seo_option( 'semantic_headings' ) ? 'h1' : $wrap;

				// Filter the post title text.
				$title = apply_filters( 'genesis_post_title_text', $title );

				// Wrap in H2 on static homepages if Primary Title H1 is set to title or description.
				if (
					( 'singular' === $args['context'] )
					&& is_front_page()
					&& ! is_home()
					&& genesis_seo_active()
					&& 'neither' !== genesis_get_seo_option( 'home_h1_on' )
				) {
					$wrap = 'h2';
				}

				/**
				 * Entry title wrapping element.
				 *
				 * The wrapping element for the entry title.
				 *
				 * @param string $wrap The wrapping element (h1, h2, p, etc.).
				 */
				$wrap = apply_filters( 'genesis_entry_title_wrap', $wrap );

				// Link it, if necessary.
				if ( ( 'archive' === $args['context'] ) && apply_filters( 'genesis_link_post_title', true ) ) {
					$link = true;
				}
			}
			// Block.
			else {

				$wrap  = 'h3';
				$title = get_the_title( $entry );
				$link  = true;
			}
		break;
		case 'term':
			$wrap  = 'h3'; // Only blocks use this function for terms.
			$title = ''; // TODO.
		break;
		case 'user':
			$wrap  = 'h3'; // Only blocks use this function for users.
			$title = ''; // TODO.
		break;
		default:
			$title = '';
	}

	// Bail if no title.
	if ( ! $title ) {
		return;
	}

	// If linking.
	if ( $link ) {
		$title = genesis_markup(
			[
				'open'    => '<a %s>',
				'close'   => '</a>',
				'content' => $title,
				'context' => 'entry-title-link',
				'echo'    => false,
				'params'  => [
					'args'  => $args,
					'entry' => $entry,
				],
			]
		);
	}

	/**
	 * Entry title wrapping element.
	 *
	 * The wrapping element for the entry title.
	 *
	 * @param  string  $wrap The wrapping element (h1, h2, p, etc.).
	 */
	$wrap = apply_filters( 'mai_entry_title_wrap', $wrap, $args );

	// Build the output.
	$output = genesis_markup(
		[
			'open'    => "<{$wrap} %s>",
			'close'   => "</{$wrap}>",
			'content' => $title,
			'context' => 'entry-title',
			'echo'    => false,
			'params'  => [
				'wrap'  => $wrap,
				'args'  => $args,
				'entry' => $entry,
			],
		]
	);

	// Add genesis filter.
	if ( 'post' === $args['type'] ) {
		$output = apply_filters( 'genesis_post_title_output', $output, $wrap, $title ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- title output is left unescaped to accommodate trusted user input. See https://codex.wordpress.org/Function_Reference/the_title#Security_considerations.
	}

	echo $output;

}

/**
 * Display the post excerpt.
 *
 * Initially based off of genesis_do_post_content().
 *
 * @return  void
 */
function mai_do_entry_excerpt( $args ) {

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

}

/**
 * Display the post content.
 *
 * Initially based off of genesis_do_post_content().
 *
 * @return  void
 */
function mai_do_entry_content( $entry, $args ) {

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

}

/**
 * Display the header meta.
 *
 * Initially based off genesis_post_info().
 */
function mai_do_entry_header_meta( $entry, $args ) {

	$header_meta = '';

	// Header meta.
	switch ( $args['type'] ) {
		case 'post':
			// Not a block.
			if ( 'block' !== $args['block'] ) {
				if ( post_type_supports( get_post_type(), 'genesis-entry-meta-before-content' ) ) {
					// TODO: Once other post types use our settings we'll need to account for it here.
					$header_meta = genesis_get_option( 'entry_meta_before_content' );
					$header_meta = apply_filters( 'genesis_post_info', $header_meta );
					$header_meta = wp_kses_post( $header_meta );
					$header_meta = trim( $header_meta );
				}
			}
			// A block.
			else {
				$header_meta = $args['header_meta'];
			}
		break;
		case 'term':
			$header_meta = ''; // TODO.
		break;
		case 'user':
			$header_meta = ''; // TODO.
		break;
		default:
			$header_meta = '';
	}

	// Bail if none.
	if ( ! $header_meta ) {
		return;
	}

	// Run shortcodes.
	$header_meta = do_shortcode( $header_meta );

	genesis_markup(
		[
			'open'    => '<p %s>',
			'close'   => '</p>',
			'content' => genesis_strip_p_tags( $header_meta ),
			'context' => 'entry-meta-before-content',
			'params'  => [
				'args'  => $args,
				'entry' => $entry,
			],
		]
	);

}

/**
 * Display the footer meta.
 *
 * Initially based off genesis_post_meta().
 */
function mai_do_entry_footer_meta( $entry, $args ) {

	$footer_meta = '';

	// Footer meta.
	switch ( $args['type'] ) {
		case 'post':
			// Not a block.
			if ( 'block' !== $args['block'] ) {
				if ( post_type_supports( get_post_type(), 'genesis-entry-meta-after-content' ) ) {
					// TODO: Once other post types use our settings we'll need to account for it here.
					$footer_meta = genesis_get_option( 'entry_meta_after_content' );
					$footer_meta = apply_filters( 'genesis_post_info', $footer_meta );
					$footer_meta = wp_kses_post( $footer_meta );
					$footer_meta = trim( $footer_meta );
				}
			}
			// A block.
			else {
				$footer_meta = $args['footer_meta'];
			}
		break;
		case 'term':
			$footer_meta = ''; // TODO.
		break;
		case 'user':
			$footer_meta = ''; // TODO.
		break;
		default:
			$footer_meta = '';
	}

	// Bail if none.
	if ( ! $footer_meta ) {
		return;
	}

	// Run shortcodes.
	$footer_meta = do_shortcode( $footer_meta );

	genesis_markup(
		[
			'open'    => '<p %s>',
			'close'   => '</p>',
			'content' => genesis_strip_p_tags( $footer_meta ),
			'context' => 'entry-meta-after-content',
			'params'  => [
				'args'  => $args,
				'entry' => $entry,
			],
		]
	);

}

function mai_do_entry_more_link( $entry, $args ) {

	// Link.
	switch ( $args['type'] ) {
		case 'post':
			$more_link = get_the_permalink( $entry );
		break;
		case 'term':
			$more_link = ''; // TODO.
		break;
		case 'user':
			$more_link = ''; // TODO.
		break;
		default:
			$more_link = '';
	}

	// Bail if no link.
	if ( ! $more_link ) {
		return;
	}

	genesis_markup(
		[
			'open'    => '<a %s>',
			'close'   => '</a>',
			'content' => esc_html( __( 'Read More', 'mai-engine' ) ),
			'context' => 'entry-read-more',
			'atts'    => [
				'href' => $more_link,
			],
			'params'  => [
				'args'  => $args,
				'entry' => $entry,
			],
		]
	);
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
