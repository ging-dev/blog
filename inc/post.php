<?php
/**
 * Post content filters
 *
 * Configures post content rendering using CommonMark for Markdown conversion.
 *
 * @package    Ging_Blog
 * @subpackage Ging_Blog/Inc
 * @since      1.0.0
 */
use League\CommonMark\MarkdownConverter;

// Remove all default content filters to use custom Markdown rendering.
remove_all_filters( 'the_content' );

/**
 * Filters the post content to convert Markdown to HTML.
 *
 * Uses CommonMark with GitHub Flavored Markdown extensions to render
 * post content. The converter is initialized once and reused via closure.
 *
 * @since 1.0.0
 *
 * @param string $content Content of the current post.
 *
 * @return string Converted HTML content.
 */
add_filter(
	'the_content',
	function ( $content ): string {
		static $markdown_converter = null;
		if ( null === $markdown_converter ) {
			$markdown_converter = new MarkdownConverter( CommonMarkSingleton::getEnvironment() );
		}
		return $markdown_converter->convert( $content );
	},
);
