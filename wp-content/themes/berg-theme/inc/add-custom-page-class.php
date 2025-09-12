<?php
// register your custom meta box
function add_post_class()
{
    add_meta_box('add_custom_post_class', 'Post Classes', 'add_custom_class_input', 'page', 'side');
    add_meta_box('add_custom_post_class', 'Post Classes', 'add_custom_class_input', 'post', 'side');
}
add_action('add_meta_boxes', 'add_post_class');

function add_custom_class_input()
{
    global $post;
    $custom_post_classes = get_post_meta($post->ID, 'custom_post_classes', true);
    wp_nonce_field('add_custom_post_class', 'custom_class_hidden');
?>
    <input type="text" style="width:100%" name="custom_post_classes" value="<?php echo $custom_post_classes; ?>" />
    <span>Separate classes by space</span>
<?php
}

function save_post_custom_classes_meta($post_id, $post)
{
    if (!current_user_can('edit_post', $post_id)) return;

    if (!isset($_POST['custom_class_hidden']) || !wp_verify_nonce($_POST['custom_class_hidden'], 'add_custom_post_class')) return;

    if (isset($_POST['custom_post_classes'])) {
        update_post_meta($post_id, 'custom_post_classes', $_POST['custom_post_classes']);
    }
}
add_action('save_post', 'save_post_custom_classes_meta', 1, 2);


add_filter('body_class', function ($classes) {
    $custom_post_classes = get_post_meta(get_the_ID(), 'custom_post_classes', true);
    return array_merge($classes, array($custom_post_classes));
});
