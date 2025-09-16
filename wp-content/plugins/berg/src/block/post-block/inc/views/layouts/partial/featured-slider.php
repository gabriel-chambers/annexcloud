<div class="row">
	<div class="col-12">
		<div class="bs-slick-slider__container">
			<div class="bs-slick-slider__container__slider featured__slider" data-slick='<?php echo  json_encode($featuredSliderSettings); ?>'>
				<?php if (isset($_fea_the_query)) :
					if ($_fea_the_query->have_posts()) :
						$inc = 1;
						$_display_order = $display_order;
						$display_order = $featured_display_order;
						$is_featured = true;
						while ($_fea_the_query->have_posts()) {
							$_fea_the_query->the_post();
							include 'post-variables.php';
							$_fea_class = "col-md-12 bs-posts__featured-image-large";
							$_classes = array();
							array_push(
								$_classes,
								"bs-post",
								$_grid_class,
								$postType,
								join(' ', array_filter($term_list_slug)),
								$_fea_class
							);
							?>
							<div class="<?php echo join(' ', array_filter($_classes)); ?>">
								<?php include 'layout.php' ?>
								<?php include 'layout-popup.php' ?>
								<?php //include 'gated-form.php' ?>
							</div>
							<?php $inc++;
						}
						$is_featured = false;
						$display_order = $_display_order;
					endif;
				endif; ?>

			</div>
		</div>
	</div>
</div>
