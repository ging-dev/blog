<?php

use League\Plates\Template\Template;

assert( $this instanceof Template );

$this->layout( 'layout' );
?>
<article class="s-prose mt8"><?php the_content(); ?></article>
<section class="giscus">
</section>
<?php
$this->start( 'js' );
?>
<script src="https://giscus.app/client.js" data-repo="gingteam/giscus" data-repo-id="R_kgDOO5OLtQ"
	data-category="General" data-category-id="DIC_kwDOO5OLtc4CrS2-" data-mapping="pathname" data-strict="0"
	data-reactions-enabled="1" data-emit-metadata="0" data-input-position="bottom" data-theme="preferred_color_scheme"
	data-lang="vi" crossorigin="anonymous" async>
	</script>
<script>hljs.highlightAll();</script>
<?php $this->stop(); ?>
