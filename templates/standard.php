<?php

echo '<div class="mai-grid__entry">';
	// Image.
	if ( $data->args['show_image'] && $data->image_id ) {
		$image_size  = ( empty( $data->image_size ) || ( 'default' == $data->image_size ) ) ? 'thumbnail' : $data->image_size;
		$image_align = $data->image_align ? sprintf( 'align%s', $data->image_align ) : 'alignnone';
		$image_class = sprintf( 'mai-grid__image %s', $image_align );
		$image_html  = wp_get_attachment_image( $data->image_id, $image_size, false, array( 'class' => $image_class ) );
		printf( '<a class="mai-grid__link mai-grid__link--image has-%s" href="%s">%s</a>', $image_align, $data->link, $image_html );
	}

	// if ( $data->args['show_image'] && $data->image ) {
	// 	$image_align = $data->image_align ? sprintf( 'align%s', $data->image_align ) : 'alignnone';
	// 	printf( '<a class="mai-grid__link mai-grid__link--image has-%s" href="%s">%s</a>', $image_align, $data->link, $data->image );
	// }

	// Title.
	echo ( $data->args['show_title'] && $data->title ) ? sprintf( '<h3 class="mai-grid__title"><a class="mai-grid__link mai-grid__link--title" href="%s">%s</a></h3>', $data->link, $data->title ) : '';

	// TODO: Check show_%s parameters here.

	// Header Meta.
	echo $data->header_meta ? sprintf( '<p class="mai-grid__header-meta entry-meta">%s</p>', $data->header_meta ) : '';
	// Content.
	echo $data->content ? sprintf( '<div class="mai-grid__content">%s</div>', $data->content ) : '';
	// More Link.
	echo $data->more_link ? sprintf( '<div class="mai-grid__more"><a class="mai-grid__link mai-grid__link--more" href="%s">%s</a></div>', $data->link, $data->more_link ) : '';
	// Footer Meta.
	echo $data->footer_meta ? sprintf( '<p class="mai-grid__footer-meta entry-meta">%s</p>', $data->footer_meta ) : '';
echo '</div>';
