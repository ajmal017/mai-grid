<?php

static $image          = '';
static $style          = '';
static $vertical_align = '';
if ( empty( $image ) ) {
	$image = mai_grid_get_image_width_height( $data->image_size, $data->image_id );
	$style .= sprintf( '--aspect-ratio:%s/%s;', $image[0], $image[1] );
}
if ( empty( $vertical_align ) ) {
	switch ( $data->align_text_vertical ) {
		case 'top':
			$align_items = 'start';
			break;
		case 'bottom':
			$align_items = 'end';
			break;
		default:
			$align_items = 'center';
	}
	$style .= sprintf( '--align-text-vertical:%s;', $align_items );
}
$style = $style ? sprintf( ' style="%s"', $style ) : '';

printf( '<a href="%s" class="mai-grid__entry"%s>', $data->link, $style );

	// vd( $data->image );

	// Aspect ratio.
	echo '<div class="mai-grid__inner">';

		// Content.
		echo '<div class="mai-grid__entry-content">';

			// Image.
			if ( $data->image_id ) {
				// printf( '<div class="mai-grid__image">%s</div>', $data->image );
				// printf( '<a class="mai-grid-image-link" href="%s">%s</a>', $data->link, $data->image );
				$image_size  = ( empty( $data->image_size ) || ( 'default' == $data->image_size ) ) ? 'thumbnail' : $data->image_size;
				$image_align = 'none';
				echo wp_get_attachment_image( $data->image_id, $image_size, false, array( 'class' => 'mai-grid__image' ) );
			}

			// echo '<span class="mai-grid__overlay"></span>';

			// Title.
			// echo $data->title ? sprintf( '<h3 class="mai-grid__title"><a class="mai-grid-title-link" href="%s">%s</a></h3>', $data->link, $data->title ) : '';
			echo $data->title ? sprintf( '<h3 class="mai-grid__title">%s</h3>', $data->title ) : '';
			// Excerpt.
			// echo $data->content ? sprintf( '<div class="mai-grid__content">%s</div>', $data->content ) : '';
			// More Link.
			// echo $data->more_link ? sprintf( '<div class="mai-grid__more"><a class="mai-grid__link mai-grid__link--more" href="%s">%s</a></div>', $data->link, $data->more_link ) : '';

			// printf( '<a class="mai-grid__bg-link" href="%s"><span class="screen-reader-text" aria-hidden="true">%s</span></a>', $data->link, $data->title );

		echo '</div>';

	echo '</div>';

echo '</a>';
