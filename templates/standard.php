<?php
// printf( '<div class="mai-grid__entry%s%s">',
// 	$data->boxed ? ' mai-grid__entry--boxed' : '',
// 	$data->more_link ? ' has-more-link' : ''
// );
echo '<div class="mai-grid__entry>';
	// Image.
	echo $data->image ? sprintf( '<a class="mai-grid__link mai-grid__link--image" href="%s">%s</a>', $data->link, $data->image ) : '';
	// Title.
	echo $data->title ? sprintf( '<h3 class="mai-grid__title"><a class="mai-grid__link mai-grid__link--title" href="%s">%s</a></h3>', $data->link, $data->title ) : '';
	// Header Meta.
	echo $data->header_meta ? sprintf( '<p class="mai-grid__header-meta entry-meta">%s</p>', $data->header_meta ) : '';
	// Content.
	echo $data->content ? sprintf( '<div class="mai-grid__content">%s</div>', $data->content ) : '';
	// More Link.
	echo $data->more_link ? sprintf( '<div class="mai-grid__more"><a class="mai-grid__link mai-grid__link--more" href="%s">%s</a></div>', $data->link, $data->more_link ) : '';
	// Footer Meta.
	echo $data->footer_meta ? sprintf( '<p class="mai-grid__footer-meta entry-meta">%s</p>', $data->footer_meta ) : '';
echo '</div>';
