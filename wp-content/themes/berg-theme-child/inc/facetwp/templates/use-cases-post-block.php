<?php
$facetwp_common_class = isset($facetwp_common_class) ? $facetwp_common_class : '';
$facetwp_column_count = isset($facetwp_column_count)
    ? $facetwp_column_count
    : esc_attr(get_option('facetwp_column_count'));
$facetwp_not_found_message = isset($facetwp_not_found_message)
    ? $facetwp_not_found_message
    : "Sorry, no items found!";
$facetwp_additional_classes_array = explode(",", esc_attr(get_option('facetwp_additional_class')));
$facetwp_additional_classes = implode(" ", $facetwp_additional_classes_array);
?>
<div class="bs-posts <?php echo $facetwp_additional_classes; ?>">
    <div id="masonry-layout" class="bs-posts__list row">
        <?php
        switch ($facetwp_column_count) {
            case "1":
                $facetwp_column_class = "col-md-12";
                break;
            case "2":
                $facetwp_column_class = "col-md-6";
                break;
            case "3":
                $facetwp_column_class = "col-md-4";
                break;
            case "4":
                $facetwp_column_class = "col-md-3";
                break;
            default:
                $facetwp_column_class = "col-md-4";
        }
        while (have_posts()) : the_post();
            $post_id = get_the_ID();
            $date_format = 'M j, Y';
            $show_custom_date = get_post_meta($post_id, 'show_custom_date', true);
            $custom_date = get_post_meta($post_id, 'custom_date', true);
            $post_slug = get_post($post_id)->post_name;
            if ($show_custom_date == 1) {
                $date = date($date_format, strtotime($custom_date));
            } else {
                $date = get_the_date($date_format, $post_id);
            }
            $post_type = get_post_type($post_id);

            $image_url = get_the_post_thumbnail_url($post_id) ? get_the_post_thumbnail_url($post_id) : '';
            $image_class = $image_url != '' ? 'has-image' : 'no-image';
            $image_alt = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
            $no_link_class = get_post_meta($post_id, 'learn_more_type', true) == 'none' ? 'no-link' : '';
            $read_more_text = get_post_meta($post_id, 'learn_more_label', true);
            $read_more_text = (trim($read_more_text)) ? $read_more_text : "Read more";
            $learn_more_type = get_post_meta($post_id, 'learn_more_type', true);
            $learn_more_cls = ($learn_more_type != "po_link") ? "no-popup" : '';
            $link_attributes = get_post_link($post_id, 'full', $read_more_text);
            unset($link_attributes['attributes'], $link_attributes['target']);
        ?>
            <div class="bs-posts__column col-sm-12 <?php echo $facetwp_column_class; ?>">
                <?php
                if ($facetwp_common_class != '') {
                    $classes_concat = implode(' ', [
                        $facetwp_common_class,
                        $image_class,
                        $no_link_class,
                        $learn_more_cls
                    ]);
                    echo '<div class="' . $classes_concat . ' bs-post__slide-down"
                    data-post-id="' . $post_id . '"
                    data-post-slug="' . $post_slug . '"
                    oncontextmenu="return false;">';
                }
                echo render_link('open', $link_attributes);
                ?>
                <div class="bs-post__inner post-article">
                    <?php
                    if (isset($display_order)) {
                        if (in_array('image', $display_order) && $image_url != '') {
                            echo '<div class="bs-post__image">
                            <figure class="figure"> <img
                            src="' . $image_url . '"
                            class="img-fluid"
                            alt="' . $image_alt . '" loading="lazy"/>
                            </figure>
                            </div>';
                        }
                        echo '<div class="bs-post__details">';
                        foreach ($display_order as $order => $item) {
                            switch ($item) {
                                case 'date':
                                    echo '<div class="bs-post__date"> <span>' . $date . '</span></div>';
                                    break;
                                case 'title':
                                    echo '<div class="bs-post__title">
    <p>' . get_the_title($post_id) . '</p></div>';
                                    break;
                                case 'excerpt':
                                    echo '<div class="bs-post__description">
    <p>' . get_the_excerpt($post_id) . '</p></div>';
                                    break;
                                case 'link':
                                    echo '<div class="bs-post__learn-more">
    <span class="btn learn-more-text bs-post__learn-more-text">'
                                        . $read_more_text . '</span></div>';
                                    break;
                                default:
                                    break;
                            }
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
                <?php
                echo render_link('close', $link_attributes);
                if ($facetwp_common_class != '') {
                    echo '</div>';
                }
                ?>
            </div>
            <?php
        endwhile;
        while (have_posts()) : the_post();
            $post_id = get_the_ID();
            $post_slug = get_post($post_id)->post_name;
            $learn_more_type = get_post_meta($post_id, 'learn_more_type', true);
            if ($learn_more_type == "po_link") { ?>
                <div class="bs-post__target bs-post__target--popup-post col-sm-12
                 bs-post__slide-down-content" id="bs-post__popup--<?= $post_id; ?>"
                 data-post-id="<?= $post_id; ?>" data-post-slug="<?= $post_slug; ?>" style="display: none;">
                    <?php echo the_content(); ?>
                </div>
            <?php }
        endwhile;

        if (!have_posts()) {
            ?>
            <div class="bs-posts__not-found col-12 text-center">
                <h3><?php echo $facetwp_not_found_message; ?></h3>
            </div>
        <?php
        }
        ?>
    </div>
</div>
