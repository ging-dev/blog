<?php

use League\Plates\Engine;

$engine = new Engine( __DIR__ . '/templates' );

$data = array();
if ( is_front_page() ) {
	$data['title']       = get_bloginfo( 'name' );
	$data['description'] = get_bloginfo( 'description' );
} else {
	$data['breadcrumbs'][ _( 'Home' ) ] = esc_url( home_url( '/' ) );
	if ( is_search() ) {
		$data['title']       = _( 'Search results' );
		$data['description'] = 'Keywords: ' . get_search_query( false );
	} elseif ( null !== $queried_object = get_queried_object() ) {
		if ( $queried_object instanceof WP_Term ) {
			$data['title']       = ( is_category() ? _( 'Category' ) : _( 'Tag' ) ) . ': ' . $queried_object->name;
			$data['description'] = $queried_object->description;
		} elseif ( $queried_object instanceof WP_Post ) {
			$data['title'] = $queried_object->post_title;
			if ( $queried_object->post_category ) {
				$term_id   = $queried_object->post_category[0];
				$ancestors = get_ancestors( $term_id, 'category' );
				array_unshift( $ancestors, $term_id );
				foreach ( array_reverse( $ancestors ) as $ancestor ) {
					$term                               = get_term( $ancestor );
					$data['breadcrumbs'][ $term->name ] = get_term_link( $term );
				}
			}
		}
	}
}

$data = wp_parse_args(
	$data,
	array(
		'breadcrumbs' => array(),
		'description' => '',
		'title'       => _( 'Not found' ),
	)
);

$engine->addData( $data );

$render_name = match ( true ) {
	is_404() => '404',
	is_singular() => 'post',
	default => 'posts',
};

echo $engine->render( $render_name );
