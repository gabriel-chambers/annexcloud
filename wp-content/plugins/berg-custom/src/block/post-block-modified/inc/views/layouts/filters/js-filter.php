<?php
$data_isotope = '';
if ($showFilters) : ?>
    <div class="js-post-blocks-filters filter-button-group">
        <?php
        if ($showAllOption) {
            $_link_attributes = array(
                'link' => 'javascript:void(0);',
                'class' => "bs-posts__filter--btn",
                'label' => "All",
                'target' => '',
                'attributes' => "data-filter='*'",
                'anchor_appearance' => "inner",
            );

            echo render_link("full", $_link_attributes);

            $data_isotope = 'data-isotope=\'{ "filter": ".*" }\'';
        }

        if ($showFilters && count($filters)) {
            $inc = 1;
            foreach ($filters as $_filter) {
                if ($_filter != 'search') {
                    if ($inc == 1) {
                        $args = array('number' => '1',);
                        $terms = get_terms($_filter, $args);
                        if (is_array($terms)) {
                            $term_slug = $terms[0]->slug;
                            $data_isotope = 'data-isotope=\'{ "filter": ".' . $term_slug . '" }\'';
                        }
                    }
                    include 'filter-taxonomy-button.php';
                    $inc++;
                }
            }
        }
        ?>
    </div>
<?php endif; ?>
