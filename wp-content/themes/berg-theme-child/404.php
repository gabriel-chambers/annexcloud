<?php
get_header();
$not_found_image = get_theme_mod('404_image');
$not_found_background_image = get_theme_mod('404_background_image');
$not_found_title = get_theme_mod('404_title');
$not_found_sub_title = get_theme_mod('404_sub_title');
$not_found_descriptions = get_theme_mod('404_descriptions');
$not_found_button_text = get_theme_mod('404_button_text');
$not_found_button_link = get_theme_mod('404_button_link');
?>
<section class="wp-block-e25m-section bs-section---default bs-section--page-not-found"
    <?php if (!empty($not_found_background_image)) {
        echo " style='background-image:url(".$not_found_background_image.")'";
    } ?>>
    <div class="container">
        <div class="wp-block-e25m-row bs-row row justify-content-center  bs-row---default">
            <div class=" bs-column col-sm-12 col-md-8 col-lg-6 col-xl-6 bs-column---default d-flex flex-column">
            <?php if($not_found_image) { ?>
                <div class="media-elements enable">
                    <div class="bs-common-image">
                        <figure class="figure justify-content-center d-flex">
                            <picture>
                                <img src="<?php echo $not_found_image; ?>"
                                    class="img-fluid"
                                    alt=""
                                    loading="lazy"
                                    title="">
                            </picture>

                        </figure>
                    </div>
                </div>
            <?php } ?>
            <p class="has-text-align-center gradient-text">
                <?php esc_attr_e($not_found_title, 'blankslate'); ?>
            </p>
            <h1 class="has-text-align-center"><?php esc_attr_e($not_found_sub_title, 'blankslate'); ?></h1>
            <p class="has-text-align-center"><?php esc_attr_e($not_found_descriptions, 'blankslate'); ?></p>
            <span
             class="bs-pro-button bs-pro-button---default bs-pro-button--primary-with-arrow justify-content-center">
                <a href="<?php esc_attr_e($not_found_button_link, 'blankslate'); ?>"
                    target=""
                    rel="noopener noreferrer"
                    class="bs-pro-button__container">
                    <?php esc_attr_e($not_found_button_text, 'blankslate'); ?>
                </a>
            </span>
            </div>
        </div>
    </div>
</section>
<?php get_sidebar(); ?>
<?php get_footer(); ?>
