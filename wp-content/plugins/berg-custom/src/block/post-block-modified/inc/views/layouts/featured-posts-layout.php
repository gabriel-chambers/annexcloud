<!--Featured posts grid-->
<?php if ( $_featured ) : ?>
	<div class='bs-posts__featured'>
		<?php if ( trim( $featuredText ) ) : ?>
			<div class='bs-posts__featured__title'><h3><?php echo $featuredText; ?></h3></div>
		<?php endif; ?>

		<?php if ( $featuredPostSlider ): ?>
			<?php include 'partial/featured-slider.php'; ?>
		<?php else: ?>
			<div class='bs-posts__featured-grid'>
				<div class='bs-posts__featured-row row'>
					<?php include 'partial/featured-grid.php'; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
