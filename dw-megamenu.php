<?php  
/**
 * Plugin Name: DW Megamenu
 * Version: 1.0.1
 * Author: DesignWall
 * Author URI: http://www.designwall.com
 * Description: Adding a versatile navigation to your site
 * 
 */

if ( ! defined( 'DWM_DIR' ) ) {
	define( 'DWM_DIR', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}
if ( ! defined( 'DWM_PATH' ) ) {
	define( 'DWM_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

class DW_Megamenu {

	public function __construct(){
		global $pagenow;
		load_plugin_textdomain( 'dw-megamenu', false, DWM_PATH . 'languages' );
		//add_action( 'admin_menu', array( $this, 'admin_menu_init' ) );

		//Add bootstrap scripts and style
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_filter( 'wp_nav_menu_args', array( __CLASS__, 'replace_walker' ), 9999999 );
		if ( 'nav-menus.php' == $pagenow ) {
			add_action( 'admin_head', array( $this, 'admin_menu_modal' ) );
		}
		
	}

	public function admin_menu_init() {

		add_menu_page( 
			__( 'DW Megamenu' , 'dw-megamenu' ), 
			__( 'DW Megamenu' , 'dw-megamenu' ),
			'manage_options', 
			'dw-megamenu',
			array( $this, 'admin_menu_callback' ),
			'dashicons-welcome-widgets-menus',
			99
		);
	}

	public static function replace_walker( $args ) {
		$args['walker'] = new DW_Megamenu_Render( $args['walker'] );
		return $args;
	}

	public function admin_menu_callback() {
		include DWM_PATH . 'templates/admin/builder.php';
	}

	public function admin_menu_modal() {
		$filter = array(
			'web_app'           => __( 'Web Application Icons', 'dw-megamenu' ),
			'transportation'    => __( 'Transportation Icons', 'dw-megamenu' ),
			'gender'            => __( 'Gender Icons', 'dw-megamenu' ),
			'file_type'         => __( 'Filt Type Icons', 'dw-megamenu' ),
			'spinner'           => __( 'Spinner Icons', 'dw-megamenu' ),
			'form_control'      => __( 'Form Control Icons', 'dw-megamenu' ),
			'payment'           => __( 'Payment Icons', 'dw-megamenu' ),
			'chart'             => __( 'Chart Icons', 'dw-megamenu' ),
			'currency'          => __( 'Currency Icons', 'dw-megamenu' ),
			'text_editor'       => __( 'Text Editor', 'dw-megamenu' ),
			'directional'       => __( 'Directional Icons', 'dw-megamenu' ),
			'video_player'      => __( 'Video Player Icons', 'dw-megamenu' ),
			'brand'             => __( 'Brand Icons', 'dw-megamenu' ),
			'medical'           => __( 'Medical', 'dw-megamenu' ),
		);
		include DWM_PATH . 'lib/icon.php';
		include DWM_PATH . 'templates/admin/icon.php';
	}

	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'dw-megamenu-front-end-style', DWM_DIR . 'assets/css/dw-megamenu.css' );
		wp_enqueue_script( 'dw-megamenu-front-end-js', DWM_DIR . 'assets/js/dw-megamenu.js', array( 'jquery' ), false, true );
	}

	public function admin_enqueue_scripts() {
		global $pagenow;

		if( 'nav-menus.php' == $pagenow ) {
			wp_enqueue_style( 'dw-megamenu-nav-menu-style', DWM_DIR . 'assets/css/nav-menu.css' );
			wp_enqueue_script( 'dw-megamenu-nav-menu-js', DWM_DIR . 'assets/js/nav-menu.js', array( 'jquery' ), false, true );
			wp_enqueue_style( 'dw-megamenu-style', DWM_DIR . 'assets/css/font-awesome.css' );
		}
	}

}


require_once DWM_PATH . 'lib/render.php';
require_once DWM_PATH . 'lib/nav-menu.php';
require_once DWM_PATH . 'lib/widgets.php';
require_once DWM_PATH . 'lib/setting.php';

$GLOBALS['dw-megamenu'] = new DW_Megamenu();

function dw_megamenu_meta( $post, $key, $default = false, $clear = false ) {
	static $meta = array();

	if ( $clear ) {
		$meta = array();
	}

	$post_id = is_object( $post ) ? $post->ID : $post;

	if ( !isset( $meta[$post_id] ) ) {
		$meta[$post_id] = get_post_meta( $post_id, $key, true );
	}

	return isset( $meta[$post_id] ) ? $meta[$post_id] : $default;
}

add_filter( 'walker_nav_menu_start_el', 'dw_megamenu_filter_start_el', 10, 4 );
function dw_megamenu_filter_start_el( $item_output, $item, $depth, $args ) {
	$hidelabel = get_post_meta( $item->ID, '_dw_megamenu_hide_label', true );
	if ( $depth > 0 && 'true' == $hidelabel ) {
		$item_output = '';
	}

	if ( $depth > 0 && trim( $item->post_content ) ) {
		ob_start();
		?>
		<div class="dw-megamenu-description"><?php dw_megamenu_the_content_by_id( $item->ID ); ?></div>
		<?php
		$item_output .= ob_get_clean();
	}

	return $item_output;
}

function dw_megamenu_the_content_by_id( $post_id=0, $more_link_text = null, $stripteaser = false ){
	global $post;
	$post = get_post($post_id);
	setup_postdata( $post, $more_link_text, $stripteaser );
	the_content();
	wp_reset_postdata( $post );
}

add_filter( 'widget_title', 'dw_megamenu_remove_title_wiget_if_empty', 10, 3 );
function dw_megamenu_remove_title_wiget_if_empty( $title, $instance, $id_base ) {
	if ( empty( $instance['title'] ) ) {
		$title = '';
	}

	return $title;
}

add_filter( 'wp_nav_menu_objects', 'dw_megamenu_add_class', 10, 2 );
function dw_megamenu_add_class( $sorted_menu_items, $args ) {
	$mega_menu = array();
	$parent = array();
	$mega_menu_tab = array();

	foreach( $sorted_menu_items as $item ) {
		$megamenu_enable = get_post_meta( $item->ID, '_dw_megamenu_enable', true );
		$megamenu_type = get_post_meta( $item->ID, '_dw_megamenu_type', true );
		if ( $item->menu_item_parent == 0 && $megamenu_enable === 'true' && $megamenu_type == 'column' ) {
			$mega_menu[ $item->ID ] = true;
		}

		if ( $item->menu_item_parent == 0 && $megamenu_enable == 'true' && $megamenu_type === 'tab' ) {
			$mega_menu_tab[$item->ID] = true;
		}

		if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
			$parent[] = $item->menu_item_parent;
		}
	}

	foreach( $sorted_menu_items as $item ) {
		$fullwidth = get_post_meta( $item->ID, '_dw_megamenu_enable_fullwidth', true );

		if ( !isset( $mega_menu[ $item->ID ] ) && !isset( $mega_menu_tab[ $item->ID ] ) && $item->menu_item_parent == 0 ) {
			$item->classes[] = 'menu-flyout';
		}

		if ( isset( $mega_menu[$item->menu_item_parent] ) ) {
			$item->classes[] = 'dw-mega-menu-col';
		}

		if ( isset( $mega_menu_tab[ $item->menu_item_parent ] ) ) {
			$item->classes[] = 'dw-mega-menu-tab';
		}

		if ( $fullwidth == 'true' || isset( $mega_menu_tab[ $item->ID ] ) ) {
			$item->classes[] = 'dw-mega-menu-full-width';
		}

		$item->classes[] = 'dw-mega-menu-hover';
	}

	return $sorted_menu_items;
}