<?php get_header(); ?>
<section class="search-results">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php if (have_posts()) : ?>
                    <h3 class="search-results__entry-title">
                        <?php printf(esc_html__('Search Results for: %s', 'blankslate'), get_search_query()); ?>
                    </h3>
                    <ul class="search-results__search-result-list">
                        <?php while (have_posts()) : the_post(); ?>
                            <?php
                            $postId = get_the_ID();
                            $link = get_the_permalink();
                            $img = get_the_post_thumbnail_url($postId, 'post-thumbnail');
                            $link_attributes    = get_post_link($postId, 'full');
                            $learn_more_type_meta = get_post_meta(get_the_ID(), 'learn_more_type', true);
                            ?>
                            <li>
                                <?php if ($learn_more_type_meta !== 'none')
                                    echo render_link('open', $link_attributes);
                                ?>
                                <div class="row search-results__inner-row">
                                    <?php if ($img) : ?>
                                        <div class="col-md-4 post-image">
                                            <figure><img src="<?php echo $img; ?>"></figure>
                                        </div>
                                        <div class="col-md-8 post-details">
                                            <div class="post-details__title"><?php the_title(); ?></div>
                                            <div class="post-details__date"><?php the_date('M d, Y'); ?></div>
                                            <div class="post-details__excerpt"><?php the_excerpt(); ?></div>
                                        </div>
                                    <?php else : ?>
                                        <div class="col-md-12 post-details">
                                            <div class="post-details__title"><?php the_title(); ?></div>
                                            <div class="post-details__date"><?php the_date('M d, Y'); ?></div>
                                            <div class="post-details__excerpt"><?php the_excerpt(); ?></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if ($learn_more_type_meta !== 'none')
                                    echo render_link('close', $link_attributes);
                                ?>
                                <div id="bs-post__popup--<?php echo $postId; ?>" style="display: none;">
                                    <div class="bs-post__title">
                                        <h5><?php the_title(); ?></h5>
                                    </div>
                                    <?php the_content(); ?>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else : ?>
                    <h3 class="search-results__entry-title"><?php esc_html_e('Nothing Found', 'blankslate'); ?></h3>
                    <p class="search-results__entry-description">
                        <?php esc_html_e('Sorry, nothing matched your search. Please try again.', 'blankslate'); ?>
                    </p>
                    <?php get_search_form(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>