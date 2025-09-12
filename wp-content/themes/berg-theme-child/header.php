<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5"/>
    <?php $fav_path = get_stylesheet_directory_uri() . '/assets/images/favicon' ?>
    <link rel="apple-touch-icon" sizes="57x57" href="<?php echo $fav_path; ?>/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?php echo $fav_path; ?>/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $fav_path; ?>/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?php echo $fav_path; ?>/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $fav_path; ?>/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?php echo $fav_path; ?>/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $fav_path; ?>/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo $fav_path; ?>/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $fav_path; ?>/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo $fav_path; ?>/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $fav_path; ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo $fav_path; ?>/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $fav_path; ?>/favicon-16x16.png">
    <link rel="manifest" href="<?php echo $fav_path; ?>/manifest.json">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php
$googleFontsURL = 'https://fonts.googleapis.com/css2?family='.
                'DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&family=Plus+Jakarta+Sans:ital,'.
                'wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800'.
                '&display=swap';
?>
<link href="<?php echo $googleFontsURL; ?>" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400&display=swap" rel="stylesheet">

<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">
<title><?php wp_title( ' | ', true, 'right' ); ?></title>
<?php
    echo get_theme_mod('preconnect') . PHP_EOL;
?>
<?php wp_head(); ?>
<?php
echo get_theme_mod('header_script_one');
echo get_theme_mod('header_script_two');
echo get_theme_mod('header_script_three');

if($_SERVER['HTTP_HOST'] == 'anxc-stg.e25.xyz'){
    echo '<script type="text/javascript"
        src="https://www.bugherd.com/sidebarv2.js?apikey=aqvoutpcv1nm6kge33xxsw"
        async="true"></script>';
}
?>
</head>
<?php
/*
 * Get term slug from resource post type
 */
$catSlug = $dataCat = "";
if (is_single() && $post->post_type == "resource") {
    $terms = get_the_terms(get_queried_object_id(), 'resource-category');
    if (!empty($terms)) {
        $term = array_shift($terms); // get the first term
        $catSlug = $term->slug;
        $dataCat = "data-cat=" . trim($catSlug);
    }
}
?>
<body <?php body_class(); ?> <?php echo $dataCat; ?>>
<?php echo get_theme_mod('body_script'); ?>
<header class="nav" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar__logo" href="<?php echo get_home_url(); ?>" title="<?php echo get_bloginfo('name'); ?>">
    <!--  Primary Logo -->
    <?php
    $header_logo_id = get_theme_mod('custom_logo');
    if (!empty($header_logo_id)) : ?>
        <?php $logo_url = wp_get_attachment_url($header_logo_id); ?>
        <img src="<?php echo $logo_url; ?>" alt='<?php echo get_bloginfo('name'); ?>' class="inactive-logo">
    <?php else : ?>
        <h1><?php echo get_bloginfo('name'); ?></h1>
    <?php endif; ?>

    <!-- Secondary Logo -->
    <?php
    $secondary_header_logo_url = get_theme_mod('secondary_logo');
    if (!empty($secondary_header_logo_url)) : ?>
        <img src="<?php echo $secondary_header_logo_url; ?>" alt="<?php echo get_bloginfo('name'); ?>"
        class="active-logo">
    <?php endif; ?>
</a>
            <button
                role="button"
                aria-label="Navbar Toggler"
                class="navbar__toggler collapsed"
                type="button"
                data-toggle="collapse"
                data-target="#navbarSupportedContent">
                <span class="navbar__toggler-icon"></span>
            </button>
            <div class="navbar__inner">
                <div class="navbar__search-popup">
                    <div class="search-wrapper">
                        <form role="search"
                              method="get"
                              id="searchform"
                              class="searchform"
                              action="<?php echo esc_url(home_url('/')); ?>">
                            <div class="input-wrapper">
                                <input type="text" value="" name="s" id="s"
                                       placeholder="Search..." autocomplete="off"/>
                                <input type="submit" id="searchsubmit"
                                       value="<?php echo esc_attr_x('Search', 'submit button'); ?>"/>
                            </div>
                        </form>
                        <span class="search-icon close-icon"></span>
                    </div>
                </div>
                <div class="collapse navbar__collapse flex-grow-0 ml-auto" id="navbarSupportedContent">
                    <!--  load main menu here -->
                    <?php wp_nav_menu(
                        array(
                            'theme_location' => 'main-menu',
                            'menu_class' => 'navbar-nav mr-auto mt-2 mt-lg-0'
                        )
                    ); ?>
                    <div class="navbar__buttons">
                        <?php wp_nav_menu(array('theme_location' => 'login-menu')); ?>
                        <span class="search-icon desktop-search-trigger"></span>
                        <?php wp_nav_menu(array('theme_location' => 'request-demo-menu')); ?>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>
<main role="main" <?php echo $dataCat; ?> class="<?php echo $catSlug; ?>">
