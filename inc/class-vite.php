<?php
/**
 * Vite integration for WordPress themes
 *
 * Handles Vite asset loading with support for both development (hot reload)
 * and production (manifest-based) modes.
 *
 * @package    Ging_Blog
 * @subpackage Ging_Blog/Inc
 * @since      1.0.0
 * @see        https://vitejs.dev/guide/backend-integration
 */

/**
 * Vite asset manager class.
 *
 * Integrates Vite build tool with WordPress, supporting both development
 * hot module replacement and production manifest-based asset loading.
 *
 * @since 1.0.0
 *
 * @phpstan-type ManifestChunk array{
 *     src?: string,
 *     file: string,
 *     css?: list<string>,
 *     assets?: list<string>,
 *     isEntry?: bool,
 *     name?: string,
 *     isDynamicEntry?: bool,
 *     imports?: list<string>,
 *     dynamicImports?: list<string>
 * }
 *
 * @phpstan-type Manifest array<string, ManifestChunk>
 */
class Vite {
	/**
	 * Base URI for Vite assets.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $base_uri;

	/**
	 * Whether Vite dev server is running.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public bool $is_running_hot;

	/**
	 * Vite manifest data.
	 *
	 * @since 1.0.0
	 * @var Manifest
	 */
	private array $manifest = array();

	/**
	 * Initialize Vite integration.
	 *
	 * Detects whether Vite dev server is running and loads the appropriate
	 * configuration (hot reload or production manifest).
	 *
	 * @since 1.0.0
	 *
	 * @param string $build_dir Build directory name. Default 'build'.
	 *
	 * @throws RuntimeException If manifest file doesn't exist in production mode.
	 */
	public function __construct( string $build_dir = 'build' ) {
		$hot_file             = get_template_directory() . '/hot';
		$this->is_running_hot = file_exists( $hot_file );

		if ( $this->is_running_hot ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading local file.
			$this->base_uri = rtrim( file_get_contents( $hot_file ) );
		} else {
			$this->base_uri = get_template_directory_uri() . "/{$build_dir}";
		}

		if ( $this->is_running_hot ) {
			wp_enqueue_script_module( '@vite/client', "{$this->base_uri}/@vite/client", array(), null );

				return;
		}

		$manifest_file = get_template_directory() . "/{$build_dir}/manifest.json";
		if ( ! file_exists( $manifest_file ) ) {
			throw new RuntimeException(
				/* translators: %s: Manifest file path */
				sprintf( esc_html__( 'File "%s" does not exist.', 'blog-theme' ), esc_html( $manifest_file ) )
			);
		}
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading local file.
		$this->manifest = json_decode( file_get_contents( $manifest_file ), true );
	}

	/**
	 * Enqueue a Vite entry point.
	 *
	 * @since 1.0.0
	 *
	 * @param string $entry Entry point path (e.g., 'resources/app.ts').
	 *
	 * @return void
	 */
	public function enqueue( string $entry ): void {
		$this->resolve( $entry );
	}

	/**
	 * Resolve and enqueue a Vite asset with its dependencies.
	 *
	 * Recursively resolves CSS files, imports, and dynamic imports,
	 * enqueueing them in the correct order.
	 *
	 * @since 1.0.0
	 *
	 * @param string $entry Entry point or asset path.
	 * @param bool   $is_css  Whether this is a CSS file. Default false.
	 *
	 * @return string Asset handle.
	 *
	 * @throws RuntimeException If entry doesn't exist in manifest.
	 */
	private function resolve( string $entry, bool $is_css = false ): string {
		/** @var ManifestChunk */
		$chunk = ( $this->is_running_hot || $is_css ) ? array(
			'file'    => $entry,
			'isEntry' => true,
		) : $this->manifest[ $entry ] ?? throw new RuntimeException(
			/* translators: %s: Entry point path */
			sprintf( esc_html__( 'Entry "%s" does not exist.', 'blog-theme' ), esc_html( $entry ) )
		);

		// Enqueue CSS dependencies.
		foreach ( $chunk['css'] ?? array() as $css ) {
			$this->resolve( $css, true );
		}

		// Build dependency array for script modules.
		$deps = array();
		foreach ( $chunk['imports'] ?? array() as $import ) {
			$deps[] = array(
				'id'     => $this->resolve( $import ),
				'import' => 'static',
			);
		}

		foreach ( $chunk['dynamicImports'] ?? array() as $dynamic_import ) {
			$deps[] = array(
				'id'     => $this->resolve( $dynamic_import ),
				'import' => 'dynamic',
			);
		}

		$handle = $chunk['name'] ?? pathinfo( $chunk['file'], PATHINFO_FILENAME );
		$src    = "{$this->base_uri}/{$chunk['file']}";

		if ( $this->is_stylesheet( $src ) ) {
			wp_enqueue_style( $handle, $src );
		} elseif ( $chunk['isEntry'] ?? false ) {
			wp_enqueue_script_module( $handle, $src, $deps );
		} else {
			// Mark as preload for non-entry modules.
			wp_register_script_module( $handle, $src, $deps );
		}

		return $handle;
	}

	/**
	 * Check if a file path is a stylesheet.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path File path to check.
	 *
	 * @return bool True if the path is a stylesheet, false otherwise.
	 */
	private function is_stylesheet( string $path ): bool {
		return preg_match( '/\.(css|less|sass|scss|styl|stylus|pcss|postcss)$/', $path ) === 1;
	}
}
