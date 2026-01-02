<?php

add_action(
	'wp_enqueue_scripts',
	function (): void {
		vite_enqueue( array( 'style.css', 'resources/app.ts' ) );
		wp_enqueue_style( 'fa-icon', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css' );
		wp_enqueue_script( 'highlight', 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js' );
	}
);

/**
 * Fires after the theme is loaded.
 */
add_action(
	'after_setup_theme',
	function (): void {
		add_theme_support( 'title-tag' );
	}
);
