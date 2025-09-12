<article class='glossary-item'>
    <div class='featured-image'><a href='<?php echo  get_permalink($item->ID); ?>'><?php echo $thumbnail; ?></a></div>
    <h2 class='entry-title'><a href='<?php echo get_permalink($item->ID); ?>'><?php echo $item->post_title; ?></a></h2>
    <div class='entry-content'><?php echo wp_trim_words(wp_strip_all_tags($item->post_content, true), $termContentWordslimit, '...'); ?></div>
    <div class='entry-footer'></div>
</article>
<br><br>