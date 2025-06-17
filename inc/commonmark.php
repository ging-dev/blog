<?php

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

class CommonMarkSingleton {
	public static function getEnvironment(): Environment {
		static $environment = null;
		if ( null === $environment ) {
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
			$environment->addExtension( new CommonMarkCoreExtension() );
			$environment->addExtension( new GithubFlavoredMarkdownExtension() );
			$environment->addExtension( new DefaultAttributesExtension() );
			$environment->addExtension( new AutolinkExtension() );
			$environment->addExtension( new AttributesExtension() );
			$environment->addExtension( new FootnoteExtension() );

			$environment->addRenderer(
				FencedCode::class,
				new class() implements NodeRendererInterface {
					/**
					 * @see https://stackoverflow.design/product/components/code-blocks/
					 */
					public function render( Node $node, ChildNodeRendererInterface $childRenderer ): \Stringable {
						assert( $node instanceof FencedCode );
						return new HtmlElement(
							'pre',
							array( 'class' => 's-code-block' ),
							new HtmlElement( 'code', $node->data->get( 'attributes' ), Xml::escape( $node->getLiteral() ) )
						);
					}
				}
			);

			$environment->addRenderer(
				Table::class,
				new class() implements NodeRendererInterface {
					/**
					 * @see https://stackoverflow.design/product/components/tables/
					 */
					public function render( Node $node, ChildNodeRendererInterface $childRenderer ): \Stringable {
						$attrs          = $node->data->get( 'attributes' );
						$attrs['class'] = 's-table';
						$separator      = $childRenderer->getInnerSeparator();
						$children       = $childRenderer->renderNodes( $node->children() );

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
