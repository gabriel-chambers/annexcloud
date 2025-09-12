<?php get_header(); ?>
<section class="bs-section bs-section---default bs-section--blog-banner
     bs-section--tag-main-banner bs-section--privacy-banner">
    <div class="container">
        <div class="bs-row row bs-row---default">
            <div class=" bs-column col-sm-12 col-xl-10 bs-column---default">
                <h1 class="wp-block-heading has-space-blue-color has-text-color">
                    Integration Type : <?php single_tag_title(); ?>
                </h1>
            </div>
        </div>
    </div>
</section>

<section class="wp-block-e25m-section bs-section---default bs-section--resources
     bs-section--platform-integrations-post-list bs-section--platform-integrations-tag-page">
    <div class="container">
        <div class="wp-block-e25m-row bs-row row  bs-row---default bs-row--platform-integration-list-row
             bs-row--common-popup-list-row">
            <div class=" bs-column col-sm-12   bs-column-5f6574ebb29ac8d58ab608d2aff5b1bbe4f96332 bs-column---default">
                <div class="facetwp-template" data-name="blog_list">
                    <div class="bs-posts">
                        <div id="masonry-layout" class="bs-posts__list row">
                            <?php
                            $tag_id = get_queried_object()->term_id;

                            // pagination
                            if (get_query_var('paged')) {
                                $paged = get_query_var('paged');
                            } elseif (get_query_var('page')) {
                                $paged = get_query_var('page');
                            } else {
                                $paged = 1;
                            }

                            $args = array(
                                'post_type' => array('integration'),
                                    'orderby' => 'menu_order',
                                    'order' => 'ASC',
                                    'posts_per_page' => 9,
                                    'paged' => $paged,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'integration-type',
                                        'field' => 'term_id',
                                        'terms' => $tag_id,
                                    )
                                ),
                            );
                            $query = new WP_Query($args);

                            while ($query->have_posts()) {
                                $query->the_post();

                                $post_id = $query->post->ID;
                                $post_slug = get_post($post_id)->post_name;
                                $integration_type = "";
                                $integration_type = implode(" </span><span class='tag'> ",
                                     wp_get_object_terms($post_id, ['integration-type'], array("fields" => "names"))
                                );
                                $image_url = get_the_post_thumbnail_url($post_id)
                                    ? get_the_post_thumbnail_url($post_id)
                                    : '';
                                $image_class = $image_url != '' ? 'has-image' : 'no-image';
                                $image_alt = get_post_meta(
                                    get_post_thumbnail_id($post_id),
                                    '_wp_attachment_image_alt',
                                    true
                                );

                                $date_format = 'M j, Y';
                                $show_custom_date        = get_post_meta($post_id, 'show_custom_date', true);
                                $custom_date             = get_post_meta($post_id, 'custom_date', true);

                                if ($show_custom_date == 1) {
                                    $date = date($date_format, strtotime($custom_date));
                                } else {
                                    $date = get_the_date($date_format, $post_id);
                                }

                                $read_more_text     = get_post_meta($post_id, 'learn_more_label', true);
                                $read_more_text     = (trim($read_more_text)) ? $read_more_text : "Read more";
                                $post_description   = get_the_excerpt($post_id);
                                $link_attributes    = get_post_link($post_id, 'full', $read_more_text);
                                $learn_more_type = get_post_meta($post_id, 'learn_more_type', true);
                                $learn_more_cls = ($learn_more_type != "po_link") ? " no-popup" : '';
                                unset($link_attributes['attributes'], $link_attributes['target']);
                                $integration_category = '';
                                if ($integration_type) {
                                    $integration_category = strtolower(
                                        str_replace("</span><span class='tag'>", "", $integration_type)
                                    );
                                    $integration_category = strtolower(str_replace(' ', '-', $integration_category));
                                }
                            ?>

                                <div class="bs-posts__column col-sm-12 col-md-4">
                                    <div class="bs-post bs-post-integrations
                                        <?php echo ' '.$integration_category . " " . $image_class; ?>">
                                        <?php echo render_link('open', $link_attributes); ?>
                                        <div class="bs-post__inner post-article">
                                            <?php if ($image_url) : ?>
                                                <div class="bs-post__image">
                                                    <figure class="figure">
                                                        <img src="<?php echo $image_url; ?>"
                                                            class="img-fluid"
                                                            alt="<?php echo $image_alt; ?>"
                                                            loading="lazy">
                                                    </figure>
                                                </div>
                                            <?php endif; ?>
                                            <div class="bs-post__details">
                                                <div class="bs-post__title">
                                                    <p><?php echo get_the_title($post_id); ?></p>
                                                </div>
                                                <div class="bs-post__description">
                                                    <p><?php echo $post_description; ?></p>
                                                </div>
                                                <div class="bs-post__integrations-type
                                                     bs-post-taxonomy_integrations-type">
                                                    <span class="tag"><?php echo $integration_type; ?></span>
                                                </div>
                                                <div class="bs-post__learn-more">
                                                    <span class="btn learn-more-text bs-post__learn-more-text">
                                                        <?php echo $read_more_text; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php echo render_link('close', $link_attributes); ?>
                                    </div>
                                    <?php
                                    if ($learn_more_type == "po_link") { ?>
                                        <div class="bs-post-platform-integrations--popup"
                                            id="bs-post__popup--<?= $post_id; ?>"
                                            data-post-id="<?= $post_id; ?>"
                                            data-post-slug="<?= $post_slug; ?>">
                                            <?php echo the_content(); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="wp-block-e25m-row bs-row row  bs-row---default">
            <div class="bs-column col-sm-12   bs-column-5f6574ebb29ac8d58ab608d2aff5b1bbe4f96332 bs-column---default">
                <div class="facetwp-facet facetwp-facet-pagination facetwp-type-pager"
                    data-name="pagination"
                    data-type="pager">
                    <div class="facetwp-pager">
                        <?php
                        $args3 = array(
                            'base'         => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                            'total'        => $query->max_num_pages, // WP_Query returns the max
                            'current'      => max(1, get_query_var('paged')),
                            'format'       => '?paged=%#%',
                            'show_all'     => false,
                            'type'         => 'plain',
                            'end_size'     => 2,
                            'mid_size'     => 1,
                            'prev_next'    => true,
                            'prev_text'    => '',
                            'next_text'    => '',
                            'add_args'     => false,
                            'add_fragment' => ''
                        );
                        echo paginate_links($args3);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
