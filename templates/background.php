<?php
printf( '<a href="%s" class="mai-grid__entry">', $data->link );

	// vd( $data->image );

	// Aspect ratio.
	echo '<div class="mai-grid__entry-inner">';

		// Content.
		echo '<div class="mai-grid__entry-content">';

			// Image.
			if ( $data->image ) {
				// printf( '<div class="mai-grid__image">%s</div>', $data->image );
				// printf( '<a class="mai-grid-image-link" href="%s">%s</a>', $data->link, $data->image );
				echo $data->image;
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
