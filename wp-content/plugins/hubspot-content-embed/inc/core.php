<?php


function load_ch_embed_plugin() {
	require_once plugin_dir_path( __FILE__ ) . '/../public/class-content-embed.php';
}


add_action( 'plugins_loaded', 'load_ch_embed_plugin', 11 );
