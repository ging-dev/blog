<?php

/**
 * Fires when enqueuing scripts for all admin pages.
 *
 * @param string $hook_suffix The current admin page.
 */
add_action(
	'admin_enqueue_scripts',
	function ( $hook_suffix ): void {
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) ) {
			return;
		}

		vite_enqueue( array( 'resources/editor.ts' ) );
	},
);

add_filter( 'user_can_richedit', '__return_false' );
add_filter( 'use_block_editor_for_post', '__return_false' );

/**
 * Filters the Quicktags settings.
 *
 * @param array  $qt_init   Quicktags settings.
 * @return array Quicktags settings.
 */
add_filter(
	'quicktags_settings',
	function ( $qt_init ) {
		$qt_init['buttons'] = 'dfw';
		return $qt_init;
	},
	10
);


/**
 * Filters the HTML markup output that displays the editor.
 *
 * @param string $output Editor's HTML markup.
 * @return string Editor's HTML markup.
 */
add_filter(
	'the_editor',
	function ( $output ): string {
		$parts = explode( '</textarea>', $output );
		return implode( '</textarea><div id="editor-container"></div>', $parts );
	}
);
