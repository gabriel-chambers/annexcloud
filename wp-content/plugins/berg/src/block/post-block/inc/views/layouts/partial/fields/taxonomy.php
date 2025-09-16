<?php
$term_list = wp_get_post_terms($_post_id, str_replace('taxonomy_', '', $display), array("fields" => "all"));

if (count($term_list)) { ?>
    <div class="bs-post__category bs-post-<?php echo $display; ?>">
        <?php foreach ($term_list as $term) {
            $term_link = get_term_link($term);
            // If there was an error, continue to the next term.
            if (is_wp_error($term_link)) {
                continue;
            }
            $term_html = ($attributes['enablTaxonomiesLink'] && $anchorElementAppearance != 'full') ? '<a href="' . esc_url($term_link) . '"><span>' . $term->name . '</span></a>' : '<span>' . $term->name . '</span>';
            echo $term_html;
        } ?>
    </div>
<?php } ?>