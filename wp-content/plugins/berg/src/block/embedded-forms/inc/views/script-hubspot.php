<?php
$portalId = $blockAttributes['portalId'];
$formId = $blockAttributes['hubspotFormId'];
$sfdcCampaignId = $blockAttributes['sfdcCampaignId'];
$enableCss = $blockAttributes['enableCss'];
$regionId = $blockAttributes['regionId'];
?>
<script charset="utf-8" type="text/javascript" src="https://js.hsforms.net/forms/shell.js"></script>
<script>
	var embeddedFormId = '<?php echo $embeddedFormId; ?>';
	var portalId = '<?php echo $portalId; ?>';
	var formId = '<?php echo $formId; ?>';
	var sfdcCampaignId = '<?php echo $sfdcCampaignId; ?>';
	var responseType = '<?php echo $responseType; ?>';
	var responseMessage = '<?php echo $responseMessage; ?>';
	var redirectURL = '<?php echo $redirectURL; ?>';
	var downloadFileURL = '<?php echo $downloadFileURL; ?>';
	var linkOpenType = '<?php echo $linkOpenType; ?>';
	var popupVideo = '<?php echo $popupVideo; ?>';
	var enableCss = '<?php echo $enableCss; ?>';
	var regionId = '<?php echo $regionId; ?>';

	var popupVideoURL = '<?php echo $popupVideoURL; ?>';
	var popupVideoUploadURL = '<?php echo $popupVideoUploadURL; ?>';
	hbspt.forms.create({
		region: regionId,
		portalId: portalId,
		formId: formId,
		sfdcCampaignId: sfdcCampaignId,
		inlineMessage: responseMessage ? responseMessage : null,
		css: enableCss ? true : null,
		target: '#bs-embedded-forms-' + embeddedFormId + '__form',
		onFormSubmitted: function() {
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
	});
</script>
<div class='bs-embedded-forms <?php echo $embeddedFormClassNames; ?> bs-embedded-forms-<?php echo $embeddedFormId; ?>' id="bs-embedded-forms-<?php echo $embeddedFormId; ?>__form">
</div>
<!-- Incluing form response options -->
<?php require('common/response-options.php'); ?>
