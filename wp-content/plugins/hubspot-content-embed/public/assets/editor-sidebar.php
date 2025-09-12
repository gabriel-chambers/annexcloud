<?php

$static_asset_domain    = 'static.hsappstatic.net';
$allowed_packages       = array( 'InpageEditorUI' );
$static_version_pattern = '/^static-\d+\.\d+$/i';
$protocol               = 'https://';

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- This data is not being processed no need for nonce
$bender_package_param = isset( $_GET['benderPackage'] ) ? sanitize_text_field( wp_unslash( $_GET['benderPackage'] ) ) : '';
$static_version       = isset( $_GET['staticVersion'] ) ? sanitize_text_field( wp_unslash( $_GET['staticVersion'] ) ) : '';
$branch_preview_build = isset( $_GET['branchPreviewBuild'] ) ? sanitize_text_field( wp_unslash( $_GET['branchPreviewBuild'] ) ) : '';
$css_path             = isset( $_GET['cssPath'] ) ? sanitize_text_field( wp_unslash( $_GET['cssPath'] ) ) : '';
$is_local             = ( isset( $_GET['localAssets'] ) && 'true' === $_GET['localAssets'] );
// phpcs:enable

$output_style_sheet_link = in_array( $bender_package_param, $allowed_packages, true )
	&& ( preg_match( $static_version_pattern, $static_version ) || $is_local )
	&& strlen( $css_path ) > 0;

if ( $is_local ) {
	$static_asset_domain = 'local.hubspotqa.com';
	$static_version      = 'static';
	$protocol            = '//';
} elseif ( $branch_preview_build ) {
	$static_asset_domain = "b-$branch_preview_build.dynamic.hsappstatic.net";
}

$stylesheet_link = $output_style_sheet_link ? "{$protocol}{$static_asset_domain}/{$bender_package_param}/{$static_version}/{$css_path}" : '';

?>
<!doctype html>

<head>
	<meta charset="UTF-8" />
	<meta name="author" content="HubSpot, Inc." />
	<?php if ( $output_style_sheet_link ) { ?>
	<link href="<?php echo esc_url( $stylesheet_link ); // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet ?>" rel="stylesheet" />
	<?php } ?>
</head>

	<body class="hubspot">
		<div id="hubspot-wrapper"></div>
	</body>

</html>
