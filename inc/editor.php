<?php
/**
 * Editor customizations
 *
 * Configures the WordPress editor with custom JavaScript and settings,
 * disabling the block editor in favor of a custom editor implementation.
 *
 * @package    Ging_Blog
 * @subpackage Ging_Blog/Inc
 * @since      1.0.0
 */

/**
 * Enqueue editor scripts and styles.
 *
 * Loads custom editor assets only on post edit screens.
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix The current admin page.
 *
 * @return void
 */
add_action(
	'admin_enqueue_scripts',
	function ( $hook_suffix ): void {
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		vite_enqueue( array( 'resources/editor.ts' ) );
	},
);

/**
 * Disable the rich text editor.
 *
 * Forces the use of the custom editor instead of WordPress's default
 * visual editor.
 *
 * @since 1.0.0
 */
add_filter( 'user_can_richedit', '__return_false' );

/**
 * Disable the block editor (Gutenberg).
 *
 * Prevents the block editor from being used for posts, using the
 * classic editor approach instead.
 *
 * @since 1.0.0
 */
add_filter( 'use_block_editor_for_post', '__return_false' );

/**
 * Customize Quicktags settings.
 *
 * Modifies the Quicktags toolbar to show only the distraction-free
 * writing (DFW) button.
 *
 * @since 1.0.0
 *
 * @param array $qt_init Quicktags settings.
 *
 * @return array Modified Quicktags settings.
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
 * Modify the editor HTML markup.
 *
 * Injects a custom editor container div after the textarea element
 * for the custom editor implementation.
 *
 * @since 1.0.0
 *
 * @param string $output Editor's HTML markup.
 *
 * @return string Modified editor HTML markup.
 */
add_filter(
	'the_editor',
	function ( $output ): string {
		$parts = explode( '</textarea>', $output );
		return implode( '</textarea><div id="editor-container"></div>', $parts );
	}
);
