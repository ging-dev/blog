<?php

use League\CommonMark\ConverterInterface;
use League\CommonMark\MarkdownConverter;

$markdown_converter = new MarkdownConverter( CommonMarkSingleton::getEnvironment() );

remove_all_filters( 'the_content' );

/**
 * Filters the post content.
 *
 * @global ConverterInterface $markdown_converter
 * @param string $content Content of the current post.
 * @return string Content of the current post.
 */
add_filter(
	'the_content',
	function ( $content ): string {
		global $markdown_converter;
		return $markdown_converter->convert( $content );
	},
);
