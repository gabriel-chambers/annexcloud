<?php

class CMTT_Archive {
    public static function init() {
        add_action('cmtt_save_options_after_on_save', [__CLASS__, 'afterSaveSettings'], 100, 2);
        add_shortcode('glossary-archive',[__CLASS__,'glossary_archive_shortcode']);
    }

    public static function afterSaveSettings($post, $messages){
        self::tryGenerateGlossaryArchivePage();
        unset($post['cmtt_glossaryArchiveID']);
    }

    public static function tryGenerateGlossaryArchivePage() {
        $glossaryArchiveId = self::getGlossaryArchivePageId();
        if ( -1 == $glossaryArchiveId ) {
            $id = wp_insert_post(
                array(
                    'post_author'  => get_current_user_id(),
                    'post_status'  => 'publish',
                    'post_title'   => 'Glossary Archive',
                    'post_type'    => 'page',
                    'post_content' => '[glossary-archive]',
                )
            );
            if ( is_numeric( $id ) ) {
                \CM\CMTT_Settings::set( 'cmtt_glossaryArchiveID', $id );
            }
        }
    }

    public static function getGlossaryArchivePageId() {
        $glossaryPageID = apply_filters( 'cmtt_get_glossary_archive_page_id', \CM\CMTT_Settings::get( 'cmtt_glossaryArchiveID', -1 ) );
        /*
         * WPML integration
         */
        if ( function_exists( 'icl_object_id' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
            $glossaryPageID = icl_object_id( $glossaryPageID, 'page', ICL_LANGUAGE_CODE );
        }

        return $glossaryPageID;
    }

    public static function glossary_archive_shortcode(){
        $query_letter = $_GET['letter'] ?? 'all';
        $current_page = $_GET['page_num'] ?? 1;
        $args = array(
            'post_type' => 'glossary',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'suppress_filters' => false,
        );

        $filter = ['letter' => $query_letter];
        $glossary_index = CMTT_Free::getGlossaryItems($args, 'index', $filter);
        $posts_per_page = 10;

        usort($glossary_index, array('CMTT_Glossary_Index', 'compareGlossaryTerms'));
        $sortByTitle = \CM\CMTT_Settings::get('cmtt_index_sortby_title', 'title');
        $nonLatinLetters = (bool)\CM\CMTT_Settings::get('cmtt_index_nonLatinLetters');
        $termContentWordslimit = \CM\CMTT_Settings::get('cmtt_archive_content_words_limit',55);

        if ( !empty($query_letter) && $query_letter !== 'all' ) {
            $glossary_index = array_filter($glossary_index, function ($glossaryItem) use ($filter, $sortByTitle, $nonLatinLetters) {
                $letter = CMTT_Free::getFirstLetter($glossaryItem, $sortByTitle);
                if ( !$nonLatinLetters ) {
                    $letter = remove_accents($letter);
                }

                return $filter['letter'] === $letter;
            });
        }
        $total_posts = count($glossary_index);
        $glossary_index = array_values($glossary_index);
        $glossary_index = array_slice($glossary_index, ($current_page - 1) * $posts_per_page, $posts_per_page);
        echo '<div class="glossary-archive">';
        foreach($glossary_index as $item){
            $thumbnail = get_the_post_thumbnail(
                $item->ID,
            );
            $glossary_path    = plugin_dir_path( __FILE__ );
            $theme_path       = get_stylesheet_directory();
            ob_start();
            if ( file_exists( $theme_path . '/Tooltip/archive-item.php' )  ) {
                require $theme_path . '/Tooltip/archive-item.php';
            } elseif ( file_exists( $glossary_path . 'theme/Tooltip/archive-item.php' ) ) {
                require $glossary_path . 'theme/Tooltip/archive-item.php';
            }
            $item_html = ob_get_clean();
            echo $item_html;
        }
        echo '<div class="pagination">' . paginate_links([
            'base' => get_permalink() . '%_%',
            'format' =>  $query_letter == 'all' ? '?page_num=%#%' : '?letter='.$query_letter.'&page_num=%#%',
            'total' => ceil($total_posts / $posts_per_page),
            'current' => $current_page,
        ]);
        echo  '</div></div>';
    }

}