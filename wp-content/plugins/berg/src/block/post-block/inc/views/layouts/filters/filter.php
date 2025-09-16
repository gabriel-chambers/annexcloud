<?php if ($showFilters && !empty($filters)) : ?>
    <div class="bs-posts__filters bs-posts__filters-<?php echo $uniqidID; ?>">
        <?php if ($filterLabel) : ?>
            <div class="bs-posts__filters--title">
                <h3><?php echo $filterLabel; ?></h3>
            </div>
        <?php endif; ?>

        <div class="bs-posts__filters--fields">
            <?php
            if ($showFilters && count($filters)) {
                foreach ($filters as $_filter) {
                    if ($_filter == 'search') {
                        include 'filter-search.php';
                    } else {
                        include 'filter-taxonomy-select.php';
                    }
                }
            }
            ?>
        </div>
    </div>
<?php endif; ?>
