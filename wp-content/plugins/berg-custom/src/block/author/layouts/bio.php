<?php
$author = get_the_author_meta('ID', $author_id);
$author_meta = get_user_meta($author);
$nickname = (!empty($author_meta['nickname'][0])) ? $author_meta['nickname'][0] : '';
$first_name = (!empty($author_meta['first_name'][0])) ? $author_meta['first_name'][0] : '';
$last_name = (!empty($author_meta['last_name'][0])) ? $author_meta['last_name'][0] : '';
$name = ($first_name && $last_name) ? $first_name . ' ' . $last_name : $nickname; 
$description = (!empty($author_meta['description'][0])) ? $author_meta['description'][0] : '';

$image = get_field('profile_image', 'user_'.$author_id);
$designation = get_field('designation', 'user_'.$author_id);
$linkedin_url = get_field('linkedin_url', 'user_'.$author_id);
$facebook_url = get_field('facebook_url', 'user_'.$author_id);
$twitter_url = get_field('twitter_url', 'user_'.$author_id);
?>
<div class="post__author-bio">
    <div class="post__author-bio-left">
    <?php if($image) { ?>
    <figure>
		<img src="<?php echo $image; ?>" alt="user-avatar">
	</figure>
    <?php } ?>
    </div>
    <div class="post__author-bio-right">
        <div class="post__author-details">
            <div class="post__author-name">
                <h2><?php echo $name; ?></h2>
                <h3><?php echo $designation; ?></h3>
            </div>
        </div>
        <p><?php echo $description; ?></p>
        <?php if($linkedin_url){ ?><a href="<?php echo $linkedin_url; ?>" target="_blank"><span class="in"></span></a><?php } ?>
        <?php if($facebook_url){ ?><a href="<?php echo $facebook_url; ?>" target="_blank"><span class="fb"></span></a><?php } ?>
        <?php if($twitter_url){ ?><a href="<?php echo $twitter_url; ?>" target="_blank"><span class="tw"></span></a><?php } ?>    
    </div>
</div>