<?php

use League\Plates\Template\Template;

assert( $this instanceof Template );

$this->layout( 'layout' );

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<article class="s-post-summary">
			<div class="s-post-summary--content">
				<h3 class="s-post-summary--content-title">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h3>
				<p class="s-post-summary--content-excerpt"><?php the_excerpt(); ?></p>
				<div class="s-post-summary--meta">
					<div class="s-post-summary--meta-tags">
						<?php
						$tags = get_the_tags();
						if ( $tags ) :
							?>
							<?php foreach ( $tags as $tag ) : ?>
								<a class="s-tag" href="<?php echo get_term_link( $tag ); ?>"><?php echo $this->e( $tag->name ); ?></a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<div class="s-user-card s-user-card__minimal">
						<a href="#" class="s-user-card--link"><?php the_author(); ?></a>
						<time class="s-user-card--time">asked about <?php echo time_ago( get_the_date( 'U' ) ); ?></time>
					</div>
				</div>
			</div>
		</article>

		<?php
	endwhile;
	echo custom_paginate_links( array( 'type' => 'list' ) );
else :
	?>
	<div class="s-empty-state wmx4 p48">
		<p><?php _e( 'No posts found.' ); ?></p>
	</div>
<?php endif; ?>
