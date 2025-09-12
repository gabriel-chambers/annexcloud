<?php if ( isset( $_fea_the_query ) ) :
	if ( $_fea_the_query->have_posts() ) :
		$inc = 1;
		$_display_order = $display_order;
		$display_order = $featured_display_order;
		$is_featured = true;
		while ( $_fea_the_query->have_posts() ) {
			$_fea_the_query->the_post();
			include 'post-variables.php';
			$_fea_class = '';
			if ( $featuredNum == 1 && $inc == 1 ) { // if one featured post
				$_fea_class = "col-xs-12 col-sm-12 bs-posts__featured-image-large only-featured";
			} elseif ( $featuredNum > 1 && $inc == 1 ) { // if more than one posts then first post will be large
				$_fea_class = "col-xs-12 col-sm-12 col-md-6 col-lg-8 bs-posts__featured-image-large";
			} else { // all other post boxes will be small
				$_fea_class = "col-xs-12 col-sm-12 col-md-6 col-lg-4 bs-posts__featured-image-normal";
			}
			$_classes = array();
			array_push(
					$_classes,
					"bs-post",
					$_grid_class,
					$postType,
					join( ' ', array_filter( $term_list_slug ) ),
					$_fea_class
			);
			?>
			<div class="<?php echo join( ' ', array_filter( $_classes ) ); ?>">
				<?php include 'layout.php' ?>
				<?php include 'layout-popup.php' ?>
				<?php //include 'gated-form.php' ?>
			</div>
			<?php $inc ++;
		}
		$is_featured   = false;
		$display_order = $_display_order;
	endif;
endif;
