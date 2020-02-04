<?php

printf( '<div class="mai-grid__entry">', '' );
	// Image.
	if ( $data->args['show_image'] && $data->image_id ) {
		$image_size  = ( empty( $data->image_size ) || ( 'default' == $data->image_size ) ) ? 'thumbnail' : $data->image_size;
		$image_align = 'none';
		$image_html  = wp_get_attachment_image( $data->image_id, $image_size, false, array( 'class' => 'mai-grid__image' ) );
		$image_html  = printf( '<a class="mai-grid__link mai-grid__link--image" href="%s">%s</a>', $data->link, $image_html );
	}
	// Content.
	echo '<div class="mai-grid__content">';
		// Title.
		echo $data->title ? sprintf( '<h3 class="mai-grid__title"><a class="mai-grid__link mai-grid__link--title" href="%s">%s</a></h3>', $data->link, $data->title ) : '';
		// echo $data->title ? sprintf( '<h3 class="mai-grid__title">%s</h3>', $data->title ) : '';
		// Excerpt.
		// echo $data->content ? sprintf( '<div class="mai-grid__content">%s</div>', $data->content ) : '';
		// More Link.
		// echo $data->more_link ? sprintf( '<div class="mai-grid__more"><a class="mai-grid__link mai-grid__link--more" href="%s">%s</a></div>', $data->link, $data->more_link ) : '';
	echo '</div>';
echo '</div>';
