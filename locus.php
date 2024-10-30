<?php
/*
Plugin Name: Locus
Text Domain: locus
Domain Path: /lang
Plugin URI: http://dianakcury.com/dev/locus
Description: Display posts from a specific category, a single post only, post types or pages anywhere in widgetized areas of your site.
Version: 1.0
Author: Diana K. Cury
Author URI: http://arquivo.tk
*/



add_action( 'init', 'locus_setup',1 );
function locus_setup(){

load_plugin_textdomain('locus', null, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
add_action( 'init', 'locus_setup',2 );

    // Hook for adding admin menus

    add_action('widgets_init', create_function('', 'return register_widget("PPostTypeWidget");'),3);
    add_action('widgets_init', create_function('', 'return register_widget("PPWidget");'),4);
    add_action('widgets_init', create_function('', 'return register_widget("PlacedSingleContent");'),5);
    add_action('admin_menu', 'lc_add_pages',6);
    add_action('admin_head', 'locus_header',7);
    add_filter('excerpt_length', 'new_excerpt_length',8);


    add_theme_support( 'post-thumbnails' );
    add_filter('get_header','data_style');


}


require WP_PLUGIN_DIR . '/locus/control/widgets.php';

function data_style($result) {
  wp_enqueue_style('locus_style', WP_PLUGIN_URL . "/locus/control/locus-style.css");
}

function lc_add_pages() {
  add_plugins_page( __('About Locus','locus'), __('About Locus','locus'), 'manage_options', 'locustools', 'lc_tools_page');
}

function lc_tools_page() { include('control/info.php');  }

function new_excerpt_length($length) { return 20; }

function locus_header() {
  	global $post_type, $page;
  	?>
   <style>
   h3.locus-admin{font: italic 20px georgia;}
   h4.locus-admin{font: bold italic 15px georgia;background:#f7f7f7;padding:4px}
   p.locus-admin{font: italic 16px georgia }
   span.locus-admin{background:#fff;font-family:courier;padding:2px; font-weight: bold }
   .locus-admin ul li{list-style:disc;list-style-position:inside;margin-left:20px;}
   .locus-admin h3{  font: italic 20px georgia;background:#fff;margin:50px 0 0px 0;padding:5px  }
   .special {font-weight:bold;background:#fff;padding:4px ;border:1px dashed #ccc;}
   </style>
   <?php }



?>