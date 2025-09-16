<?php
$formId = $blockAttributes['contactFormId'];
?>
<div class='bs-embedded-forms <?php echo $embeddedFormClassNames; ?> bs-embedded-forms-<?php echo $embeddedFormId; ?>'><?= do_shortcode('[contact-form-7 id="' . $formId . '"]'); ?></div>
<script>
  var currentFormWrapper = document.querySelector('.bs-embedded-forms-<?php echo $embeddedFormId; ?>');
  currentFormWrapper.addEventListener('wpcf7mailsent', function(event) {
    if (event.detail.contactFormId == '<?= $formId ?>') {
      <?php if ($responseType == 'redirect' && $redirectURL) { ?>
        window.open('<?= $redirectURL; ?>', '<?= $linkOpenType; ?>');
      <?php } elseif ($responseType == 'download' && $downloadFileURL) { ?>
        window.open('<?= $downloadFileURL; ?>', '_blank');
        <?php } elseif ($responseType == 'popup') {
        if (($popupVideo == 'url' && $popupVideoURL) || ($popupVideo == 'upload' && $popupVideoUploadURL) || ($popupVideo == 'embedded' && $customVideoScript)) { ?>
          document.getElementById("bs_embedded_forms_fancybox_<?= $embeddedFormId; ?>").click();
      <?php }
      } ?>
    }
  }, false);
</script>

<!-- Incluing form response options -->
<?php require('common/response-options.php'); ?>