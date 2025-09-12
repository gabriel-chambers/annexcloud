<?php
$addons = $currentPlugin->getOption('plugin-addons');
$specials = $currentPlugin->getOption('plugin-specials');
$bundles = $currentPlugin->getOption('plugin-bundles');
$services = $currentPlugin->getOption('plugin-services');
$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
?>
<style>
section.cm { width: 100%; display: block; }
.cm .cmlp-box.postbox.addonbox { width:35%; min-height:135px; margin: 20px 12px 20px 8px; display: inline-block; padding: 15px; background-color: #fff; border-radius: 10px; vertical-align: top; }
.cm .cmlp-box.postbox.addonbox .cmlp-img { width:20%; float:left; margin-right:5%;}
.cm .cmlp-box.postbox.addonbox .cmlp-img img { width:100%; }
.cm .cmlp-box.postbox.addonbox .cmlp-inside { display: block; margin-left:2%; width:100%; float:left; padding:0; margin:0; }
.cm .cmlp-box.postbox.addonbox .cmlp-inside h4 { color:#333; font-weight:600; margin-top:0px; margin-bottom:10px; text-align:center; }
.cm .cmlp-box.postbox.addonbox .cmlp-inside span { color:#333; line-height:1.5; font-size:14px; }
.cm .cmlp-box.postbox.addonbox .cmlp-inside .buttons { position:absolute; bottom:0px; right:20px; }
.cm .cmlp-box.postbox.addonbox .cmlp-inside .buttons label { cursor:pointer; }
.cm .cmlp-box.addonbox .button-success, .cm .cmlp-box.addonbox .button-success:focus { font-size:14px; font-weight:bold; display:block; color:#135e96; text-decoration:none; }
.cm .cmlp-top {text-decoration:none; }
.cm .cmlp-top:focus { box-shadow:none; }
.cm .cmlp-box {border: 2px solid #ccc;}
.cm .cmlp-box:hover {border: 2px solid #333; border-color: #135e96;}
</style>
<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<a href="?page=cmtt_addons&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">Tooltip Glossary Add-ons</a>
		<a href="?page=cmtt_addons&tab=advanced" class="nav-tab <?php echo $active_tab === 'advanced' ? 'nav-tab-active' : ''; ?>">Discounted Premium Plugins</a>
		<a href="?page=cmtt_addons&tab=extra" class="nav-tab <?php echo $active_tab === 'extra' ? 'nav-tab-active' : ''; ?>">Plugin Bundles</a>
		<a href="?page=cmtt_addons&tab=service" class="nav-tab <?php echo $active_tab === 'service' ? 'nav-tab-active' : ''; ?>">Web Services</a>
	</h2>
	<div class="tab-content">
		<?php
		if ($active_tab === 'general') {
			?>
			<br><h2 style="padding-left:10px;">Extend the functionality of the Tooltip Glossary plugin with additional add-ons.</h2>
			<section id="" class="cm">
				<?php
				foreach ( $addons as $value ) : ?>
					<?php if ( esc_attr( $value[ 'color' ] ) == "") { ?>
					<div class="cmlp-box postbox addonbox">
						<?php } else { ?>
					  <div class="cmlp-box postbox addonbox" style="background:<?php echo esc_attr( $value[ 'color' ] ); ?>">
						<?php } ?>
						<a class="cmlp-top" href="<?php echo esc_attr( $value[ 'link' ] ); ?>" target="_blank">
							<div class="cmlp-inside">
								<h4><span><?php echo esc_attr( $value[ 'title' ] ); ?></span></h4>
								<div class="cmlp-img">
								<img src="<?php echo esc_attr( $value[ 'image' ] ); ?>" alt="<?php echo esc_attr( $value[ 'title' ] ); ?>" />
								</div>
								<span><?php echo esc_attr( $value[ 'description' ] ); ?></span>
								<div class="buttons">
									<label class="button-success">More Details</label>
								</div>
							</div>
						</a>
					</div>
					<?php
				endforeach;
				?>
			</section>
			<?php
		} elseif ($active_tab === 'advanced') {
			?>
			<br><h2 style="padding-left:10px;">Save 10% on the following premium plugins! Use code <span style="color: red; font-weight: bold;">CMINDS10</span> at checkout.</h2>
			<section id="" class="cm">
				<?php
				foreach ( $specials as $value ) : ?>
					<?php if ( esc_attr( $value[ 'color' ] ) == "") { ?>
					<div class="cmlp-box postbox addonbox">
						<?php } else { ?>
					  <div class="cmlp-box postbox addonbox" style="background:<?php echo esc_attr( $value[ 'color' ] ); ?>">
						<?php } ?>
						  <a  class="cmlp-top" href="<?php echo esc_attr( $value[ 'link' ] ); ?>" target="_blank">
							<div class="cmlp-inside">
							   <h4><span><?php echo esc_attr( $value[ 'title' ] ); ?></span></h4>
							   <div class="cmlp-img">
								<img src="<?php echo esc_attr( $value[ 'image' ] ); ?>" alt="<?php echo esc_attr( $value[ 'title' ] ); ?>" />
							</div>
							   <span><?php echo esc_attr( $value[ 'description' ] ); ?></span>
								<div class="buttons">
									<label class="button-success">More Details</label>
								</div>
							</div>
						</a>
					</div>
					<?php
				endforeach;
				?>
			</section>
			<?php
		} elseif ($active_tab === 'extra') {
			?>
			<br><h2 style="padding-left:10px;">Get the best value with our plugin bundles - combine and save!</h2>
			<section id="" class="cm">
				<?php
				foreach ( $bundles as $value ) : ?>
					<?php if ( esc_attr( $value[ 'color' ] ) == "") { ?>
					<div class="cmlp-box postbox addonbox">
						<?php } else { ?>
					  <div class="cmlp-box postbox addonbox" style="background:<?php echo esc_attr( $value[ 'color' ] ); ?>">
						<?php } ?>
						 <a  class="cmlp-top" href="<?php echo esc_attr( $value[ 'link' ] ); ?>" target="_blank">
							<div class="cmlp-inside">
							   <h4><span><?php echo esc_attr( $value[ 'title' ] ); ?></span></h4>
							   <div class="cmlp-img">
								<img src="<?php echo esc_attr( $value[ 'image' ] ); ?>" alt="<?php echo esc_attr( $value[ 'title' ] ); ?>" />
								</div>
							   <span><?php echo esc_attr( $value[ 'description' ] ); ?></span>
								<div class="buttons">
									<label class="button-success">More Details</label>
								</div>
							</div>
						</a>
					</div>
					<?php
				endforeach;
				?>
			</section>
			<?php
		} elseif ($active_tab === 'service') {
			?>
			<br><h2 style="padding-left:10px;">Need customization, setup, or expert advice? Hire our WordPress specialists!</h2>
			<section id="" class="cm">
				<?php
				foreach ( $services as $value ) : ?>
					<?php if ( esc_attr( $value[ 'color' ] ) == "") { ?>
					<div class="cmlp-box postbox addonbox">
						<?php } else { ?>
					  <div class="cmlp-box postbox addonbox" style="background:<?php echo esc_attr( $value[ 'color' ] ); ?>">
						<?php } ?>
						 <a  class="cmlp-top" href="<?php echo esc_attr( $value[ 'link' ] ); ?>" target="_blank">
							<div class="cmlp-inside">
							   <h4><span><?php echo esc_attr( $value[ 'title' ] ); ?></span></h4>
							   <div class="cmlp-img">
								<img src="<?php echo esc_attr( $value[ 'image' ] ); ?>" alt="<?php echo esc_attr( $value[ 'title' ] ); ?>" />
								</div>
							   <span><?php echo esc_attr( $value[ 'description' ] ); ?></span>
								<div class="buttons">
									<label class="button-success">More Details</label>
								</div>
							</div>
						</a>
					</div>
					<?php
				endforeach;
				?>
			</section>
			<?php
		}
		?>
	</div>
</div>