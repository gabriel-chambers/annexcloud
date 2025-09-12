<?php if (trim(get_the_excerpt())): ?>
    <div class="bs-post__description">
        <?php the_excerpt(); ?>
    </div>
<?php endif; ?>