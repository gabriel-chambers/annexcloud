<div class="clients-slider-wrapper" data-direction="<?php echo $direction; ?>" data-speed="<?php echo $speed; ?>">
	<div class="clients-wrap">
		<ul class="clients-list" animation-duration="<?php echo $speed; ?>">
			<?php
			if (count($images) > 0):
				foreach ($images as $image):
					$content = '<img src="' . $image['mediaURL'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '">';
					$enable_media_url = get_field('enable_media_url', $image['mediaID']);
					$media_url_link = get_field('media_url_link', $image['mediaID']);
					if ($enable_media_url && $media_url_link !== '') {
						$target = get_field('open_in_new_tab', $image['mediaID']) ? 'target="_blank"' : '';
						$html = "<a href='$media_url_link' $target>$content</a>";
					} else {
						$html = $content;
					}
					?>
					<li>
						<div>
							<?php echo $html; ?>
						</div>
					</li>
					<?php
				endforeach;
			endif;
			?>
		</ul>
	</div>
</div>