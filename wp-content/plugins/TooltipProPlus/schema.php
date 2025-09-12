<?php

class CMTT_Schema {

    private static $ld_schema = array();

    public static function init($posts = null) {
        static::reset_schema();
        add_action( 'wp_head', array(__CLASS__, 'print_schema') );
    }

    private static function reset_schema() {
        static::$ld_schema = array(
            '@id'        => '',
            '@context'   => 'https://schema.org',
            '@type'      => 'ItemPage',
            'name'       => '',
            'description'=> '',
            'accessMode' => 'textual, visual',
            'url'        => ''
        );
    }

    private static function add_schema_content($name, $desc, $url) {
        static::$ld_schema = array(
            '@id'        => $url . '#ItemPage',
            '@context'   => 'https://schema.org',
            '@type'      => 'ItemPage',
            'name'       =>  htmlspecialchars($name),
            'description'=> htmlspecialchars(do_shortcode($desc)),
            'accessMode' => 'textual, visual',
            'url'        => $url
        );

    }

    private static function get_schema() {
        global $post;
        if (isset($post->ID) && $post->post_type == 'glossary' ){
            $url = get_permalink($post->ID);
            $title = apply_filters('cmtt_schema_title', $post->post_title, $post);
            $description = apply_filters('cmtt_schema_description', $post->post_content, $post);
            self::add_schema_content($title, $description, $url);
            return static::$ld_schema;
        }
        return '';
    }

    public static function print_schema() {
        if (\CM\CMTT_Settings::get('cmtt_add_structured_data_term_page', 1)){
            $schema = static::get_schema();
            if ( !empty($schema) ) {
                $schema = json_encode($schema);

                echo "<script type='application/ld+json'>\n";
                echo stripslashes($schema);
                echo "</script>\n";
            }
        }
    }
}
