<?php
get_header();
$not_found_image = get_theme_mod('404_image');
$not_found_title = get_theme_mod('404_title');
$not_found_sub_title = get_theme_mod('404_sub_title');
$not_found_button_text = get_theme_mod('404_button_text');
$not_found_button_link = get_theme_mod('404_button_link');
?>
<section class="wp-block-e25m-section bs-section---default bs-section--page-not-found">
    <div class="container">
        <div class="wp-block-e25m-row bs-row row justify-content-center  bs-row---default">
            <div class=" bs-column col-sm-12 col-md-6  col-xl-6 col-lg-12 bs-column---default  d-sm-flex flex-sm-column d-md-flex flex-md-column d-lg-flex flex-lg-column d-xl-flex flex-xl-column">
                <?php if($not_found_image) { ?>
                <div class="media-elements enable">
                    <div class="bs-common-image">
                        <figure class="figure justify-content-center d-flex">
                            <picture>
                                <img src="<?php echo $not_found_image; ?>" class="img-fluid" alt="" loading="lazy" title="">
                            </picture>

                        </figure>
                    </div>
                </div>
                <?php } ?>
              <h1 class="has-text-align-center"><?php esc_attr_e($not_found_title, 'blankslate'); ?></h1>

                <h6 class="has-text-align-center"><?php esc_attr_e($not_found_sub_title, 'blankslate'); ?></h6>
                <span class="bs-pro-button bs-pro-button---default bs-pro-button--primary-link justify-content-center">
                   <a href="<?php esc_attr_e($not_found_button_link, 'blankslate'); ?>" target="" rel="noopener noreferrer" class="bs-pro-button__container"><?php esc_attr_e($not_found_button_text, 'blankslate'); ?></a>
                </span>
            </div>
        </div>
    </div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>