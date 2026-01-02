<?php
/**
 * Main template file
 *
 * This is the main template file that handles all page requests.
 * It determines which template to render based on WordPress conditional tags
 * and prepares the necessary data for the Plates template engine.
 *
 * @package    Ging_Blog
 * @since      1.0.0
 */

use League\Plates\Engine;

// Initialize the Plates template engine.
$engine = new Engine( __DIR__ . '/templates' );

// Prepare template data based on the current page type.
$data = array();
if ( is_front_page() ) {
	$data['title']       = get_bloginfo( 'name' );
	$data['description'] = get_bloginfo( 'description' );
} else {
	$data['breadcrumbs'][ __( 'Home', 'blog-theme' ) ] = esc_url( home_url( '/' ) );
	if ( is_search() ) {
		$data['title']       = __( 'Search results', 'blog-theme' );
		$data['description'] = 'Keywords: ' . get_search_query( false );
	}
	$queried_object = get_queried_object();
	if ( null !== $queried_object ) {
		if ( $queried_object instanceof WP_Term ) {
			$data['title']       = ( is_category() ? __( 'Category', 'blog-theme' ) : __( 'Tag', 'blog-theme' ) ) . ': ' . $queried_object->name;
			$data['description'] = $queried_object->description;
		} elseif ( $queried_object instanceof WP_Post ) {
			$data['title'] = $queried_object->post_title;
			// Build breadcrumbs from post categories.
			if ( $queried_object->post_category ) {
				$term_id   = $queried_object->post_category[0];
				$ancestors = get_ancestors( $term_id, 'category' );
				array_unshift( $ancestors, $term_id );
				foreach ( array_reverse( $ancestors ) as $ancestor ) {
					$category_term                               = get_term( $ancestor );
					$data['breadcrumbs'][ $category_term->name ] = get_term_link( $category_term );
				}
			}
		}
	}
}

// Set default values for template data.
$data = wp_parse_args(
	$data,
	array(
		'breadcrumbs' => array(),
		'description' => '',
		'title'       => __( 'Not found', 'blog-theme' ),
	)
);

// Add data to the template engine.
$engine->addData( $data );

// Determine which template to render based on page type.
$render_name = match ( true ) {
	is_404() => '404',
	is_singular() => 'post',
	default => 'posts',
};

// Render the template.
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template engine output is already escaped.
echo $engine->render( $render_name );
