<?php

if (!function_exists('pretty_print')) {
    /**
     * @param $value
     */
    function pretty_print($value)
    {
        static $first_time = true;
        if ($first_time) {
            ob_start();
            echo '<style type="text/css">div.fw_print_r{max-height:500px;overflow-y:scroll;background:#23282d;margin:10px 30px;padding:0;border:1px solid #F5F5F5;border-radius:3px;position:relative;z-index:11111}div.fw_print_r pre{color:#78FF5B;background:#23282d;text-shadow:1px 1px 0 #000;font-family:Consolas,monospace;font-size:12px;margin:0;padding:5px;display:block;line-height:16px;text-align:left}div.fw_print_r_group{background:#f1f1f1;margin:10px 30px;padding:1px;border-radius:5px;position:relative;z-index:11110}div.fw_print_r_group div.fw_print_r{margin:9px;border-width:0}</style>';
            echo str_replace(array('  ', "\n"), '', ob_get_clean());
            $first_time = false;
        }
        echo '<div class="fw_print_r"><pre>';
        print_r($value);
        echo '</pre></div>';
    }
}

if (!function_exists('get_array_values')) {
    function get_array_values($data)
    {
        return array_column($data, 'value');
    }
}

if (!function_exists('default_display_fields')) {
    /**
     * defaultDisplayFields
     * @return array
     */
    function default_display_fields()
    {
        return array(
            'title' => 'Title',
            'image' => 'Image',
            'meta_featured_image' => 'Featured image',
            'excerpt' => 'Excerpt',
            'content' => 'Content',
            'date' => 'Date',
            'more' => 'Read more',
            'author' => 'Author Name',
            'event_date' => 'Event Date [start date - End date]',
            'post_type_name' => 'Post Type Name',
            'post_count' => 'Post View Count',
        );
    }
}

if (!function_exists('get_all_meta')) {
    /**
     * get All Meta by post type
     * @param $postType
     * @return array
     */
    function get_all_meta($postType)
    {
        global $wpdb;
        $_unset = array("_edit_last", "_edit_lock");
        $_meta = array();
        $results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT  meta_key FROM  $wpdb->posts, $wpdb->postmeta WHERE post_type = %s AND $wpdb->posts.ID = $wpdb->postmeta.post_id", $postType), ARRAY_A);
        $results = array_diff(array_column($results, 'meta_key'), $_unset);
        foreach ($results as $result) {
            if (substr($result, 0, 1) !== "_") {
                $_meta['meta_' . $result] = ucwords(str_replace('_', ' ', $result));
            }
        }
        return $_meta;
    }
}

if (!function_exists('array_by_keys')) {
    /**
     * Recursively find a key's value in array
     *
     * @param string/array $keys 'a/b/c'
     * @param array|object $array_or_object
     * @param null|mixed $default_value
     * @param string $keys_delimiter
     *
     * @return null|mixed
     */
    function array_by_keys($keys, array $array_or_object, $default_value = null, $keys_delimiter = '.')
    {
        if (!is_array($keys)) {
            $keys = explode($keys_delimiter, (string)$keys);
        }

        $key_or_property = array_shift($keys);
        if ($key_or_property === null) {
            return $default_value;
        }

        $is_object = is_object($array_or_object);

        if ($is_object) {
            if (!property_exists($array_or_object, $key_or_property)) {
                return $default_value;
            }
        } else {
            if (!is_array($array_or_object) || !array_key_exists($key_or_property, $array_or_object)) {
                return $default_value;
            }
        }

        if (isset($keys[0])) { // not used count() for performance reasons
            if ($is_object) {
                return array_by_keys($keys, $array_or_object->{$key_or_property}, $default_value);
            } else {
                return array_by_keys($keys, $array_or_object[$key_or_property], $default_value);
            }
        } else {
            if ($is_object) {
                return $array_or_object->{$key_or_property};
            } else {
                return $array_or_object[$key_or_property];
            }
        }
    }
}

if (!function_exists('berg_attributes_default')) {
    /**
     * Merges an attribute array with default values.
     * Merged when the attribute value is "" or doesn't exist.
     *
     * @since 2.0
     */
    function berg_attributes_default($attributes, $defaults)
    {
        $out = array();
        foreach ($attributes as $name => $value) {
            $out[$name] = $value;
        }
        foreach ($defaults as $name => $default) {
            if (array_key_exists($name, $out)) {
                if ($out[$name] === '') {
                    $out[$name] = $default;
                }
            } else {
                $out[$name] = $default;
            }
        }
        return $out;
    }
}

if (!function_exists('berg_blog_posts_block_default_attributes')) {
    function berg_blog_posts_block_default_attributes($attributes)
    {
        $defaultsAttributes = json_decode(file_get_contents(WP_PLUGIN_DIR  . "/berg/src/block/post-block/inc/attributes.json"), true);
        $defaults = [];
        foreach ($defaultsAttributes as $key => $defaultsAttribute) {
            $defaults[$key] = $defaultsAttribute['default'];
        }

        if (!$attributes['displayOrders']) {
            unset($attributes['displayOrders']);
        }

        return berg_attributes_default($attributes, $defaults);
    }
}

if (!function_exists('berg_rest_get_terms')) {
    /**
     * REST Callback. Gets all the terms registered for all post types (including category and tags).
     *
     * @see https://stackoverflow.com/questions/42462187/wordpress-rest-api-v2-how-to-list-taxonomy-terms
     *
     * @since 2.0
     */
    function berg_rest_get_terms()
    {
        $args = array(
            'public' => true,
        );
        $taxonomies = get_taxonomies($args, 'objects');


        $_defaultFields = default_display_fields();
        $return = array();

        $post_types = get_post_types(array('public' => true), 'objects');
        foreach ($post_types as $post_type => $data) {
            // Don't include attachments.
            if ($post_type === 'attachment') {
                continue;
            }
            $return[$post_type] = array(
                'label' => $data->label,
                'taxonomies' => array(),
            );

            $return[$post_type]['display_orders'] = array_merge($_defaultFields, get_all_meta($post_type));
        }

        foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
            foreach ($taxonomy->object_type as $post_type) {
                // Don't include post formats.
                if ($post_type === 'post' && $taxonomy_slug === 'post_format') {
                    continue;
                }
                $return[$post_type]['display_orders']['taxonomy_' . $taxonomy_slug] = $taxonomy->label;

                $return[$post_type]['taxonomies'][$taxonomy_slug] = array(
                    'label' => $taxonomy->label,
                    'terms' => get_terms($taxonomy->name),
                );
            }
        }

        return new WP_REST_Response($return, 200);
    }
}

if (!function_exists('berg_get_terms_endpoint')) {
    /**
     * Define our custom REST API endpoint for getting all the terms/taxonomies.
     *
     * @since 2.0
     */
    function berg_get_terms_endpoint()
    {
        register_rest_route('wp/v2', '/berg_terms', array(
            'methods' => 'GET',
            'callback' => 'berg_rest_get_terms',
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
        ));
    }

    add_action('rest_api_init', 'berg_get_terms_endpoint');
}

if (!function_exists('bootstrap_pagination')) {
    /**
     * bootstrap pagination
     * @param $query
     * @param $paged
     * @param $prev_btn_label
     * @param $next_btn_label
     */
    function bootstrap_pagination($max_num_pages, $paged, $prev_btn_label, $next_btn_label)
    {
        $big = 999999999; // need an unlikely integer
        $links = paginate_links(array(
            'base' => str_replace($big, '%#%', get_pagenum_link($big, false)),
            'format' => '?paged=%#%',
            'current' => $paged,
            'total' => $max_num_pages,
            'show_all' => false,
            'end_size' => 1,
            'mid_size' => 2,
            'prev_next' => true,
            'prev_text' => __($prev_btn_label),
            'next_text' => __($next_btn_label),
            'type' => 'array',
            'add_args' => false,
            'add_fragment' => '',
            'before_page_number' => '',
            'after_page_number' => ''
        ));

        if ($links) :
            $numItems = count($links);
            echo '<ul class="pagination">';
            foreach ($links as $key => $link) :
                $link = str_replace('class="', 'class="page-link ', $link);
                $link = str_replace("class='", "class='page-link ", $link);
                if (strpos($link, 'current') !== false) {
                    $menu_active = "page-item active";
                } else {
                    $menu_active = "page-item";
                }
                if ($key == 0) {
                    if (strpos($link, 'page-link prev') === false) {
                        echo '<li><span class="page-link prev page-numbers disabled">' . $prev_btn_label . '</span></li>';
                    }
                }
                echo "<li class='$menu_active'>$link</li>";
                if (($key + 1) == $numItems) {
                    if (strpos($link, 'page-link next') === false) {
                        echo '<li><span class="page-link next page-numbers disabled">' . $next_btn_label . '</span></li>';
                    }
                }
            endforeach;
            echo '</ul>';
        endif;
    }
}

if (!function_exists('create_layout')) {
    /**
     * @param array $display_elements
     * @param array $exclude_element
     * @param string $wrapper_class
     * @return array
     */
    function create_layout(array $display_elements, array $exclude_element, string $wrapper_class)
    {

        if (!is_array($exclude_element)) {
            $exclude_element = array($exclude_element);
        }
        $_display_elements = array_fill_keys($display_elements, "field");
        $_count = count($_display_elements);
        $inc = 1;
        $wrapper = true;
        $_open_wrapper = "<div class=\"$wrapper_class\">";
        $_close_wrapper = "</div>";
        $_wrapper_field_type = 'print';
        $_final_display_elements = array();

        foreach ($_display_elements as $field => $type) {
            if ($wrapper && !in_array($field, $exclude_element)) {
                $wrapper = false;
                $_final_display_elements[][$_open_wrapper] = $_wrapper_field_type;
            }

            if (!$wrapper && in_array($field, $exclude_element) && $inc != $_count) {
                $_final_display_elements[][$_close_wrapper] = $_wrapper_field_type;
            }

            if ($inc == $_count && in_array($field, $exclude_element)) {
                $_final_display_elements[][$_close_wrapper] = $_wrapper_field_type;
            }

            $_final_display_elements[][$field] = $type;

            if (!$wrapper && in_array($field, $exclude_element) && $inc != $_count) {
                $_final_display_elements[][$_open_wrapper] = $_wrapper_field_type;
            }

            if ($inc == $_count && !in_array($field, $exclude_element)) {
                $_final_display_elements[][$_close_wrapper] = $_wrapper_field_type;
            }

            $inc++;
        }

        return $_final_display_elements;
    }
}

if (!function_exists('get_post_link')) {
    /**
     * @param $_post_id
     * @param $anchor_appearance
     * @param string $read_more_text
     * @param string $fancyboxStyleClassName
     * @return array
     */
    function get_post_link($_post_id, $anchor_appearance, $read_more_text = "", $fancyboxStyleClassName = "")
    {
        if ($_post_id) {
            $_learn_more_type = get_post_meta($_post_id, 'learn_more_type', true);

            $_link = '';
            $_target = '_self';
            $_attributes = '';
            switch ($_learn_more_type) {
                case 'in_link':
                case 'ex_link':
                    $_learn_more_link = get_post_meta($_post_id, "learn_more_link", true);

                    $_show_popup = get_post_meta($_post_id, "show_popup", true);
                    $_id = array_by_keys('id.0', $_learn_more_link);
                    $_url = array_by_keys('url.0', $_learn_more_link);
                    $_type = array_by_keys('type.0', $_learn_more_link);
                    $_opensInNewTab = array_by_keys('opensInNewTab.0', $_learn_more_link);
                    $_fancy_box_data_type = "data-type='iframe'";


                    $_link = $_url;
                    if ($_type !== "URL") {
                        if ($_id) {
                            $_link = get_the_permalink($_id);
                        }
                    }

                    if ($_opensInNewTab) {
                        $_target = "_blank";
                    }

                    if ($_show_popup) {
                        $_disable_iframe = get_post_meta($_post_id, "disable_iframe", true);

                        if ($_disable_iframe) {
                            $_fancy_box_data_type = "";
                        }

                        $_attributes = " data-fancybox data-main-class='$fancyboxStyleClassName' ". $_fancy_box_data_type ." data-small-btn='true'";
                    }
                    break;
                case 'fi_link':
                    $_learn_more_link_file = get_post_meta($_post_id, 'learn_more_link_file', true);
                    $_gated_download = get_post_meta($_post_id, "gated_download", true);

                    if ($_gated_download == 'yes') {
                        $_target = '_self';
                        $_attributes = "data-fancybox data-main-class='$fancyboxStyleClassName'  data-src='#bs-post__form--$_post_id'";
                        $_link = "javascript:void(0)";
                    } else {
                        $_target = '_blank';
                        $_attributes = 'download';
                        $_link = wp_get_attachment_url($_learn_more_link_file);
                    }
                    break;
                case 'po_link':
                    $_link = "javascript:void(0)";
                    $_attributes = "data-fancybox data-main-class='$fancyboxStyleClassName' data-src='#bs-post__popup--$_post_id'";
                    $_target = '_blank';
                    break;
                case 'none':
                    $_link = "javascript:void(0)";
                    $_attributes = 'data-none="none"';
                    break;
                default:
                    $_link = get_the_permalink($_post_id);
                    break;
            }


            if ($anchor_appearance == 'full') :
                $_class = 'bs-post__trigger';
            else :
                $_class = 'bs-post__learn-more--link';
            endif;

            return array(
                'link' => "$_link",
                'class' => "$_class",
                'label' => "$read_more_text",
                'target' => "$_target",
                'attributes' => "$_attributes",
                'anchor_appearance' => "$anchor_appearance",
            );
        }
        return array();
    }
}

if (!function_exists('render_link')) {
    function render_link($anchor_type, $_link_attributes)
    {
        $btn = json_decode('{"name":"Learn More Text","button-style":{"type":"fill","fill":{"button-theme":"learn-more-text"},"outline":{"button-theme":"button"}},"button-block":false,"button-disabled":false,"button-size":"","button-type":{"type":"a","a":{"button-role":""},"button":{"button-behavior":"button"}},"button-icon":{"type":"none"},"button-icon-position":"before"}', true);
        $btnType = $btn['button-type']['type'];
        $iconElement = "";
        $btnAction = "";
        $isIconButton = 'none';
        $cssStyles = array();
        $attr = array();
        $_open = "";
        $_close = "";
        $return = "";

        // link_attributes
        $_id = (array_key_exists("id", $_link_attributes)) ? $_link_attributes['id'] : '';
        $_link = (array_key_exists("link", $_link_attributes)) ? $_link_attributes['link'] : 'javascript:void(0);';
        $_label = (array_key_exists("label", $_link_attributes)) ? $_link_attributes['label'] : '';
        $_class = (array_key_exists("class", $_link_attributes)) ? $_link_attributes['class'] : '';
        $_target = (array_key_exists("target", $_link_attributes)) ? $_link_attributes['target'] : '';
        $_attributes = (array_key_exists("attributes", $_link_attributes)) ? $_link_attributes['attributes'] : '';
        $_anchor_appearance = (array_key_exists("anchor_appearance", $_link_attributes)) ? $_link_attributes['anchor_appearance'] : '';

        // skip blank id
        $_id = ($_id) ? "id='$_id'" : '';
        $_target = ($_target) ? "target='$_target'" : '';

        // styles
        if ($_anchor_appearance == 'full' && $anchor_type != 'label') :
            array_push(
                $cssStyles,
                $_class
            );
        else :
            array_push(
                $cssStyles,
                'btn',
                $btn['button-style'][$btn['button-style']['type']]['button-theme'],
                $btn['button-block'] ? 'btn-block' : '',
                $btn['button-disabled'] ? 'disabled' : '',
                $btn['button-size'],
                $_class
            );
        endif;

        // attributes
        array_push(
            $attr,
            $btn['button-disabled'] ? 'disabled' : '',
            ($btnType == "a") ? "href='$_link'" : "",
            $_target,
            $_attributes,
            $_id
        );

        if ($isIconButton != "none") {
            // $iconElement = $this->btn_icon->render($btn["button-icon"]);
            $iconElement = '';
        }

        $cssStylesList = join(' ', array_filter($cssStyles));
        $attrList = join(' ', array_filter($attr));


        $_open = "<$btnType class=\"$cssStylesList\" $attrList>";

        // icon before option
        if ($btn['button-icon-position'] == "before" && $isIconButton != "none" && $_anchor_appearance != 'full') {
            $_open .= $iconElement;
        }

        // icon after option
        if ($btn['button-icon-position'] == "after" && $isIconButton != "none" && $_anchor_appearance != 'full') {
            $_close = $iconElement;
        }

        $_close .= "</$btnType>";

        switch ($anchor_type) {
            case 'open':
                $return = $_open;
                break;
            case 'close':
                $return = $_close;
                break;
            case 'full':
                $return = $_open . $_label . $_close;
                break;
            case 'label':
                $return = "<span class='$cssStylesList'>" . $_label . "</span>";
                break;
        }

        return $return;
    }
}

if (!function_exists('render_image')) {
    /**
     * @return string
     */
    function render_image($post_thumbnail_id, $is_featured = false)
    {

        //placeholder will apear only allowed function calls
        if ($post_thumbnail_id) {
            $image_data = array();
            $attachment = get_post($post_thumbnail_id);

            $image_url = wp_get_attachment_url($attachment->ID);
            $image_alt = get_post_meta(get_the_ID(), 'image_alt_text', true) ?? get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
            $image_caption = $attachment->post_excerpt;
            $image_show_caption = true;
            $image_title = $attachment->post_title;
            $image_type = 'img-fluid';
            $lazy_loading_attribute = (!metadata_exists('post', get_the_ID(), 'enable_lazy_loading') || get_post_meta(get_the_ID(), 'enable_lazy_loading', true)) ? 'loading="lazy"' : null;
            $img_attr = "src='$image_url'";
            $img_class = ($is_featured) ? "featured-image" : "image";
            $img_attr .= " class='$image_type' alt='$image_alt' title='$image_title' $lazy_loading_attribute";
            $common_prefix = 'bs-post__';
            $mask_prefix = $common_prefix . 'mask';

            // extention catch
            $ext = pathinfo($image_url, PATHINFO_EXTENSION);
            $data_gif = ($ext == "gif") ? 'data-' . $ext . '= "' . $image_url . '"' : '';

            $element = "";
            $_figure_caption_data = "";

            if ($image_show_caption == "yes") {
                $_figure_caption_data = "<figcaption class='figure-caption'>$image_caption</figcaption>";
            }

            $element .= "<div class='" . $common_prefix . $img_class."'>
                            <figure class='figure'>
                                <img $img_attr $data_gif />
                                $_figure_caption_data
                            </figure>
                        </div>";

            return $element;
        }
        return false;
    }
}

if (!function_exists('get_background')) {
    /**
     * @param $_post_id
     * @param $image_display_format
     * @param $image_field
     * @param bool $popup
     * @return array
     */
    function get_background($_post_id, $image_display_format, $image_field, $popup = false, $showPlaceholderImage = false, $placeholderImage = '')
    {
        $bg_img_status = false;
        $bg_img = '';
        $bg_img_class = '';

        if ($image_display_format == 'background') {
            if ($image_field == 'image') {
                $bg_img_status = true;
                $bg_img_class = ' bs-bg-image';
                if (has_post_thumbnail($_post_id)) {
                    $bg_img = array_by_keys('0', wp_get_attachment_image_src(get_post_thumbnail_id($_post_id), $size = 'full'));
                } else {
                    if ($placeholderImage && $showPlaceholderImage) {
                        $bg_img = array_by_keys('0', wp_get_attachment_image_src($placeholderImage, $size = 'full'));
                    }
                }
            }
            if ($image_field == 'meta_featured_image') {
                $bg_img_status = true;
                $bg_img_class = ' bs-bg-image';
                if (get_post_meta(get_the_ID(), 'featured_image', true)) {
                    $bg_img = array_by_keys('0', wp_get_attachment_image_src(get_post_meta(get_the_ID(), 'featured_image', true), $size = 'full'));
                } else {
                    if ($placeholderImage && $showPlaceholderImage) {
                        $bg_img = array_by_keys('0', wp_get_attachment_image_src($placeholderImage, $size = 'full'));
                    }
                }
            }
        }

        if (!trim($bg_img)) {
            $bg_img_status = false;
            $bg_img = '';
            $bg_img_class = '';
        } else {
            if ($popup) {
                $bg_img = ' style="background-image: url(' . $bg_img . '); display: none;"';
            } else {
                $bg_img = ' style="background-image: url(' . $bg_img . ');"';
            }
        }

        return array(
            'bg_img_status' => $bg_img_status,
            'bg_img' => $bg_img,
            'bg_img_class' => $bg_img_class
        );
    }
}

if (!function_exists('humanDateRanges')) {
    /**
     * @param $event_start_date
     * @param $event_end_date
     * @return string
     */
    function humanDateRanges($event_start_date, $event_end_date)
    {
        $startTime = strtotime($event_start_date);
        $endTime = strtotime($event_end_date);

        if (date('Y', $startTime) != date('Y', $endTime)) {
            $result = date('M j, Y', $startTime) . " - " . date('M j, Y', $endTime);
        } else {
            if ((date('j', $startTime) == 1) && (date('j', $endTime) == date('t', $endTime))) {
                $result = date('M', $startTime) . " - " . date('M, Y', $endTime);
            } else {
                if (date('m', $startTime) != date('m', $endTime)) {
                    $result = date('M j', $startTime) . " - " . date('M j, Y', $endTime);
                } else {
                    if (date('d', $startTime) == date('d', $endTime)) {
                        $result = date('M j, Y', $startTime);
                    } else {
                        $result = date('M j', $startTime) . " - " . date('j, Y', $endTime);
                    }
                }
            }
        }
        return apply_filters('berg_event_posts_human_date_range_filter', $result, $startTime, $endTime);
    }
}
