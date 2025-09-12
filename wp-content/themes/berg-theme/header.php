<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5"/>

    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <title><?php wp_title( ' | ', true, 'right' ); ?></title>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="nav" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light ustify-content-end">
            <a class="navbar-logo" href="<?php echo get_home_url(); ?>" title="<?php echo get_bloginfo( 'name' ); ?>">
                <!--  Primary Logo -->
				<?php
				$header_logo_id = get_theme_mod( 'custom_logo' );
				if ( ! empty( $header_logo_id ) ) :?>
					<?php $logo_url = wp_get_attachment_url( $header_logo_id ); ?>
                    <img src="<?php echo $logo_url; ?>" alt='<?php echo get_bloginfo( 'name' ); ?>'
                         class="inactive-logo">
				<?php else: ?>
                    <h1><?php echo get_bloginfo( 'name' ); ?></h1>
				<?php endif; ?>

                <!-- Secondary Logo -->
				<?php
				$secondary_header_logo_url = get_theme_mod( 'secondary_logo' );
				if ( ! empty( $secondary_header_logo_url ) ) :?>
                    <img src="<?php echo $secondary_header_logo_url; ?>" alt='<?php echo get_bloginfo( 'name' ); ?>'
                         class="active-logo">
				<?php endif; ?>
            </a>
            <button role="button" aria-label="Navbar Toggler" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-grow-0 ml-auto mr-1" id="navbarSupportedContent">
                <!--  load main menu here -->
				<?php //wp_nav_menu(array('theme_location' => 'main-menu', 'walker' => new wp_bootstrap_navwalker(), 'menu_class' => 'navbar-nav mr-auto mt-2 mt-lg-0')); ?>
            </div>
        </nav>
    </div>
</header>
<main role="main">

