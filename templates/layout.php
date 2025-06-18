<?php
use League\Plates\Template\Template;

assert( $this instanceof Template );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<header class="s-topbar ps-sticky tw:top-0">
		<div class="s-topbar--container">
			<a href="#" class="s-topbar--menu-btn" aria-controls="popover-example" aria-expanded="false"
				data-controller="s-popover" data-action="s-popover#toggle" data-s-popover-placement="bottom-start"
				data-s-popover-toggle-class="is-selected"><span></span></a>
			<div class="s-popover" id="popover-example" role="menu">
				<div class="s-popover--arrow"></div>
				<div class="s-popover--content">haha</div>
			</div>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="s-topbar--logo">
				<i class="fa-brands fa-linux svg-icon fa-2x"></i>
				<?php echo get_bloginfo( 'name' ); ?></a>
			<div class="s-topbar--container wmx50">
				<form id="search" class="s-topbar--searchbar" autocomplete="off" role="search"
					action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
					<div class="s-topbar--searchbar--input-group">
						<input type="text" placeholder="Search…" value="" autocomplete="off"
							class="s-input s-input__search" name="s" />
						<svg aria-hidden="true" class="svg-icon iconSearch s-input-icon s-input-icon__search" width="18"
							height="18" viewBox="0 0 18 18">
							<path
								d="m18 16.5-5.14-5.18h-.35a7 7 0 1 0-1.19 1.19v.35L16.5 18zM12 7A5 5 0 1 1 2 7a5 5 0 0 1 10 0">
							</path>
						</svg>
					</div>
				</form>
			</div>
			<nav class="s-topbar--navigation">
				<button type="button" class="s-topbar--item s-btn s-btn__muted s-btn__icon s-btn__dropdown bar0 pr24"
					aria-controls="theming-popover" data-controller="s-popover" data-action="s-popover#toggle"
					data-s-popover-toggle-class="is-selected" data-s-popover-placement="bottom-end"
					title="Select a theme" aria-expanded="false">
					<i class="fa-solid fa-circle-half-stroke fa-2x"></i>
				</button>
				<div class="s-popover w-auto wmn-initial wmx-initial" id="theming-popover" role="menu"
					data-popper-escaped="" data-popper-placement="bottom-end">
					<div class="s-popover--arrow"></div>
					<div class="s-popover--content">
						<div class="d-flex fd-column g12">
							<div class="d-flex ai-center jc-space-between g8">
								<label class="s-label fs-body1 fw-normal" for="toggle-theme-dark">Dark mode</label>
								<input data-controller="toggle-theme" data-action="change->toggle-theme#toggle"
									class="s-toggle-switch" id="toggle-theme-dark" type="checkbox">
							</div>
						</div>
					</div>
				</div>
			</nav>
		</div>
	</header>
	<main class="mx8 mt16 tw:lg:px-40">
		<div class="s-page-title">
			<div class="s-page-title--text">
				<?php if ( $breadcrumbs ) : ?>
					<nav class="s-breadcrumbs" aria-label="breadcrumbs">
						<?php
						$i = 0;
						foreach ( $breadcrumbs as $name => $link ) :
							?>
							<div class="s-breadcrumbs--item">
								<a class="s-breadcrumbs--link"
									href="<?php echo esc_url( $link ); ?>"><?php echo $this->e( $name ); ?></a>
								<?php if ( ++$i !== count( $breadcrumbs ) ) : ?>
									<svg aria-hidden="true" class="svg-icon iconArrowRightAltSm s-breadcrumbs--divider" width="13"
										height="14" viewBox="0 0 13 14">
										<path d="m4.38 4.62 1.24-1.24L9.24 7l-3.62 3.62-1.24-1.24L6.76 7z"></path>
									</svg>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>
				<h1 class="s-page-title--header"><?php echo $this->e( $title ); ?></h1>
				<?php if ( $description ) : ?>
					<p class="s-page-title--description"><?php echo $this->e( $description ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php echo $this->section( 'content' ); ?>
	</main>
	<footer class="tw:text-center my48">
		<p>Brought to you with ❤ by the ging.</p>
	</footer>
	<?php wp_footer(); ?>
	<?php if ( $this->section( 'js' ) ) : ?>
		<?php echo $this->section( 'js' ); ?>
	<?php endif; ?>
</body>

</html>
