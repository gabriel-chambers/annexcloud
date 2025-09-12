<?php
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
* Get Options
*/
if( !function_exists('eppc_options') ){
	function eppc_options( $key = ''){
		$options = get_option( 'eppc_options' );
		$return_val = false;
		$key = sanitize_key($key);
		if(isset($options[$key])){
			if( !empty($options[$key]) && is_array($options[$key]) ){
				$return_val =  array_map(function($val){
					return sanitize_text_field($val);
				},$options[$key]);
			}else{
				$return_val = !empty($options[$key]) ? sanitize_text_field($options[$key]) : '';
			}
		}
		return $return_val;
		
	}
}// end

/**
* kses allowed html
*/
if( !function_exists('eppc_allowed_html') ){
	function eppc_allowed_html( $allowed_tags = 'all' ){
		$allowed_atts = array(
			'align'      => array(),
			'class'      => array(),
			'type'       => array(),
			'id'         => array(),
			'dir'        => array(),
			'lang'       => array(),
			'style'      => array(),
			'xml:lang'   => array(),
			'src'        => array(),
			'alt'        => array(),
			'href'       => array(),
			'rel'        => array(),
			'rev'        => array(),
			'target'     => array(),
			'novalidate' => array(),
			'placeholder' => array(),
			'value'      => array(),
			'enabled'    => array(),
			'disabled'    => array(),
			'readonly'    => array(),
			'maxlength'    => array(),
			'minlength'    => array(),
			'selected'    => array(),
			'checked'    => array(),
			'required'    => array(),
			'name'       => array(),
			'tabindex'   => array(),
			'role'   => array(),
			'aria'   => array(),
			'action'     => array(),
			'method'     => array(),
			'for'        => array(),
			'width'      => array(),
			'height'     => array(),
			'data'       => array(),
			'multiple'       => array(),
			'title'      => array(),
		);
		
		$set_tags = array(
		'div','header','footer','section','article','h1','h2','h3','h4','h5','h6','p','span','i','mark','strong','b','br','em','del','ins','u','s','nav','ul','li','form','input','textarea','select','option','img','a');
		$allowed_html = array();
		foreach($set_tags as $set_tag){
			$allowed_html[$set_tag] = $allowed_atts; 
		}
		
		if( 'all'== $allowed_tags ){
			return $allowed_html;
		}else{
			if( is_array($allowed_tags) && !empty($allowed_tags) ){
				$specific_tags = array();
				foreach( $allowed_tags as $allowed_tag ){
					if( array_key_exists($allowed_tag,$allowed_html) ){
						$specific_tags[$allowed_tag] = $allowed_html[$allowed_tag];
					}
				}
				return $specific_tags;
			} 
		}
		
	}
}// end


/* ============ NOTICES ================ */
if( !function_exists('eppc_notices_set') ){
	function eppc_notices_set(){
		if (!is_admin()){ return; }
		
		$eppc_hash = eppc_options( 'eppc_hash' ) ? eppc_options( 'eppc_hash' ) : '';
		if (empty($eppc_hash)):
			$setPageUrl = admin_url( 'options-general.php?page=eppc_pushcrew_options' );
			?>
			<div class="updated fade">
				<p><strong><?php echo esc_html__('PushCrew is almost ready.','eppc');?></strong> <?php echo esc_html__(' You must','eppc');?><a href="<?php echo esc_url($setPageUrl);?>"><?php echo esc_html__(' enter your Account ID','eppc');?></a><?php echo esc_html__(' for it to work.','eppc');?></p>
			</div>
			<?php
		endif;
		
	}
	add_action( 'admin_notices','eppc_notices_set');
}// end

/* ============ ADD MENU PAGE ================ */
if( !function_exists('eppc_admin_menu') ){
	function eppc_admin_menu(){
		add_options_page(
			esc_html__('EP PushCrew Page','eppc'), 
			esc_html__('EP PushCrew','eppc'), 
			'create_users', 
			'eppc_pushcrew_options', 
			'eppc_plugin_options_init'
		);
	}
	add_action( 'admin_menu', 'eppc_admin_menu' );
}// end

/* ============ ADD SETTING PAGE OPTIONS ================ */
if( !function_exists('eppc_isset_start') ){
	function eppc_isset_start(){
		$eppc_start = eppc_options( 'eppc_start' ) ? eppc_options( 'eppc_start' ) : '';
		return ( !empty(sanitize_key($eppc_start)) && ('eppc_start' === sanitize_key($eppc_start)) ) ? true : false;
	}
}

/* ============ SETTING FIELDS ================ */
if( !function_exists('eppc_register_settings') ){
	function eppc_register_settings(){
		register_setting( 'eppc_settings', 'eppc_options' );
		add_settings_section(
			'eppc_settings_section',
			'',
			'eppc_settings_section_callback',
			'eppc_settings'
		);
		
		add_settings_field(
			'eppc_start',
			'',
			'eppc_start_field_render',
			'eppc_settings',
			'eppc_settings_section'
		);
		
		add_settings_field(
			'eppc_hash',
			esc_html__( 'Your PushCrew account ID', 'eppc' ),
			'eppc_hash_field_render',
			'eppc_settings',
			'eppc_settings_section'
		);
		
		add_settings_field(
			'eppc_posttypes',
			esc_html__( 'Add exclude metabox to', 'eppc' ),
			'eppc_post_types_field_render',
			'eppc_settings',
			'eppc_settings_section'
		);
		
		

		
	}
	add_action( 'admin_init', 'eppc_register_settings' );
	
}// end


if( !function_exists('eppc_settings_section_callback') ){
	function eppc_settings_section_callback() {
		echo '';
	}
}

// Render Start check
if( !function_exists('eppc_start_field_render') ){
	function eppc_start_field_render() {
		?>
		<input type="hidden" name="eppc_options[eppc_start]" value="<?php echo esc_attr(sanitize_key('eppc_start')); ?>">
		<?php
	}
}

// Render PushCrew account ID Option
if( !function_exists('eppc_hash_field_render') ){
	function eppc_hash_field_render( ) {
		$eppc_hash = eppc_options( 'eppc_hash' ) ? eppc_options( 'eppc_hash' ) : '';
		?>
		<div><input type="text" name="eppc_options[eppc_hash]" value="<?php echo esc_attr($eppc_hash); ?>"></div>
		<?php
	}
}

// Render PushCrew Post Types
if( !function_exists('eppc_post_types_field_render') ){
	function eppc_post_types_field_render() {
		$eppc_posttypes = eppc_options( 'eppc_posttypes' ) ? eppc_options( 'eppc_posttypes' ) : array();
		if( empty($eppc_posttypes) && !eppc_isset_start() ){
			$eppc_posttypes[] = sanitize_key('page');
			$eppc_posttypes[] = sanitize_key('post');
		}
		$set_posttypes = array(
			sanitize_key('page') => esc_html__('Page','eppc'),
			sanitize_key('post') => esc_html__('Post','eppc'),
		);
		$args = array(
		   'public'   => true,
		   '_builtin' => false
		);
		  
		$output = 'objects';
		$operator = 'and';
		  
		$get_posttypes = get_post_types( $args, $output, $operator );
		if(!empty($get_posttypes) && is_array($get_posttypes)){
			foreach($get_posttypes as $posttype){
				$key = !empty($posttype->name) ? $posttype->name : '';
				$label = !empty($posttype->label) ? $posttype->label : '';
				$set_posttypes[sanitize_key($key)] = esc_html($label);
			}
		}
		if(!empty($set_posttypes) && is_array($set_posttypes)):
		?>
		<div class="eppc-posttypes">
			<fieldset>
				<?php foreach($set_posttypes as $pt_key => $pt_label): 
				$checked = in_array($pt_key,$eppc_posttypes) ? sanitize_key('checked') : '';
				
				?>
				<label for="eppc_posttypes_<?php echo esc_attr($pt_key);?>">
					<input id="eppc_posttypes_<?php echo esc_attr($pt_key);?>" type="checkbox" name="eppc_options[eppc_posttypes][]" value="<?php echo esc_attr($pt_key);?>" <?php echo esc_attr($checked);?>> <span><?php echo esc_html($pt_label);?></span>
				</label>
				<br>
			
				<?php
				endforeach; ?>
			</fieldset>
		</div>
		<?php	
		endif;
	}
}

if( !function_exists('eppc_plugin_options_init') ){
	function eppc_plugin_options_init() {
		$eppc_hash = eppc_options( 'eppc_hash' ) ? eppc_options( 'eppc_hash' ) : '';
		

	  echo '<div class="eppc-settingsWrap wrap">';?>
	  <h2><?php echo esc_html__('Ep PushCrew','eppc'); ?></h2>
	  <?php if (empty($eppc_hash)):?>
	  <p><?php echo wp_kses(__('You need to have a <a target="_blank" href="https://pushcrew.com/">PushCrew</a> account in order to use this plugin. This plugin inserts the neccessary code into your Wordpress site automatically without you having to touch anything. You cal also exclude script from specific page. In order to use the plugin, you need to enter your PushCrew Account ID (Your Account ID (a string of characters) can be found in the <i>Account Details</i> section under <i>Settings</i> area after you <a target="_blank" href="https://pushcrew.com/admin/">login</a> into your PushCrew account.)','eppc'),eppc_allowed_html(array('a','i')));?></p>
	  <?php endif;?>
	  <form method="post" action="options.php">
		  <table class="form-table">
				<tr valign="top">
					<td>
						<?php
						settings_fields( 'eppc_settings' );
						do_settings_sections( 'eppc_settings' );
						submit_button();
						?>
					</td>
				</tr>
		  </table>
	   </form>
	<?php
	  echo '</div>';
	}
}
/* ============ METABOX ================ */
if( !function_exists('eppc_metabox_options_init') ){
	function eppc_metabox_options_init( $post_type, $post) {
		$eppc_posttypes = eppc_options( 'eppc_posttypes' ) ? eppc_options( 'eppc_posttypes' ) : array();
		
		if( empty($eppc_posttypes) && !eppc_isset_start() ){
			$eppc_posttypes[] = sanitize_key('page');
			$eppc_posttypes[] = sanitize_key('post');
		}
		if(in_array($post_type,$eppc_posttypes)){
			add_meta_box( 
				'eppc_pushcrew_metabox',
				esc_html__( 'Exclude Pushcrew(VWO Engage)','eppc' ),
				'eppc_metabox_fields',
				$eppc_posttypes,
				'side',
				'high'
			);
		}

	}
	add_action( 'add_meta_boxes', 'eppc_metabox_options_init', 10, 2 );
}
/* ============ METABOX FIELDS ================ */
if( !function_exists('eppc_metabox_fields') ){
	function eppc_metabox_fields($post) {
		wp_nonce_field( basename( __FILE__ ), sanitize_key('eppc_nonce') );
		$eppc_exclude = get_post_meta( absint($post->ID), sanitize_key('eppc_exclude'), true ); 
		
		?>
		<div class="meta-row eppc-meta_row">
			<div class="meta-td">
				<label for="eppc_exclude">
					 <input id="eppc_exclude" type="checkbox" name="eppc_exclude" value="<?php echo esc_attr(sanitize_key('yes'));?>" <?php checked( sanitize_key($eppc_exclude), sanitize_key('yes') ); ?>><span><?php echo esc_html__('Exclude Pushcrew Script','eppc');?></span>
				</label>
			</div>
		</div>
		<?php

	}
}
/* ============ METABOX SAVE ================ */
if( !function_exists('eppc_metabox_save') ){
	function eppc_metabox_save($post_id) {
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$nonce_key = sanitize_key('eppc_nonce');
		$exclude_key = sanitize_key('eppc_exclude');
		$eppc_nonce = ( isset( $_POST[$nonce_key] ) && !empty( $_POST[$nonce_key] ) && wp_verify_nonce( $_POST[$nonce_key], $nonce_key ) ) ? true : false;
		if( $is_autosave || $is_revision ){
			return;
		}
		if ( isset($_POST[$exclude_key]) && !empty($_POST[$exclude_key]) && ( sanitize_key('yes') === $_POST[$exclude_key] ) ) {
			update_post_meta( absint($post_id), $exclude_key, sanitize_text_field($_POST[$exclude_key]));
		}else{
			update_post_meta( absint($post_id), $exclude_key, '');
		}

	}
	add_action( 'save_post', 'eppc_metabox_save');
}

/* ============ PUSHCREW SCRIPTS ================ */
if( !function_exists('eppc_add_pushcrew_scripts') ){
	function eppc_add_pushcrew_scripts() {
		global $post;
		$post_type = !empty($post->post_type) ? $post->post_type : '';
		$eppc_hash = eppc_options( 'eppc_hash' ) ? eppc_options( 'eppc_hash' ) : '';
		$eppc_exclude = get_post_meta(absint($post->ID),sanitize_key('eppc_exclude'),true); 
		if( empty($eppc_hash) ){ return; }
		$eppc_exclude = (!empty($eppc_exclude) && (sanitize_key('yes') === sanitize_key($eppc_exclude)) ) ? true : false; 
		
		$eppc_posttypes = !empty(eppc_options('eppc_posttypes')) ? eppc_options('eppc_posttypes') : array();
		if( empty($eppc_posttypes) && !eppc_isset_start() ){
			$eppc_posttypes[] = sanitize_key('page');
			$eppc_posttypes[] = sanitize_key('post');
		}
		
		$is_exclude = ( in_array($post_type,$eppc_posttypes) && $eppc_exclude ) ? true : false;
		if($is_exclude){ return; }
		?>
		<!-- Start PushCrew Asynchronous Code -->
		<script>
		(function(p,u,s,h) {
			p._pcq = p._pcq || [];
			p._pcq.push(['_currentTime', Date.now()]);
			s = u.createElement('script'); s.type = 'text/javascript'; s.async = true;
			s.src = '<?php echo esc_url("https://cdn.pushcrew.com/js/{$eppc_hash}.js");?>';
			h = u.getElementsByTagName('script')[0]; h.parentNode.insertBefore(s, h);
		})(window,document);
		</script>
		<!-- End PushCrew Asynchronous Code -->
		<?php
	}
	add_action( 'wp_head', 'eppc_add_pushcrew_scripts');
}



