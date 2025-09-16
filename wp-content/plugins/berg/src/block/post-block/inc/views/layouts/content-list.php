<div class="bs-posts list-view <?php echo implode(' ',array_column($postBlockClassNames,'value')); 
	echo ($attributes['postVisibility'] == true ? " enable" : " disable"); ?>">
    <div class="bs-posts__container">
		<?php
		if(!empty($layoutDisplayOrders) && is_array($layoutDisplayOrders)):
			$layoutDisplayOrders = array_column($layoutDisplayOrders,'value');
			foreach ($layoutDisplayOrders AS $layout):
				 include ($layout.'-layout.php');
			endforeach;
		endif;
		?>
    </div>
</div>
