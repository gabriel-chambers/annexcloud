</main>
<footer class="footer" role="contentinfo">
    <div class="container">
        <div class="row">
            <div>
                <!--  Footer Logo -->
                <a class="navbar-logo" href="<?php echo get_home_url(); ?>"
                   title="<?php echo get_bloginfo( 'name' ); ?>">
					<?php
					$footer_logo_url = get_theme_mod( 'footer_logo' );
					if ( ! empty( $footer_logo_url ) ) :?>
                        <img src="<?php echo $footer_logo_url; ?>" alt='<?php echo get_bloginfo( 'name' ); ?>'
                             class="footer-logo">
					<?php else: ?>
                        <h1><?php echo get_bloginfo( 'name' ); ?></h1>
					<?php endif; ?>
                </a>
            </div>
            <div>
                <!--  Footer Copyright -->
				<?php
				$copyright = get_theme_mod( 'copyright' );
				if ( ! empty( $copyright ) ) :?>
                    <p>&copy; <?php echo date( 'Y' ) . ' ' . $copyright ?></p>
				<?php else: ?>
                    <p>&copy; <?php echo date( 'Y' ) ?> <?php echo get_bloginfo( 'name' ); ?></p>
				<?php endif; ?>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>