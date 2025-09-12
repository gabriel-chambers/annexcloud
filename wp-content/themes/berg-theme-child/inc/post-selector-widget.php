<?php
/*
Plugin Name: Post Selector Widget
Version: 1.0+1
*/

// register widget
add_action('widgets_init', function () {
    register_widget('PostSelectorWidgetModified');
});

class PostSelectorWidgetModified extends WP_Widget
{
    // class constructor
    public function __construct()
    {
        $widget_ops = array(
            'classname' => 'post_selector_widget_modified_modified',
            'description' => 'A plugin for display selected posts',
        );
        parent::__construct('post_selector_widget_modified_modified', 'Custom Post Selector Widget', $widget_ops);
    }

    // output the widget content on the front-end
    public function widget($args, $instance)
    {
        if (empty($instance['selected_post'])) {
            echo 'No posts selected!';
            return;
        }
    
        $selected_post = get_post($instance['selected_post']);
    
        if (!$selected_post) {
            return;
        }
    
        $link_attributes = get_post_link($selected_post->ID, 'full');
        $image_url = get_the_post_thumbnail_url($instance['selected_post']);
        $read_more_text = get_post_meta($selected_post->ID, 'learn_more_label', true);
        $read_more_text = (trim($read_more_text)) ? $read_more_text : "Read more";
        $show_custom_date = get_post_meta($instance['selected_post'], 'show_custom_date', true);
        $custom_date = get_post_meta($instance['selected_post'], 'custom_date', true);
        $post_date = $show_custom_date == '1' ? date('M d, Y', strtotime($custom_date)) : get_the_time('M d, Y');
    
        $category = $this->get_post_category($selected_post);
    
        ?>
        <div class="nav-content-block">
            <?php echo render_link('open', $link_attributes); ?>
    
            <?php if ($image_url) : ?>
                <figure><img src="<?php echo $image_url ?>" alt="<?php echo $image_url ?>" /></figure>
            <?php endif; ?>
    
            <div class="content-wrapper">
                <div class="category-and-date">
                    <span class="category"><?php echo $category ?></span>
    
                    <?php if ($this->should_show_date($category)) : ?>
                        <span class="date"><?php echo $post_date ?></span>
                    <?php endif; ?>
                </div>
                <p class="post-title"><?php echo $selected_post->post_title; ?></p>
                <span class="read-more"><?php echo $read_more_text; ?></span>
            </div>
    
            <?php echo render_link('close', $link_attributes); ?>
    
        </div>
    
        <?php
        $learn_more_type = get_post_meta($selected_post->ID, 'learn_more_type', true);
    
        if ($learn_more_type == "po_link") {
            $this->render_popup_post($selected_post);
        }
    }
    
    // Helper function to get the post category based on the post type
    private function get_post_category($post) {
        switch ($post->post_type) {
            case "resource":
                $taxonomy = 'resource-type';
                break;
            case "post":
                $taxonomy = 'category';
                break;
            case "news":
                $taxonomy = 'news-category';
                break;
            default:
                $taxonomy = '';
                break;
        }
    
        $terms = wp_get_post_terms($post->ID, $taxonomy);
    
        return implode(' | ', wp_list_pluck($terms, 'name'));
    }
    
    // Helper function to determine if the date should be shown based on the category
    private function should_show_date($category) {
        return strpos($category, "Blog") !== false || strpos($category, "In the news") !== false;
    }

    // Helper function to render the popup post content
    private function render_popup_post($post) {
        ?>
        <div class="bs-post__target bs-post__target--popup-post"
            id="bs-post__popup--<?= $post->ID; ?>"
            data-post-id="<?= $post->ID; ?>"
            style="display: none;">
            <p>
                <?php echo apply_filters('the_content', get_post_field('post_content', $post->ID)); ?>
            </p>
        </div>
        <?php
    }

    // output the option form field in admin Widgets screen
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('Title', 'text_domain'); ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Title:', 'text_domain'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
        $selected_post = !empty($instance['selected_post']) ? $instance['selected_post'] : '';

        // Retrieving the posts
        $args = array(
            'numberposts' => -1,
            'post_type' => array('resource'),
            'orderby' => array(
                'post_type' => 'ASC',
                'title' => 'ASC',
            ),
            'post_status' => 'publish',
        );

        $posts_array = get_posts($args);
        ?>

        <label for="<?php echo $this->get_field_id('selected_post'); ?>"><?php _e('Select Post'); ?></label>
        <select name="<?php echo $this->get_field_name('selected_post'); ?>"
            id="<?php echo $this->get_field_id('selected_post'); ?>" class="widefat post-selector-widget-select2">
            <?php
            $post_type = '';
            foreach ($posts_array as $post): ?>
                <?php if ($post_type != $post->post_type): ?>
                    <optgroup label="<?php echo ucfirst($post->post_type); ?>">
                    <?php endif; ?>
                    <option
                         value="<?php echo $post->ID; ?>"
                        id="<?php echo $post->ID; ?>" <?php selected($selected_post, $post->ID, true); ?>>
                        <?php echo $post->post_title ?>
                    </option>
                    <?php $post_type = $post->post_type;
                    if ($post_type != $post->post_type): ?>
                    </optgroup>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

    <?php }

    // save options
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['selected_post'] = (!empty($new_instance['selected_post'])) ? $new_instance['selected_post'] : '';

        return $instance;
    }
}
