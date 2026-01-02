<?php
/**
 * CommonMark environment singleton
 *
 * Provides a configured CommonMark environment with GitHub Flavored Markdown
 * and custom renderers for code blocks and tables.
 *
 * @package    Ging_Blog
 * @subpackage Ging_Blog/Inc
 * @since      1.0.0
 */

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\Xml;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;

/**
 * CommonMark environment singleton.
 *
 * Provides a pre-configured CommonMark environment with:
 * - GitHub Flavored Markdown support
 * - Attributes extension
 * - Autolink extension
 * - Footnotes extension
 * - Custom renderers for code blocks and tables
 *
 * @since 1.0.0
 */
class CommonMarkSingleton {
	/**
	 * Get the CommonMark environment instance.
	 *
	 * Returns a singleton instance of the CommonMark environment configured
	 * with all necessary extensions and custom renderers.
	 *
	 * @since 1.0.0
	 *
	 * @return Environment Configured CommonMark environment.
	 */
	public static function getEnvironment(): Environment {
		static $environment = null;
		if ( null === $environment ) {
			// Initialize environment with configuration.
			$environment = new Environment(
				array(
					'default_attributes' => array(),
					'table'              => array(
						'alignment_attributes' => array(
							'left'   => array( 'style' => 'text-align:left' ),
							'center' => array( 'style' => 'text-align:center' ),
							'right'  => array( 'style' => 'text-align:right' ),
						),
					),
				)
			);

			// Add CommonMark extensions.
			$environment->addExtension( new CommonMarkCoreExtension() );
			$environment->addExtension( new GithubFlavoredMarkdownExtension() );
			$environment->addExtension( new DefaultAttributesExtension() );
			$environment->addExtension( new AutolinkExtension() );
			$environment->addExtension( new AttributesExtension() );
			$environment->addExtension( new FootnoteExtension() );

			// Custom renderer for fenced code blocks using Stack Overflow design.
			$environment->addRenderer(
				FencedCode::class,
				new class() implements NodeRendererInterface {
					/**
					 * Render fenced code block.
					 *
					 * @since 1.0.0
					 * @see https://stackoverflow.design/product/components/code-blocks/
					 *
					 * @param Node                       $node            The node to render.
					 * @param ChildNodeRendererInterface $child_renderer Child node renderer.
					 *
					 * @return \Stringable Rendered HTML element.
					 */
					public function render( Node $node, ChildNodeRendererInterface $child_renderer ): \Stringable {
						assert( $node instanceof FencedCode );
						return new HtmlElement(
							'pre',
							array( 'class' => 's-code-block' ),
							new HtmlElement( 'code', $node->data->get( 'attributes' ), Xml::escape( $node->getLiteral() ) )
						);
					}
				}
			);

			// Custom renderer for tables using Stack Overflow design.
			$environment->addRenderer(
				Table::class,
				new class() implements NodeRendererInterface {
					/**
					 * Render table with wrapper div.
					 *
					 * @since 1.0.0
					 * @see https://stackoverflow.design/product/components/tables/
					 *
					 * @param Node                       $node            The node to render.
					 * @param ChildNodeRendererInterface $child_renderer Child node renderer.
					 *
					 * @return \Stringable Rendered HTML element.
					 */
					public function render( Node $node, ChildNodeRendererInterface $child_renderer ): \Stringable {
						$attrs          = $node->data->get( 'attributes' );
						$attrs['class'] = 's-table';
						$separator      = $child_renderer->getInnerSeparator();
						$children       = $child_renderer->renderNodes( $node->children() );

						return new HtmlElement(
							'div',
							array( 'class' => 's-table-container' ),
							new HtmlElement( 'table', $attrs, $separator . \trim( $children ) . $separator )
						);
					}
				}
			);
		}

		return $environment;
	}
}
