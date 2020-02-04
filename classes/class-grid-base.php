<?php

// add_action( 'genesis_before_loop', function() {
// 	vd( is_numeric( '0' ) );
// });

// Get it started.
// add_action( 'plugins_loaded', function() {
// 	new Mai_Grid_Base;
// });

/**
 * This should handle templates, sanitization, enqueing of files, etc., but nothing with ACF
 * since ACF should only be used for the block. We need a shortcode and helper function as well,
 * outside of ACF.
 */
class Mai_Grid_Base {

	protected $type;
	protected $args;
	// protected $fields;

	protected $base_url;
	protected $base_dir;
	protected $version;
	protected $suffix;
	protected $templates;
	protected $fields;
	// protected $values;

	function __construct( $type, $args ) {

		$this->type      = $type;
		$this->args      = $args;

		$this->base_url  = MAI_GRID_PLUGIN_URL . 'assets';
		$this->base_dir  = MAI_GRID_PLUGIN_DIR . 'assets';
		$this->version   = MAI_GRID_VERSION;
		$this->suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '': '.min';
		$this->templates = self::get_templates();
		$this->fields    = self::get_fields();
	}

	function get() {

		// Parse args.
		$this->args = shortcode_atts( array(
			'class'                  => '',
			// Display.
			'template'               => $this->fields['template']['default'],
			'show_image'             => $this->fields['show_image']['default'],
			'image_size'             => $this->fields['image_size']['default'],
			'image_align'            => $this->fields['image_align']['default'],
			'show_title'             => $this->fields['show_title']['default'],
			'show_header_meta'       => $this->fields['show_header_meta']['default'],
			'header_meta'            => $this->fields['header_meta']['default'],
			'show_excerpt'           => $this->fields['show_excerpt']['default'],
			'show_content'           => $this->fields['show_content']['default'],
			'content_limit'          => $this->fields['content_limit']['default'],
			'show_more_link'         => $this->fields['show_more_link']['default'],
			'more_link_text'         => $this->fields['more_link_text']['default'],
			'show_footer_meta'       => $this->fields['show_footer_meta']['default'],
			'footer_meta'            => $this->fields['footer_meta']['default'],
			'boxed'                  => $this->fields['boxed']['default'],
			'align_text'             => $this->fields['align_text']['default'],
			'align_text_vertical'    => $this->fields['align_text_vertical']['default'],
			// Layout.
			'columns_responsive'     => $this->fields['columns_responsive']['default'],
			'columns'                => $this->fields['columns']['default'],
			'columns_md'             => $this->fields['columns_md']['default'],
			'columns_sm'             => $this->fields['columns_sm']['default'],
			'columns_xs'             => $this->fields['columns_xs']['default'],
			'align_columns'          => $this->fields['align_columns']['default'],
			'align_columns_vertical' => $this->fields['align_columns_vertical']['default'],
			'column_gap'             => $this->fields['column_gap']['default'],
			'row_gap'                => $this->fields['row_gap']['default'],
			// WP_Query.
			'post_type'              => $this->fields['post_type']['default'],
			'number'                 => $this->fields['number']['default'],
			'offset'                 => $this->fields['offset']['default'],
			'query_by'               => $this->fields['query_by']['default'],
			'post__in'               => $this->fields['post__in']['default'],
			'post__not_in'           => $this->fields['post__not_in']['default'],
			'taxonomies'             => $this->fields['taxonomies']['default'],
			'taxonomy'               => $this->fields['taxonomy']['default'],
			'terms'                  => $this->fields['terms']['default'],
			'relation'               => $this->fields['relation']['default'],
			'operator'               => $this->fields['operator']['default'],
			'relation'               => $this->fields['relation']['default'],
			'post_parent__in'        => $this->fields['post_parent__in']['default'],
			'orderby'                => $this->fields['orderby']['default'],
			'meta_key'               => $this->fields['meta_key']['default'],
			'order'                  => $this->fields['order']['default'],
			'exclude'                => $this->fields['exclude']['default'],
		), $this->args, 'mai_grid' );

		// Sanitize.
		$this->args = array(
			'class'                  => $this->sanitize( $this->args['class'], 'sanitize_html_class' ),
			// Display.
			'template'               => $this->sanitize( $this->args['template'], 'esc_html' ),
			'show_image'             => $this->sanitize( $this->args['show_image'], 'esc_html' ),
			'image_size'             => $this->sanitize( $this->args['image_size'], 'esc_html' ),
			'image_align'            => $this->sanitize( $this->args['image_align'], 'esc_html' ),
			'show_title'             => $this->sanitize( $this->args['show_title'], 'esc_html' ),
			'show_header_meta'       => $this->sanitize( $this->args['show_header_meta'], 'esc_html' ),
			'header_meta'            => $this->sanitize( $this->args['header_meta'], 'esc_html' ),
			'show_excerpt'           => $this->sanitize( $this->args['show_excerpt'], 'esc_html' ),
			'show_content'           => $this->sanitize( $this->args['show_content'], 'esc_html' ),
			'content_limit'          => $this->sanitize( $this->args['content_limit'], 'esc_html' ),
			'show_more_link'         => $this->sanitize( $this->args['show_more_link'], 'esc_html' ),
			'more_link_text'         => $this->sanitize( $this->args['more_link_text'], 'esc_html' ),
			'show_footer_meta'       => $this->sanitize( $this->args['show_footer_meta'], 'esc_html' ),
			'footer_meta'            => $this->sanitize( $this->args['footer_meta'], 'esc_html' ),
			'boxed'                  => $this->sanitize( $this->args['boxed'], 'esc_html' ),
			'align_text'             => $this->sanitize( $this->args['align_text'], 'esc_html' ),
			'align_text_vertical'    => $this->sanitize( $this->args['align_text_vertical'], 'esc_html' ),
			// Layout.
			'columns_responsive'     => $this->sanitize( $this->args['columns_responsive'], 'esc_html' ),
			'columns'                => $this->sanitize( $this->args['columns'], 'esc_html' ),
			'columns_md'             => $this->sanitize( $this->args['columns_md'], 'esc_html' ),
			'columns_sm'             => $this->sanitize( $this->args['columns_sm'], 'esc_html' ),
			'columns_xs'             => $this->sanitize( $this->args['columns_xs'], 'esc_html' ),
			'align_columns'          => $this->sanitize( $this->args['align_columns'], 'esc_html' ),
			'align_columns_vertical' => $this->sanitize( $this->args['align_columns_vertical'], 'esc_html' ),
			'column_gap'             => $this->sanitize( $this->args['column_gap'], 'esc_html' ),
			'row_gap'                => $this->sanitize( $this->args['row_gap'], 'esc_html' ),
			// WP_Query.
			'post_type'              => (array) $this->sanitize( $this->args['post_type'], 'esc_html' ),
			'number'                 => $this->sanitize( $this->args['number'], 'esc_html' ),
			'offset'                 => $this->sanitize( $this->args['offset'], 'esc_html' ),
			'query_by'               => $this->sanitize( $this->args['query_by'], 'esc_html' ),
			'post__in'               => (array) $this->sanitize( $this->args['post__in'], 'esc_html' ),
			'post__not_in'           => (array) $this->sanitize( $this->args['post__not_in'], 'esc_html' ),
			'taxonomies'             => $this->sanitize( $this->args['taxonomies'], 'esc_html' ),
			'taxonomy'               => $this->sanitize( $this->args['taxonomy'], 'esc_html' ),
			'terms'                  => $this->sanitize( $this->args['terms'], 'esc_html' ),
			'relation'               => $this->sanitize( $this->args['relation'], 'esc_html' ),
			'operator'               => $this->sanitize( $this->args['operator'], 'esc_html' ),
			'relation'               => $this->sanitize( $this->args['relation'], 'esc_html' ),
			'post_parent__in'        => $this->sanitize( $this->args['post_parent__in'], 'esc_html' ),
			'orderby'                => $this->sanitize( $this->args['orderby'], 'esc_html' ),
			'meta_key'               => $this->sanitize( $this->args['meta_key'], 'esc_html' ),
			'order'                  => $this->sanitize( $this->args['order'], 'esc_html' ),
			'exclude'                => (array) $this->sanitize( $this->args['exclude'], 'esc_html' ),
		);

		$this->args = apply_filters( 'mai_grid_args', $this->args, $this->type );

		// Get the entries HTML.
		$grid = $this->get_grid_entries();

		// Bail if no entries.
		if ( ! $grid ) {
			return;
		}

		// Enqueue scripts and styles.
		$this->enqueue_assets();

		$html = genesis_markup( array(
			'open'    => '<div %s>',
			'context' => 'mai-grid',
			'params'  => $this->args,
			'atts'    => $this->get_attributes(),
			'echo'    => false,
		) );

			$html .= genesis_markup( array(
				'open'    => '<div %s>',
				'context' => 'mai-grid__wrap',
				'params'  => $this->args,
				'echo'    => false,
			) );

				$html .= $grid;

			$html .= genesis_markup( array(
				'close'   => '</div>',
				'context' => 'mai-grid__wrap',
				'echo'    => false,
			) );

		$html .= genesis_markup( array(
			'close'   => '</div>',
			'context' => 'mai-grid',
			'echo'    => false,
		) );

		return $html;
	}

	/**
	 *
	 */
	static function get_templates() {
		$templates = array(
			'standard' => array(
				'label'    => __( 'Standard', 'mai-grid' ),
				'supports' => array(
					'show_image',
					'image_size',
					'image_align',
					'show_title',
					'show_header_meta',
					'header_meta',
					'show_excerpt',
					'show_content',
					'content_limit',
					'show_more_link',
					'more_link_text',
					'show_footer_meta',
					'footer_meta',
					'boxed',
					'align_text',
				),
			),
			'background' => array(
				'label'    => __( 'Background Image', 'mai-grid' ),
				'supports' => array(
					'show_image',
					'image_size',
					'show_title',
					'align_text',
					'align_text_vertical',
				),
			),
			'compact' => array(
				'label'    => __( 'Compact', 'mai-grid' ),
				'supports' => array(
					'show_image',
					'image_align',
					'show_title',
					'boxed',
				),
			),
			// TODO: Horizontal?
		);
		// Filter so custom sites can add their own templates.
		return apply_filters( 'mai_grid_templates', $templates );
	}

	static function get_fields() {
		$fields = array_merge(
			self::get_display_fields(),
			self::get_layout_fields(),
			self::get_wp_query_fields(),
			self::get_wp_term_query_fields(),
		);
		return apply_filters( 'mai_grid_fields', $fields );
	}

	static function get_display_fields() {
		return array(
			'template' => array(
				'default' => 'standard',
				'key'     => 'field_5de9b96fb69b0',
			),
			'show_image' => array(
				'default' => true,
				'key'     => 'field_5e1e665ffc7e5',
			),
			'image_size' => array(
				'default' => 'default',
				'key'     => 'field_5bd50e580d1e9',
			),
			'image_align' => array(
				'default' => '',
				'key'     => 'field_5e2f3adf82130',
			),
			'show_title' => array(
				'default' => true,
				'key'     => 'field_5e1e6693fc7e6',
			),
			'show_header_meta' => array(
				'default' => '',
				'key'     => 'field_5e1e680ce988d',
			),
			'header_meta' => array(
				'default' => '',
				'key'     => 'field_5e2b563a7c6cf',
			),
			'show_excerpt' => array(
				'default' => '',
				'key'     => 'field_5e1e67e7e988b',
			),
			'show_content' => array(
				'default' => '',
				'key'     => 'field_5e1e67fce988c',
			),
			'content_limit' => array(
				'default' => '',
				'key'     => 'field_5bd51ac107244',
			),
			'show_more_link' => array(
				'default' => '',
				'key'     => 'field_5e1e6843e988f',
			),
			'more_link_text' => array(
				// TODO: Filter on this, even though we have it elsewhere?
				'default' => __( 'Read More', 'mai-grid' ),
				'key'     => 'field_5c85465018395',
			),
			'show_footer_meta' => array(
				'default' => '',
				'key'     => 'field_5e1e6835e988e',
			),
			'footer_meta' => array(
				'default' => '',
				'key'     => 'field_5e2b567e7c6d0',
			),
			'boxed' => array(
				'default' => '',
				'key'     => 'field_5e2a08a182c2c',
			),
			'align_text' => array(
				'default' => '',
				'key'     => 'field_5c853f84eacd6',
			),
			'align_text_vertical' => array(
				'default' => '',
				'key'     => 'field_5e2f519edc912',
			),
		);
	}

	static function get_layout_fields() {
		return array(
			'columns_responsive' => array(
				'default' => '',
				'key'     => 'field_5e334124b905d',
			),
			'columns' => array(
				'default' => 3,
				'key'     => 'field_5c854069d358c',
			),
			'columns_md' => array(
				'default' => '',
				'key'     => 'field_5e3305dff9d8b',
			),
			'columns_sm' => array(
				'default' => '',
				'key'     => 'field_5e3305f1f9d8c',
			),
			'columns_xs' => array(
				'default' => '',
				'key'     => 'field_5e332a5f7fe08',
			),
			'align_columns' => array(
				'default' => '',
				'key'     => 'field_5c853e6672972',
			),
			'align_columns_vertical' => array(
				'default' => '',
				'key'     => 'field_5e31d5f0e2867',
			),
			'column_gap' => array(
				'default' => '24px',
				'key'     => 'field_5c8542d6a67c5',
			),
			'row_gap' => array(
				'default' => '24px',
				'key'     => 'field_5e29f1785bcb6',
			),
		);
	}

	static function get_wp_query_fields() {
		return array(
			'post_type' => array(
				'default' => array( 'post' ),
				'key'     => 'field_5df1053632ca2',
			),
			'number' => array(
				'default' => '12',
				'key'     => 'field_5df1053632ca8',
			),
			'offset' => array(
				'default' => '',
				'key'     => 'field_5df1bf01ea1de',
			),
			'query_by' => array(
				'default' => 'date',
				'key'     => 'field_5df1053632cad',
			),
			'post__in' => array(
				'default' => '',
				'key'     => 'field_5df1053632cbc',
			),
			'post__not_in' => array(
				'default' => '',
				'key'     => 'field_5e349237e1c01',
			),
			'taxonomies' => array(
				'default' => '',
				'key'     => 'field_5df1397316270',
			),
			'taxonomy' => array(
				'default' => '',
				'key'     => 'field_5df1398916271',
			),
			'terms' => array(
				'default' => '',
				'key'     => 'field_5df139a216272',
			),
			'relation' => array(
				'default' => '',
				'key'     => 'field_5df139281626f',
			),
			'operator' => array(
				'default' => 'IN',
				'key'     => 'field_5df18f2305c2c',
			),
			'relation' => array(
				'default' => '',
				'key'     => 'field_5df139281626f',
			),
			'post_parent__in' => array(
				'default' => '',
				'key'     => 'field_5df1053632ce4',
			),
			'orderby' => array(
				'default' => 'date',
				'key'     => 'field_5df1053632cec',
			),
			'meta_key' => array(
				'default' => '',
				'key'     => 'field_5df1053632cf4',
			),
			'order' => array(
				'default' => '',
				'key'     => 'field_5df1053632cfb',
			),
			'exclude' => array(
				'default' => '',
				'key'     => 'field_5df1053632d03',
			),
		);
	}

	static function get_wp_term_query_fields() {
		return array(

		);
	}


	function get_grid_entries() {

		/**
		 * Start data array so we don't need isset() in template.
		 * This is the data getting passed to the template.
		 */
		$data = array(
			'args'                => $this->args,
			'link'                => '',
			'image_id'            => '',
			'image_size'          => '',
			'image_align'         => '',
			'title'               => '',
			'header_meta'         => '',
			'footer_meta'         => '',
			'content'             => '',
			'more_link'           => $this->args['show_more_link'],
			'align_text'          => $this->args['align_text'],
			'align_text_vertical' => $this->args['align_text_vertical'],
			'boxed'               => $this->args['boxed'],
		);

		$html = '';

		// Get the template loader.
		$loader = new Mai_Grid_Template_Loader;

		switch ( $this->type ) {
			case 'post':
				$posts = new WP_Query( $this->get_post_query_args() );
				if ( $posts->have_posts() ):
					while ( $posts->have_posts() ) : $posts->the_post();
						// Link.
						$data['link'] = get_permalink();
						// Image.
						if ( $this->args['show_image'] ) {
							// TODO: If 'default', find an actual image size, or maybe this is from template config?
							$image_id = get_post_thumbnail_id();
							if ( $image_id ) {
								$data['image_id']    = $image_id;
								$data['image_size']  = ( 'default' == $this->args['image_size'] ) ? 'thumbnail' : $this->args['image_size'];
								$data['image_align'] = $this->args['image_align'];
							}
						}
						// Title.
						if ( $this->args['show_title'] ) {
							$data['title'] = get_the_title();
						}
						// Header Meta.
						if ( $this->args['show_header_meta'] ) {
							$data['header_meta'] = do_shortcode( $this->args['header_meta'] );
						}
						// Excerpt.
						if ( $this->args['show_excerpt'] ) {
							$data['content'] = wpautop( get_the_excerpt() );
						}
						// Content.
						if ( $this->args['show_content'] ) {
							$data['content'] = strip_shortcodes( get_the_content() );
						}
						// Content Limit.
						if ( $this->args['content_limit'] > 0 ) {
							$data['content'] = $this->get_the_content_limit( $data['content'], $this->args['content_limit'] );
						}
						// Footer Meta.
						if ( $this->args['show_footer_meta'] ) {
							$data['footer_meta'] = do_shortcode( $this->args['footer_meta'] );
						}

						/**
						 * TODO: This is not loading correctly when you add a new grid block.
						 * The grid is empty until you toggle Show things.
						 */

						// Template.
						ob_start();
						$loader->set_template_data( $data );
						$loader->get_template_part( $this->args['template'] );
						$html .= ob_get_clean();
					endwhile;
				endif;
				wp_reset_postdata();
			break;
			case 'term':
			break;
		}

		return $html;
	}

	function get_post_query_args() {

		$query_args = array(
			'post_type'           => $this->args['post_type'],
			'posts_per_page'      => $this->args['number'],
			'post_status'         => 'publish',
			'offset'              => $this->args['offset'],
			'ignore_sticky_posts' => true,
		);

		// Handle query_by.
		switch ( $this->args['query_by'] ) {
			case 'parent':
				$query_args['post_parent__in'] = $this->args['post_parent__in'];
			break;
			case 'title':
				// Empty array returns all posts, so we need to check for values.
				if ( $this->args['post__in'] ) {
					$query_args['post__in'] = $this->args['post__in'];
				}
			break;
			case 'taxonomy':
				$query_args['tax_query'] = array(
					'relation' => $this->args['relation'],
				);
				foreach( $this->args['taxonomies'] as $taxo ) {
					$query_args['tax_query'][] = array(
						'taxonomy' => $taxo['taxonomy'],
						'field'    => 'id',
						'terms'    => $taxo['terms'],
						'operator' => $taxo['operator'],
					);
				}
			break;
		}

		// Exclude entries.
		if ( ( 'title' !== $this->args['query_by'] ) && $this->args['post__not_in'] ) {
			$query_args['post__not_in'] = $this->args['post__not_in'];
		}

		// vd( $args );

		return apply_filters( 'mai_post_grid_query_args', $query_args );
	}

	function get_attributes() {
		// Start the attributes.
		$attributes = array(
			'class' => sprintf( 'mai-grid mai-grid-%s', sanitize_html_class( $this->args['template'] ) ),
			'style' => '',
		);
		// Global styles.
		$attributes['style'] .= sprintf( '--columns:%s;', $this->args['columns'] );
		$attributes['style'] .= sprintf( '--columns-md:%s;', $this->get_responsive_columns( $this->args['columns_md'], $this->args['columns'], $this->args['columns'] ) );
		$attributes['style'] .= sprintf( '--columns-sm:%s;', $this->get_responsive_columns( $this->args['columns_sm'], $this->args['columns_md'], $this->args['columns'] ) );
		$attributes['style'] .= sprintf( '--columns-xs:%s;', $this->get_responsive_columns( $this->args['columns_xs'], $this->args['columns_sm'], $this->args['columns'] ) );
		$attributes['style'] .= sprintf( '--column-gap:%s;', $this->args['column_gap'] );
		$attributes['style'] .= sprintf( '--row-gap:%s;', $this->args['row_gap'] );
		$attributes['style'] .= sprintf( '--align-columns:%s;', $this->args['align_columns'] );
		$attributes['style'] .= sprintf( '--align-columns-vertical:%s;', $this->args['align_columns_vertical'] );
		// Template based classes.
		if ( $this->template_supports( $this->args['template'], 'boxed' ) && $this->args['boxed'] ) {
			$attributes['class'] .= ' has-boxed';
		}
		if ( $this->template_supports( $this->args['template'], 'show_image' ) && $this->template_supports( $this->args['template'], 'image_align' ) && $this->args['show_image'] && $this->args['image_align'] ) {
			$attributes['class'] .= ' has-image-align-' . $this->args['image_align'];
		}
		// Template based styles.
		if ( $this->template_supports( $this->args['template'], 'align_text' ) ) {
			$attributes['style'] .= sprintf( '--align-text:%s;', $this->args['align_text'] );
		}
		if ( $this->template_supports( $this->args['template'], 'align_text_vertical' ) ) {
			$attributes['style'] .= sprintf( '--align-text-vertical:%s;', $this->args['align_text_vertical'] );
		}
		if ( $this->template_supports( $this->args['template'], 'align_text_vertical' ) ) {
			$attributes['style'] .= sprintf( '--aspect-ratio:%s;', $this->args['show_image'] ? $this->get_aspect_ratio( $this->args['image_size'] ) : '4/3' );
		}
		// Send it.
		return $attributes;
	}

	/**
	 *
	 * TODO: Will this break if more than one grid on a page?
	 */
	function get_responsive_columns( $value, $previous_value, $original_value ) {
		static $current_columns;
		// Set the current column.
		if ( ! isset( $current_columns ) ) {
			$current_columns = $original_value;
		}
		// If using responsive settings, and have a value.
		if ( $this->args['columns_responsive'] && is_numeric( $value ) ) {
			$current_columns = $value;
			return $current_columns;
		}
		$compare = is_numeric( $previous_value ) ? $previous_value : $current_columns;
		switch ( $compare ) {
			case 6:
				$current_columns = 4;
			break;
			case 5:
				$current_columns = 3;
			break;
			case 4:
				$current_columns = 2;
			break;
			case 3:
				$current_columns = 2;
			break;
			case 2:
				$current_columns = 1;
			break;
			case 1:
				$current_columns = 1;
			break;
			case 0:
				$current_columns = 0;
			break;
		}
		return absint( $current_columns );
	}

	function get_aspect_ratio( $image_size ) {
		$sizes = $this->get_image_sizes( $image_size );
		return sprintf( '%s/%s', $sizes[0], $sizes[1] );
	}

	/**
	 * Get an image width and height.
	 *
	 * @return  array  An array with [0] being width and [1] being height.
	 */
	function get_image_sizes( $image_size ) {
		global $_wp_additional_image_sizes;
		// Get width/height from global image sizes.
		if ( isset( $_wp_additional_image_sizes[ $image_size ] ) ) {
			$registered_image = $_wp_additional_image_sizes[ $image_size ];
			$width  = $registered_image['width'];
			$height = $registered_image['height'];
		}
		// Fallback.
		else {
			$width  = 4;
			$height = 3;
		}
		return array( $width, $height );
	}

	function get_flex_align( $value ) {
		switch ( $value ) {
			case 'start':
			case 'top':
				$return = 'flex-start';
				break;
			case 'center':
			case 'middle':
				$return = 'center';
				break;
			case 'right':
			case 'bottom':
				$return = 'flex-end';
				break;
			default:
				$return = 'unset';
		}
		return $return;
	}

	/**
	 * Get the gap value.
	 * If only a number value, force to pixels.
	 */
	function get_gap( $value ) {
		if ( empty( $value ) || is_numeric( $value ) ) {
			return sprintf( '%spx', intval( $value ) );
		}
		return trim( $value );
	}

	function template_supports( $template, $value ) {
		return isset( $this->templates[ $template ]['supports'] ) && in_array( $value, $this->templates[ $template ]['supports'] );
	}

	/**
	 * Sanitize a value. Checks for null/array.
	 *
	 * @param   string  $value       The value to sanitize.
	 * @param   string  $function    The function to use for escaping.
	 * @param   bool    $allow_null  Wether to return or escape if the value is.
	 *
	 * @return  mixed
	 */
	function sanitize( $value, $function = 'esc_html', $allow_null = false ) {

		// Return null if allowing null.
		if ( is_null( $value ) && $allow_null ) {
			return $value;
		}

		// If array, escape and return it.
		if ( is_array( $value ) ) {
			$escaped = array();
			foreach( $value as $index => $item ) {
				$item = trim( $item );
				$escaped[ $index ] = $function( $item );
			}
			return $escaped;
		}

		// Return single value.
		$value   = trim( $value );
		$escaped = $function( $value );
		return $escaped;
	}

	function enqueue_assets() {

		// Default layout CSS.
		$this->enqueue_asset( 'entries', 'css' );

		if ( is_admin() ) {

			// Default admin CSS.
			$this->enqueue_asset( 'admin', 'css' );

			// We can't dynamically load assets via ajax when the template select field changes, so we need them all available in the backend.
			foreach( array_keys( $this->templates ) as $template ) {
				$this->enqueue_asset( $template, 'css' );
			}

			// Query JS.
			switch ( $this->type ) {
				case 'post':
					$this->enqueue_asset( 'wp-query', 'js' );
				break;
				case 'term':
				break;
			}

		} else {

			// Only load the template CSS file we need.
			$this->enqueue_asset( $this->args['template'], 'css' );
		}
	}

	/**
	 * Enqueue an asset.
	 *
	 * @param   string  $name  The asset name.
	 * @param   string  $type  The type. Typically js or css.
	 *
	 * @return  void
	 */
	function enqueue_asset( $name, $type ) {
		if ( ! file_exists( "{$this->base_dir}/{$type}/{$name}{$this->suffix}.{$type}" ) ) {
			return;
		}
		$url     = sprintf( '%s/%s/%s%s.%s', $this->base_url, $type, $name, $this->suffix, $type );
		$version = $this->version . '.' . date ( 'njYHi', filemtime( "{$this->base_dir}/{$type}/{$name}{$this->suffix}.{$type}" ) );
		switch ( $type ) {
			case 'css':
				wp_enqueue_style( "mai-grid-{$name}", $url, array(), $version );
				break;
			case 'js':
				wp_enqueue_script( "mai-grid-{$name}", $url, array(), $version, true );
				break;
		}
	}

}
