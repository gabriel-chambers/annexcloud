<?php
$author_meta = get_user_meta($author_id);
$nickname = (!empty($author_meta['nickname'][0])) ? $author_meta['nickname'][0] : '';
$first_name = (!empty($author_meta['first_name'][0])) ? $author_meta['first_name'][0] : '';
$last_name = (!empty($author_meta['last_name'][0])) ? $author_meta['last_name'][0] : '';
$name = ($first_name && $last_name) ? $first_name . ' ' . $last_name : $nickname;
$image = get_field('profile_image', 'user_'.$author_id);
?>

<div class="bs-post__author has-text-align-center">
	<div class="profile-desc">
		<?php if($image) { ?>
		<figure>
			<img src="<?php echo $image; ?>" alt="user-avatar">
		</figure>
		<?php } ?>
		<span class="prefix"><?php echo (!empty($prefix) ? $prefix : '')?></span>
		<span class="name">
			<?php
			echo $name;
			?>
		</span>
	</div>
</div>
