<html>

<head>
	<title><?php printf(__('Preview Mode - %s', 'mobile-content'), get_bloginfo('name')); ?></title>
	<link rel="stylesheet" href="<?php echo plugin_dir_url(BERG_FILE) . 'dist/editor_blocks_styles.css'; ?>">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@icon/dashicons@0.9.0-alpha.4/dashicons.css">
</head>

<body class="mobile-content-preview-mode">
	<?php
	if (empty($_GET['post'])) {
		wp_die(__('Invalid request. Parameter post is not set', 'mobile-content'));
	} else {
		$desktop_preview = add_query_arg('preview-mode', 'desktop', get_the_permalink($_GET['post']));
		$preview_url = add_query_arg('preview-mode', 'mobile', $desktop_preview);
		$device_preview_urls = [
			'iphone' => $preview_url,
			'ipad' => $preview_url,
			'desktop' => $desktop_preview,
		];
		$device = filter_input(INPUT_GET, 'device');
		$device = isset($device_preview_urls[$device]) ? $device : 'iphone';
	?>
		<div class="tabs">
			<a title="<?php echo __('Mobile', 'mobile-content') ?>" href="<?php echo add_query_arg('device', 'iphone') ?>" class="tab <?php echo $device === 'iphone' ? 'active' : ''; ?>"><span class="dashicons dashicons-smartphone"></span></a>
			<a title="<?php echo __('Tablet', 'mobile-content') ?>" href="<?php echo add_query_arg('device', 'ipad') ?>" class="tab <?php echo $device === 'ipad' ? 'active' : ''; ?>"><span class="dashicons dashicons-tablet"></span></a>
			<a title="<?php echo __('Desktop', 'mobile-content') ?>" href="<?php echo add_query_arg('device', 'desktop') ?>" class="tab <?php echo $device === 'desktop' ? 'active' : ''; ?>"><span class="dashicons dashicons-desktop"></span></a>
		</div>

		<div class="<?php echo $device ?>" style="background-image:url(<?php echo plugin_dir_url(__DIR__) . 'images/' . $device . '-frame.svg'; ?>)">
			<iframe src="<?php echo $device_preview_urls[$device]; ?>" frameborder="0"></iframe>
		</div>
	<?php
	}
	?>
</body>

</html>
<?php
