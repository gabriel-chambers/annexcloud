<div class="bs-post__learn-more">
    <?php
    if ($anchorElementAppearance != 'full'):
        echo render_link('full', $_link_attributes);
    else:
        echo render_link('label', array("class" => "bs-post__learn-more-text", "label" => $read_more_text));
    endif;
    ?>
</div>
