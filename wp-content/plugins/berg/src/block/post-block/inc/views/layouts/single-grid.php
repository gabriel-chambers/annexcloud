<?php if (!defined('FW')) die('Forbidden'); ?>
<div class="bs-posts">
    <div class="bs-posts__container">
        <!--Normal posts grid-->
        <div class='bs-posts__normal'>
            <div class='bs-posts__normal__grid bs-posts__normal__grid-<?php echo $uniqidID; ?>'>
                <div class='bs-posts__row row'>
                    <?php include 'partial/grid.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>