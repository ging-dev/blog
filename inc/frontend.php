<?php
/**
 * Frontend functionality
 *
 * Handles frontend asset enqueuing and theme setup.
 *
 * @package    Ging_Blog
 * @subpackage Ging_Blog/Inc
 * @since      1.0.0
 */

/**
 * Enqueue frontend scripts and styles.
 *
 * Loads theme assets including Vite-managed files and external CDN resources
 * for Font Awesome icons and Highlight.js syntax highlighting.
 *
 * @since 1.0.0
 *
 * @return void
 */
add_action(
	'wp_enqueue_scripts',
	function (): void {
		vite_enqueue( array( 'style.css', 'resources/app.ts' ) );
		wp_enqueue_style( 'fa-icon', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css' );
		wp_enqueue_script( 'highlight', 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js' );
	}
);

/**
 * Theme setup.
 *
 * Configures theme features and support after the theme is loaded.
 * Enables automatic title tag generation.
 *
 * @since 1.0.0
 *
 * @return void
 */
add_action(
	'after_setup_theme',
	function (): void {
		add_theme_support( 'title-tag' );
	}
);
