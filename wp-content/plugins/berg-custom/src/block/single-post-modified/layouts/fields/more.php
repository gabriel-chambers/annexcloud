<div class="bs-post__learn-more">
    <?php
    if ($anchor_appearance != 'full'):
        echo render_link('full', $link_attributes);
    else:
        echo render_link('label', array("class" => "bs-post__learn-more-text", "label" => $read_more_text));
    endif;
    ?>
</div>
