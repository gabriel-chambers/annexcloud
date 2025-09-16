<?php if ($fancyBoxURL) { ?>
    <a id="bs_embedded_forms_fancybox_<?= $embeddedFormId; ?>" data-fancybox="true" data-src="<?= $fancyBoxURL; ?>" rel="noopener noreferrer">
    </a>
<?php } ?>
<?php if ($customVideoScript) { ?>
    <div class="bs-embedded-forms__fancybox" id="bs_embedded_forms_custom_<?= $embeddedFormId; ?>">
        <?php echo $customVideoScript; ?>
    </div>
<?php }
