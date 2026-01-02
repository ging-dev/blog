<?php
/**
 * Theme functions and definitions
 *
 * Sets up the theme and loads necessary files. This file is the entry point
 * for the theme and includes all required functionality files.
 *
 * @package    Ging_Blog
 * @since      1.0.0
 */

// Load Composer dependencies using Jetpack autoloader.
require plugin_dir_path( __FILE__ ) . '/vendor/autoload_packages.php';

// Load theme classes.
require plugin_dir_path( __FILE__ ) . '/inc/class-commonmark.php';
require plugin_dir_path( __FILE__ ) . '/inc/class-vite.php';

// Load theme functionality.
require plugin_dir_path( __FILE__ ) . '/inc/editor.php';
require plugin_dir_path( __FILE__ ) . '/inc/frontend.php';
require plugin_dir_path( __FILE__ ) . '/inc/post.php';
require plugin_dir_path( __FILE__ ) . '/inc/utils.php';
