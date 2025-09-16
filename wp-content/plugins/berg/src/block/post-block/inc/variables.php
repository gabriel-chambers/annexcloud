<?php

/**
 * @var string $dateFormat
 * @var string $orderBy
 * @var string $order
 * @var number $titleCharLimit
 * @var boolean $showPlaceholderImage
 * @var number $placeholderImage
 * @var string $postText
 * @var string $postFrom
 * @var string $postType
 * @var string $selectedPosts
 * @var array $filterByType
 * @var boolean $showFilters
 * @var boolean $multiselect
 * @var string $filterLabel
 * @var array $filters
 * @var array $displayOrders
 * @var string $imageAppearance
 * @var string $backgroundImageField
 * @var array $popupDisplayOrders
 * @var string $popupImageAppearance
 * @var string $popupBackgroundImageField
 * @var array $featuredDisplayOrders
 * @var string $featuredImageAppearance
 * @var string $featuredBackgroundImageField
 * @var string $anchorElementAppearance
 * @var string $noEntriesFoundText
 * @var string $blockType
 * @var string $postLayout
 * @var string $showFeatured
 * @var string $featuredText
 * @var string $showDefaultFeatured
 * @var string $showFeaturedFirst
 * @var string $featuredNum
 * @var string $postsInXs
 * @var string $postsInSm
 * @var string $postsInMd
 * @var string $postsInLg
 * @var string $postsInXl
 * @var string $loadType
 * @var string $postsNumberFirstLoad
 * @var string $postsPerPage
 * @var string $prevLabel
 * @var string $nextLabel
 * @var string $loadMoreLabel
 * @var string $showAllOption
 * @var string $search_text
 * @var string $tax_select_filters
 * @var array $featured_ids
 * @var boolean $ajax
 * @var number $paged
 * @var string $postBlockClass
 * @var array $layoutDisplayOrders
 * @var boolean $featuredPostSlider
 * @var boolean $displayFeaturedPostOnPostGrid
 * @var object $featuredSliderSettings
 * @var string $fancyboxStyleClassName
 */
extract($attributes);

$is_featured = false;
$filters = get_array_values($filters);
$posts_blocks_class = uniqid('bs-post-');

/* Initial 'paged' value is dynamic only for 'pagination' enabled blocks. This is to avoid 
conflicting pagination values when having multiple post blocks in a single page */
$paged = ($attributes['loadType'] == 'pagination' && get_query_var('paged')) ? get_query_var('paged') : 1;

$uniqidID = uniqid('base_2_post_blocks_');

$_col_xm = "col-" . 12 / $postsInXs;
$_col_sm = "col-sm-" . 12 / $postsInSm;
$_col_md = "col-md-" . 12 / $postsInMd;
$_col_lg = "col-lg-" . 12 / $postsInLg;
$_col_xl = "col-xl-" . 12 / $postsInXl;
$_grid_class = '';
$_featured_grid_class = '';
$_featured = false;
$featured_ids = array();

switch ($postLayout) {
    case 'content-grid':
        if ($showFeatured) {
            if ($showFeaturedFirst) {
                if ($paged == 1) {
                    $_featured = true;
                }
            } else {
                $_featured = true;
            }
        }
        break;

    case 'content-list':
        $_col_xm = "col-12";
        $_col_sm = "col-sm-12";
        $_col_md = "col-md-12";
        $_col_lg = "col-lg-12";
        $_col_xl = "col-xl-12";
        if ($showFeatured) {
            if ($showFeaturedFirst) {
                if ($paged == 1) {
                    $_featured = true;
                }
            } else {
                $_featured = true;
            }
        }
        break;

    case 'content-filter':
        $_grid_class = "grid-item";
        $postsPerPage = -1;
        $showFeatured = false;
        break;
}

$display_order = create_layout(get_array_values($displayOrders), array('image', 'meta_featured_image'), 'bs-post__details');
$popup_display_order = create_layout(array_diff(get_array_values($popupDisplayOrders), array('more')), array('image', 'meta_featured_image'), 'bs-post__details');
$featured_display_order = create_layout(get_array_values($featuredDisplayOrders), array('image', 'meta_featured_image'), 'bs-post__details');
