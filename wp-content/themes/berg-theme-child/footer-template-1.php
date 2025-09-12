<?php
$footer_logo_url = get_theme_mod('footer_logo');
$footer_logo_text = get_theme_mod('footer_logo_text');

$footer_subcribe_text = get_theme_mod('footer_subcribe_text');
$footer_form_code = get_theme_mod('footer_form_code');

$copyright = get_theme_mod('copyright') ?: get_bloginfo('name');

$social = array(
  'podcast'  => array( get_theme_mod('footer_social_pc'), get_theme_mod('footer_social_pc_logo') ),
  'linkedin' => array( get_theme_mod('footer_social_in'), get_theme_mod('footer_social_in_logo') ),
  'youtube'  => array( get_theme_mod('footer_social_yt'), get_theme_mod('footer_social_yt_logo') ),
);
?>

<footer class="footer" role="contentinfo">
  <div class="container">
    <!-- Main Content -->
    <div class="footer__row">
      <div class="footer__column">
        <div class="footer__branding">
          <a href="<?php echo get_home_url(); ?>" title="<?php echo get_bloginfo('name'); ?>">
            <?php echo !empty($footer_logo_url)
              ? '<img src="'.$footer_logo_url.'" alt="'.get_bloginfo('name').'" class="footer-logo">'
              : '<h4>'.get_bloginfo('name').'</h4>'; ?>
          </a>
        </div>

        <?php echo !empty($footer_logo_text)
          ? '<div class="footer__description"><p>'.$footer_logo_text.'</p></div>'
          : false ?>

        <div class="footer__social">
          <?php foreach ($social as $key => $value) : ?>
            <?php if(!empty($value[0])) : ?>
              <a href="<?php echo $value[0]?>"
                target="_blank"
                class="footer--social-icon <?php echo $key?>-social"
                rel="noopener">
                  <?php echo (!empty($value[1]))
                    ? '<img src="'.$value[1].'" alt="'.$key.'-logo" class="'.$key.'-logo">'
                    : '<span class="icon-'.$key.'"></span>'; ?>
              </a>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>

        <?php if ($footer_subcribe_text) { ?>
            <div class="footer__subcribe-text">
                <p><?php echo $footer_subcribe_text; ?></p>
            </div>
        <?php } ?>
        <?php if ($footer_form_code) { ?>
            <div class="footer__form-code">
                <p><?php echo $footer_form_code; ?></p>
            </div>
        <?php } ?>
      </div>

      <?php foreach (['one', 'two', 'three'] as $menu) : ?>
        <div class="footer__column footer__column--main-menu">
          <?php wp_nav_menu(['theme_location' => 'footer-menu-'.$menu, 'menu_class' => 'navbar-nav ']); ?>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Bottom Content -->
    <div class="footer__row">
      <div class="footer__copyright">
          <p class="copyright">&copy; <?php echo date('Y') . ' ' . $copyright; ?></p>
      </div>

      <div class="footer__bottom-menu">
          <?php wp_nav_menu(['theme_location' => 'bottom-menu', 'menu_class' => 'navbar-nav']); ?>
      </div>
    </div>
  </div>
</footer>
