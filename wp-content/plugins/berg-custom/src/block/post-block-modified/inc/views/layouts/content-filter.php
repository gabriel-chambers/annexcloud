<div class="content-filter bs-posts bs-post-block---default 
            <?php echo ($attributes['postVisibility'] == true ? " enable" : " disable"); ?>">
    <?php include 'filters/js-filter.php'; ?>
    <div class="grid row" <?php //echo $data_isotope; ?>>
        <?php include 'partial/grid.php'; ?>
    </div>
</div>
