<?php
/**
 * Created by PhpStorm.
 * User: anusha
 * Date: 4/20/18
 * Time: 3:04 PM
 */

//Unique id for slider
use E25\Base\Modules\SlickSlider;

$slider_id = uniqid('bs-posts__slider-');
$slider_options = SlickSlider::getDataAttribute(fw_akg('picker_items/content-slider', $atts));
?>

<div class="bs-posts">
    <div class="bs-posts__slider" id="<?php echo $slider_id; ?>"
         data-slick='<?php echo json_encode($slider_options); ?>'>
        <?php include 'partial/grid.php'; ?>
    </div>
</div>
