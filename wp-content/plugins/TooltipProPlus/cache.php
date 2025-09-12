<?php

use CM\CMTT_Settings;

class CMTT_Cache {

    public static function init() {
        add_action('admin_enqueue_scripts',[__CLASS__,'enqueue_pre_cache_script']);
    }

    public static function get_cache($key,$group=''){
        $cached_content = wp_cache_get($key, $group);
        if ( !empty($cached_content)) {
            return $cached_content;
        }
        $cached_content = get_transient($key);
        return $cached_content;
    }

    public static function set_cache($value,$key,$group='',$expire = 0){
        $days_to_expire = (int) \CM\CMTT_Settings::get( 'cmtt_glossaryCachingExpiration', 30 );
        $expire = $expire ?? 60 * 60 * 24 * $days_to_expire;
        wp_cache_set( $key, $value, 'glossary_filtered', $expire );
        set_transient( $key, $value, $expire );
    }

    public static function delete_cache($key,$group=''){
        wp_cache_delete( $key, $group );
        delete_transient( $key );
    }

    public static function enqueue_pre_cache_script() {
        global $typenow;

        $jsPath   = plugin_dir_url( __FILE__ ) . 'assets/js/';

        $screen = get_current_screen();
        $defaultPostTypes         = \CM\CMTT_Settings::get( 'cmtt_allowed_terms_metabox_all_post_types' ) ? get_post_types() : array(
            'post',
            'page',
        );
        $allowedTermsBoxPostTypes = apply_filters( 'cmtt_allowed_terms_metabox_posttypes', $defaultPostTypes );
        if ( (! in_array( $typenow, $allowedTermsBoxPostTypes ) || !$screen || $screen->base !== 'post') && $typenow !== 'glossary') {
            return;
        }
        if ( \CM\CMTT_Settings::get('cmtt_glossaryEnablePreCaching', false) && \CM\CMTT_Settings::get('cmtt_glossaryEnableCaching', false) ) {
            wp_enqueue_script('cmtt-pre-cache-update-js', $jsPath . 'tooltip-pre-cache.js', array('wp-blocks', 'wp-element', 'wp-data', 'wp-components', 'wp-editor', 'jquery'));
            $index_page = CMTT_Glossary_Index::getGlossaryIndexPageId();
            $index_permalink = $index_page ? get_permalink($index_page) : '';
            $letters = [];
            if(\CM\CMTT_Settings::get('cmtt_index_enabled')) {
                $letters = (array)\CM\CMTT_Settings::get('cmtt_index_letters');
                $letters = apply_filters('cmtt_index_letters', $letters, [], false);
            }
            wp_localize_script('cmtt-pre-cache-update-js', 'cmtt_pre_cache', ['index_permalink' => $index_permalink,'letters' => $letters]);
        }
    }
}