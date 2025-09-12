<?php
$post_type_obj = get_post_type_object(get_post_type());
if ($post_type_obj) :
	$post_type_name = $post_type_obj->labels->name;
?>
	<div class="bs-post__post-type-name">
		<span><?php echo __($post_type_name); ?></span>
	</div>
<?php
endif;
?>
