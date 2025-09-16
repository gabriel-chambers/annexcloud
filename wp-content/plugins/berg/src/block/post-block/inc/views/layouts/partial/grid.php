<?php if ($_the_query->have_posts()) : ?>
    <?php while ($_the_query->have_posts()) {
        $_the_query->the_post();
        include 'post-variables.php';
        $_classes = array();
        array_push(
            $_classes,
            "bs-post",
            $_grid_class,
            $postType,
            $_col_xm,
            $_col_sm,
            $_col_md,
            $_col_lg,
            $_col_xl,
            join(' ', array_filter($term_list_slug))
        );
        ?>
        <div class="<?php echo join(' ', array_filter($_classes)); ?>">
            <?php include 'layout.php' ?>
            <?php include 'layout-popup.php' ?>
            <?php //include 'gated-form.php' ?>
        </div>
    <?php } ?>
<?php endif; ?>

<?php
$_fet_status = false;
if (isset($_fea_the_query)) {
    $_fet_status = $_fea_the_query->have_posts();
}
if (!$_the_query->have_posts()) : ?>
    <div class="bs-posts__not-found col-12">
        <?php
		if (isset($noEntriesFoundText)) : ?>
            <h3><?php echo $noEntriesFoundText; ?></h3>
        <?php endif; ?>
    </div>
<?php endif; ?>
