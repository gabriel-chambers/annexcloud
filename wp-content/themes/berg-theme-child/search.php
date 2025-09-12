<?php get_header(); ?>
<section class="bs-section--search-results bs-section--helper-header-box-shadow">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                if (get_query_var('paged')) {
                    $paged = get_query_var('paged');
                } elseif (get_query_var('page')) {
                    $paged = get_query_var('page');
                } else {
                    $paged = 1;
                }

                if (have_posts()) {
                    global $wp_query;
                    get_search_form();
                ?>
                    <h1 class="search-results__entry-title">
                        <span><?php echo $wp_query->found_posts; ?> </span> results for
                        <?php if (get_search_query()) : ?>
                            &quot;<span>
                                <?php printf(esc_html__("%s", 'blankslate'), get_search_query()); ?>
                            </span>&quot;
                        <?php endif; ?>
                    </h1>
                    <ul class="search-results__search-result-list">
                        <?php while (have_posts()) : the_post(); ?>
                            <?php
                            $postId = get_the_ID();
                            if ( get_post_type( $postId ) == 'leadership'
                            || get_post_type( $postId ) == 'sw_partners'
                            || get_post_type( $postId ) == 'marketplace'
                            || get_post_type( $postId ) == 'integration'
                            || get_post_type( $postId ) == 'usecases'
                            || get_post_type( $postId ) == 'news'
                            || get_post_type( $postId ) == 'events'
                            || get_post_type( $postId ) == 'careers' ) {
                                $link_attributes    = [ "link" => get_the_permalink() ];
                                $link = render_link('full', $link_attributes);
                            } else{
                                $link_attributes    = get_post_link($postId, 'full');
                                $link = render_link('full', $link_attributes);
                            }

                            ?>
                            <li>
                            <?php
                                echo render_link('open', $link_attributes);
                            ?>
                                <div class="row search-results__inner-row">
                                    <div class="col-md-12 post-details">
                                        <div class="post-details__title"><?php the_title(); ?></div>
                                        <?php if (has_excerpt()) { ?>
                                            <div class="post-details__excerpt"><?php the_excerpt(); ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php
                                echo render_link('close', $link_attributes);
                            ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php } else { ?>
                    <h3 class="search-results__entry-title"><?php esc_html_e('Nothing Found', 'blankslate'); ?></h3>
                    <p class="search-results__entry-description">
                        <?php esc_html_e('Sorry, nothing matched your search. Please try again.', 'blankslate'); ?>
                    </p>
                <?php } ?>
            </div>
            <div class=" bs-column col-sm-12 bs-column---default">
                <div class="facetwp-facet facetwp-facet-pagination facetwp-type-pager"
                    data-name="pagination" data-type="pager">
                    <div class="facetwp-pager">
                        <?php
                        $args3 = array(
                            'base'         => str_replace(999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) )),
                            'total'        => $wp_query->max_num_pages,
                            'current'      => max( 1, get_query_var( 'paged' ) ),
                            'format'       => '?paged=%#%',
                            'show_all'     => false,
                            'type'         => 'plain',
                            'end_size'     => 2,
                            'mid_size'     => 1,
                            'prev_next'    => true,
                            'prev_text'    => '',
                            'next_text'    => '',
                            'add_args'     => false,
                            'add_fragment' => '',
                            'paged' => $paged
                        );
                        echo paginate_links($args3);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bs-section bs-section---default bs-section--common-ready-to-get-started">
    <?php
        $ready_to_get_started_title = get_field('ready_to_get_started_title', 'option');
        $button_title = get_field('button_title', 'option');
        $button_link = get_field('button_link', 'option');
        $link_type = get_field('link_type', 'option');
    ?>

    <div class="container">
        <div class="bs-row row bs-row---default">
            <div
                class=" bs-column col-sm-12 bs-column---default">
                <h2 class="wp-block-heading has-white-color has-text-color">
                    <span
                         class="ez-toc-section"
                         id="ready_to_get_started"
                         ez-toc-data-id="#ready_to_get_started">
                    </span>
                    <?php echo $ready_to_get_started_title; ?>
                    <span class="ez-toc-section-end"></span>
                </h2>
                <?php if($button_title){ ?>
                    <span
                        class="bs-pro-button bs-pro-button---default bs-pro-button--tertiary-with-arrow">
                        <a href="<?php echo $button_link; ?>"
                            target="<?php if($link_type){ echo '_self'; }else{ echo '_blank'; } ?>"
                            rel="noopener noreferrer" class="bs-pro-button__container">
                            <?php echo $button_title; ?>
                        </a>
                    </span>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
