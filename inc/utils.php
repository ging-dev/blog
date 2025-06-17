<?php

/**
 * @see https://github.com/timber/timber/blob/e974e252851af262426319aca4991fb09afbe6b1/src/DateTimeHelper.php#L80
 *
 * Returns the difference between two times in a human readable format.
 *
 * Differentiates between past and future dates.
 *
 * @param int|string $from          Base date as a timestamp or a date string.
 * @param int|string $to            Optional. Date to calculate difference to as a timestamp or
 *                                  a date string. Default current time.
 * @param string     $format_past   Optional. String to use for past dates. To be used with
 *                                  `sprintf()`. Default `%s ago`.
 * @param string     $format_future Optional. String to use for future dates. To be used with
 *                                  `sprintf()`. Default `%s from now`.
 *
 * @return string
 */
function time_ago( $from, $to = null, $format_past = null, $format_future = null ) {
	if ( null === $format_past ) {
		$format_past = \__( '%s ago' );
	}

	if ( null === $format_future ) {
		$format_future = \__( '%s from now' );
	}

	$to ??= \time();
	$to   = \is_numeric( $to )
		? new DateTimeImmutable( '@' . $to, \wp_timezone() )
		: new DateTimeImmutable( $to, \wp_timezone() );
	$from = \is_numeric( $from )
		? new DateTimeImmutable( '@' . $from, \wp_timezone() )
		: new DateTimeImmutable( $from, \wp_timezone() );

	if ( $from < $to ) {
		return \sprintf( $format_past, \human_time_diff( $from->getTimestamp(), $to->getTimestamp() ) );
	}

	return \sprintf( $format_future, \human_time_diff( $to->getTimestamp(), $from->getTimestamp() ) );
}

/**
 * @global WP_Query $wp_query
 * @global WP_Rewrite $wp_rewrite
 * @param mixed $args
 * @return string|string[]|void
 */
function custom_paginate_links( $args = '' ) {
	global $wp_query, $wp_rewrite;

	// Setting up default values based on the current URL.
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$url_parts    = explode( '?', $pagenum_link );

	// Get max pages and current page out of the current query, if available.
	$total   = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
	$current = get_query_var( 'paged' ) ? (int) get_query_var( 'paged' ) : 1;

	// Append the format placeholder to the base URL.
	$pagenum_link = trailingslashit( $url_parts[0] ) . '%_%';

	// URL base depends on permalink settings.
	$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

	$defaults = array(
		'base'               => $pagenum_link, // http://example.com/all_posts.php%_% : %_% is replaced by format (below).
		'format'             => $format, // ?page=%#% : %#% is replaced by the page number.
		'total'              => $total,
		'current'            => $current,
		'aria_current'       => 'page',
		'show_all'           => false,
		'prev_next'          => true,
		'prev_text'          => __( '&laquo; Previous' ),
		'next_text'          => __( 'Next &raquo;' ),
		'end_size'           => 1,
		'mid_size'           => 2,
		'type'               => 'plain',
		'add_args'           => array(), // Array of query args to add.
		'add_fragment'       => '',
		'before_page_number' => '',
		'after_page_number'  => '',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( ! is_array( $args['add_args'] ) ) {
		$args['add_args'] = array();
	}

	// Merge additional query vars found in the original URL into 'add_args' array.
	if ( isset( $url_parts[1] ) ) {
		// Find the format argument.
		$format       = explode( '?', str_replace( '%_%', $args['format'], $args['base'] ) );
		$format_query = isset( $format[1] ) ? $format[1] : '';
		wp_parse_str( $format_query, $format_args );

		// Find the query args of the requested URL.
		wp_parse_str( $url_parts[1], $url_query_args );

		// Remove the format argument from the array of query arguments, to avoid overwriting custom format.
		foreach ( $format_args as $format_arg => $format_arg_value ) {
			unset( $url_query_args[ $format_arg ] );
		}

		$args['add_args'] = array_merge( $args['add_args'], urlencode_deep( $url_query_args ) );
	}

	// Who knows what else people pass in $args.
	$total = (int) $args['total'];
	if ( $total < 2 ) {
		return;
	}
	$current  = (int) $args['current'];
	$end_size = (int) $args['end_size']; // Out of bounds? Make it the default.
	if ( $end_size < 1 ) {
		$end_size = 1;
	}
	$mid_size = (int) $args['mid_size'];
	if ( $mid_size < 0 ) {
		$mid_size = 2;
	}

	$add_args   = $args['add_args'];
	$r          = '';
	$page_links = array();
	$dots       = false;

	if ( $args['prev_next'] && $current && 1 < $current ) :
		$link = str_replace( '%_%', 2 === $current ? '' : $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current - 1, $link );
		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		$page_links[] = sprintf(
			'<a class="prev s-pagination--item" href="%s">%s</a>',
			/**
			 * Filters the paginated links for the given archive pages.
			 *
			 * @since 3.0.0
			 *
			 * @param string $link The paginated link URL.
			 */
			esc_url( apply_filters( 'paginate_links', $link ) ),
			$args['prev_text']
		);
	endif;

	for ( $n = 1; $n <= $total; $n++ ) :
		if ( $n === $current ) :
			$page_links[] = sprintf(
				'<span aria-current="%s" class="s-pagination--item is-selected">%s</span>',
				esc_attr( $args['aria_current'] ),
				$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
			);

			$dots = true;
		elseif ( $args['show_all'] || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size ) ) :
				$link = str_replace( '%_%', 1 === $n ? '' : $args['format'], $args['base'] );
				$link = str_replace( '%#%', $n, $link );
			if ( $add_args ) {
				$link = add_query_arg( $add_args, $link );
			}
				$link .= $args['add_fragment'];

				$page_links[] = sprintf(
					'<a class="s-pagination--item" href="%s">%s</a>',
					/** This filter is documented in wp-includes/general-template.php */
					esc_url( apply_filters( 'paginate_links', $link ) ),
					$args['before_page_number'] . number_format_i18n( $n ) . $args['after_page_number']
				);

				$dots = true;
			elseif ( $dots && ! $args['show_all'] ) :
				$page_links[] = '<span class="s-pagination--item s-pagination--item__clear">' . __( '&hellip;' ) . '</span>';

				$dots = false;
		endif;
	endfor;

	if ( $args['prev_next'] && $current && $current < $total ) :
		$link = str_replace( '%_%', $args['format'], $args['base'] );
		$link = str_replace( '%#%', $current + 1, $link );
		if ( $add_args ) {
			$link = add_query_arg( $add_args, $link );
		}
		$link .= $args['add_fragment'];

		$page_links[] = sprintf(
			'<a class="next s-pagination--item" href="%s">%s</a>',
			/** This filter is documented in wp-includes/general-template.php */
			esc_url( apply_filters( 'paginate_links', $link ) ),
			$args['next_text']
		);
	endif;

	switch ( $args['type'] ) {
		case 'array':
			return $page_links;

		case 'list':
			$r  = '<nav class="s-pagination pt8" aria-label="pagination"><ul>';
			$r .= implode(
				"\n",
				array_map(
					function ( $page_link ) {
						return '<li>' . $page_link . '</li>';
					},
					$page_links
				)
			);
			$r .= '</ul></nav>';
			break;

		default:
			$r = implode( "\n", $page_links );
			break;
	}

	/**
	 * Filters the HTML output of paginated links for archives.
	 *
	 * @since 5.7.0
	 *
	 * @param string $r    HTML output.
	 * @param array  $args An array of arguments. See paginate_links()
	 *                     for information on accepted arguments.
	 */
	$r = apply_filters( 'paginate_links_output', $r, $args );

	return $r;
}
