<?php

// Get it started.
// add_action( 'plugins_loaded', function() {
// 	new Mai_Post_Grid_Block;
// });

class Mai_Post_Grid extends Mai_Grid_Base {


	function get_query_fields() {
		return array(
			'post_type'    => 'field_5df1053632ca2',
			'number'       => 'field_5df1053632ca8',
			'offset'       => 'field_5df1bf01ea1de',
			'query_by'     => 'field_5df1053632cad',
			'post__in'     => 'field_5df1053632cbc',
			'post__not_in' => 'field_5e349237e1c01',
			'taxonomies'   => 'field_5df1397316270',
			'taxonomy'     => 'field_5df1398916271',
			'terms'        => 'field_5df139a216272',
			'relation'     => 'field_5df139281626f',
			'operator'     => 'field_5df18f2305c2c',
			'relation'     => 'field_5df139281626f',
			'parent'       => 'field_5df1053632ce4',
			'orderby'      => 'field_5df1053632cec',
			'meta_key'     => 'field_5df1053632cf4',
			'order'        => 'field_5df1053632cfb',
			'exclude'      => 'field_5df1053632d03',
			'load_more'    => 'field_5df1bbaeb25f2',
		);
	}

	/**
	 * TODO: Make a query class that does the loop stuff.
	 * Should this be the post vs term stuff?
	 */
	function do_grid_entries( $block, $content = '', $is_preview = false ) {

		$this->block = $block;

		$this->values = $this->get_values();

		$posts = new WP_Query( $this->get_query_args() );

		if ( $posts->have_posts() ):

			while ( $posts->have_posts() ) : $posts->the_post();

				// Start data array so we don't need isset() in template.
				$data = array(
					'args'                => $this->values,
					'link'                => get_permalink(),
					'image_id'            => '',
					'image_size'          => '',
					'image_align'         => '',
					'title'               => '',
					'header_meta'         => '',
					'footer_meta'         => '',
					'content'             => '',
					'more_link'           => '',
					'more_link_text'      => '',
					'align_text'          => '',
					'align_text_vertical' => '',
					'boxed'               => false,
				);

				// Image.
				if ( $this->values['show_image'] ) {
					// TODO: If 'default', find an actual image size, or maybe this is from template config?
					$image_id = get_post_thumbnail_id();
					if ( $image_id ) {
						$data['image_id']    = $image_id;
						$data['image_size']  = ( 'default' == $this->values['image_size'] ) ? 'thumbnail' : $this->values['image_size'];
						$data['image_align'] = $this->values['image_align'];
					}
				}
				// Title.
				if ( $this->values['show_title'] ) {
					$data['title'] = get_the_title();
				}
				// Header Meta.
				if ( $this->values['show_header_meta'] ) {
					$data['header_meta'] = do_shortcode( $this->values['header_meta'] );
				}
				// Excerpt.
				if ( $this->values['show_excerpt'] ) {
					$data['content'] = wpautop( get_the_excerpt() );
				}
				// Content.
				if ( $this->values['show_content'] ) {
					$data['content'] = strip_shortcodes( get_the_content() );
				}
				// Content Limit.
				if ( $this->values['content_limit'] > 0 ) {
					/**
					 * OLD WAY: Word count. Do we want this instead?
					 */
					// Reset the variable while trimming the content. wp_trim_words runs wp_strip_all_tags so we need to do this before re-processing.
					// $data['content'] = wp_trim_words( $data['content'], $this->values['content_limit'], '&hellip;' );

					/**
					 * NEW WAY: Character count. This matches Genesis content limit setting for archives, but I feel like word count makes more sense?
					 */
					$data['content'] = $this->get_the_content_limit( $data['content'], $this->values['content_limit'] );
				}
				// More Link.
				if ( $this->values['show_more_link'] ) {
					$data['more_link'] = $this->values['more_link_text'];
				}
				// Footer Meta.
				if ( $this->values['show_footer_meta'] ) {
					$data['footer_meta'] = do_shortcode( $this->values['footer_meta'] );
				}
				// Align Text.
				if ( $this->values['align_text'] ) {
					$data['align_text'] = $this->values['align_text'] ;
				}
				// Align Text Vertical
				if ( $this->values['align_text_vertical'] ) {
					$data['align_text_vertical'] = $this->values['align_text_vertical'] ;
				}
				// Boxed.
				if ( $this->values['boxed'] ) {
					$data['boxed'] = true;
				}

				/**
				 * TODO: This is not loading correctly when you add a new grid block.
				 * The grid is empty until you toggle Show things.
				 */

				// Template.
				$this->loader->set_template_data( $data );
				$this->loader->get_template_part( $this->values['template'] );

			endwhile;

		endif;
		wp_reset_postdata();

		// $template->get_template_part( 'standard' );
		// echo $this->get_grid_entries( $block );
	}

	function get_query_args() {

		$args = array(
			'post_type'           => $this->values['post_type'],
			'posts_per_page'      => $this->values['number'],
			'post_status'         => 'publish',
			'offset'              => $this->values['offset'],
			'ignore_sticky_posts' => true,
		);

		// Handle query_by.
		switch ( $this->values['query_by'] ) {
			case 'parent':
				$args['post_parent__in'] = $this->values['post_parent__in'];
			break;
			case 'title':
				// Empty array returns all posts, so we need to check for values.
				if ( $this->values['post__in'] ) {
					$args['post__in'] = $this->values['post__in'];
				}
			break;
			case 'taxonomy':
				$args['tax_query'] = array(
					'relation' => $this->values['relation'],
				);
				foreach( $this->values['taxonomies'] as $taxo ) {
					$args['tax_query'][] = array(
						'taxonomy' => $taxo['taxonomy'],
						'field'    => 'id',
						'terms'    => $taxo['terms'],
						'operator' => $taxo['operator'],
					);
				}
			break;
		}

		// Exclude entries.
		if ( ( 'title' !== $this->values['query_by'] ) && $this->values['post__not_in'] ) {
			$args['post__not_in'] = $this->values['post__not_in'];
		}

		// vd( $args );

		return apply_filters( 'mai_post_grid_args', $args, $this->block );
	}

	function get_query_values() {
		// Get the values.
		$values = array(
			// Query.
			'post_type'              => (array) $this->get_field( 'post_type', 'esc_html' ),
			'number'                 => $this->get_field( 'number', 'absint' ),
			'offset'                 => $this->get_field( 'offset', 'absint' ),
			'query_by'               => $this->get_field( 'query_by', 'esc_html' ),
			'post__in'               => (array) $this->get_field( 'post__in', 'absint' ),
			'post__not_in'           => (array) $this->get_field( 'post__not_in', 'absint' ),
			'taxonomies'             => $this->get_field( 'taxonomies', 'esc_html' ),
			'relation'               => $this->get_field( 'relation', 'esc_html' ),
			'post_parent__in'        => $this->get_field( 'post_parent__in', 'esc_html' ),
			'orderby'                => $this->get_field( 'orderby', 'esc_html' ),
			'meta_key'               => $this->get_field( 'meta_key', 'esc_html' ),
			'order'                  => $this->get_field( 'order', 'esc_html' ),
			'exclude'                => (array) $this->get_field( 'exclude', 'esc_html' ),
			// 'load_more'              => $this->get_field( 'load_more', 'esc_html' ),
		);
		return $values;
	}

		/**
	 * Get the content, limited by max character count.
	 * Most of this was taking from Genesis get_the_content_limit() function.
	 *
	 * @param   string  $content         The existing content.
	 * @param   int     $max_characters  The character limit.
	 *
	 * @return  string  The limited content.
	 */
	function get_the_content_limit( $content, $max_characters ) {

		// Strip tags and shortcodes so the content truncation count is done correctly.
		$content = strip_tags( strip_shortcodes( $content ), apply_filters( 'get_the_content_limit_allowedtags', '<script>,<style>' ) );

		// Remove inline styles / scripts.
		$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );

		// Truncate $content to $max_char.
		$content = genesis_truncate_phrase( $content, $max_characters );

		// Autop. Do we really need this?
		$output = wpautop( $content . '&hellip;' );

		return apply_filters( 'get_the_content_limit', $output, $content, $link = '', $max_characters );
	}

}
