<?php

// Add class to .content
add_filter( 'genesis_attr_mai-grid', function( $attributes, $context, $args ) {

	return $attributes;

	$items       = $args['params']['number'];
	$columns     = absint( $args['params']['columns'] );
	$rows        = absint( ceil( $items / $columns ) );
	$empty       = ( $columns * $rows ) - $items;

	// TODO: What if show_image is false?
	$image       = mai_grid_get_image_width_height( $args['params']['image_size'] );

	switch ( $args['params']['align_text_vertical'] ) {
		case 'top':
			$align_text_vertical = 'start';
			break;
		case 'bottom':
			$align_text_vertical = 'end';
			break;
		default:
			$align_text_vertical = 'center';
	}

	$attributes['style'] .= sprintf( '--align-text-vertical:%s;', $align_text_vertical );
	$attributes['style'] .= sprintf( '--aspect-ratio:%s/%s;', $image[0], $image[1] );
	$attributes['style'] .= sprintf( '--columns:%s;', $args['params']['columns'] );
	$attributes['style'] .= sprintf( '--column-gap:%s;', $args['params']['column_gap'] );
	$attributes['style'] .= sprintf( '--row-gap:%s;', $args['params']['row_gap'] );
	$attributes['style'] .= sprintf( '--empty:%s;', $empty );
	$attributes['style'] .= sprintf( '--align-text:%s;', $args['params']['align_text'] );

	if ( $args['param']['show_title'] ) {

	}

	return $attributes;

}, 10, 3 );
