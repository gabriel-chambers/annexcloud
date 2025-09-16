<?php
$marketoBaseUrl = $blockAttributes['marketoBaseUrl'];
$munchkinId = $blockAttributes['munchkinId'];
$formId = $blockAttributes['marketoFormId'];
$disableMarketoDefaultFollowUp =
	in_array($blockAttributes['disableMarketoDefaultFollowUp'],[true, 'true', 1, '1'], true) === true ? 'true' : 'false';
?>
<script src="<?php echo $marketoBaseUrl; ?>/js/forms2/js/forms2.min.js"></script>
<form id="mktoForm_<?php echo $formId; ?>"></form>
<script>
    let embeddedFormId = '<?php echo $embeddedFormId; ?>';
    let marketoBaseUrl = '<?php echo $marketoBaseUrl; ?>';
    let munchkinId = '<?php echo $munchkinId; ?>';
    let formId = '<?php echo $formId; ?>';
    let responseType = '<?php echo $responseType; ?>';
    let responseMessage = '<?php echo $responseMessage; ?>';
    let redirectURL = '<?php echo $redirectURL; ?>';
    let downloadFileURL = '<?php echo $downloadFileURL; ?>';
    let linkOpenType = '<?php echo $linkOpenType; ?>';
    let popupVideo = '<?php echo $popupVideo; ?>';
    let popupVideoURL = '<?php echo $popupVideoURL; ?>';
    let popupVideoUploadURL = '<?php echo $popupVideoUploadURL; ?>';
    let disableMarketoDefaultFollowUp = <?php echo $disableMarketoDefaultFollowUp ?>;
    MktoForms2.loadForm(marketoBaseUrl, munchkinId, formId, function(form) {
        form.onSuccess(function(values, followUpUrl) {
            document.getElementById("int_mktoForm_" + formId).innerHTML = responseMessage;
            <?php if ($responseType == 'redirect' && $redirectURL) { ?>
                window.open('<?= $redirectURL; ?>', '<?= $linkOpenType; ?>');
            <?php } elseif ($responseType == 'download' && $downloadFileURL) { ?>
                window.open('<?= $downloadFileURL; ?>', '_blank');
                <?php } elseif ($responseType == 'popup') {
                if (($popupVideo == 'url' && $popupVideoURL) ||
				($popupVideo == 'upload' && $popupVideoUploadURL) ||
				($popupVideo == 'embedded' && $customVideoScript)) { ?>
                    document.getElementById("bs_embedded_forms_fancybox_<?= $embeddedFormId; ?>").click();
            <?php }
            } ?>
			return !disableMarketoDefaultFollowUp;
        });
    });
</script>
<div class="form-submit-note" id="int_mktoForm_<?php echo $formId; ?>"></div>
<!-- Incluing form response options -->
<?php require('common/response-options.php'); ?>
