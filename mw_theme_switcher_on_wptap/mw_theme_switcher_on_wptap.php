<?php
/**
 * Plugin Name: MW Theme Switcher on WPtap
 * Plugin URI: http://2inc.org
 * Description: WPtap Mobile Detector を使用中の場合、フッターにPC<=>モバイルの切り替えボタンを表示するプラグインです。
 * Version: 0.2
 * Author: Takashi Kitajima
 * Author URI: http://2inc.org
 * Modified: May 12, 2012
 * License: GPL2
 *
 * Copyright 2012 Takashi Kitajima (email : inc@2inc.org)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
class mw_theme_switcher_on_wptap {

	const FLG = "viewmode";

	/**
	 * コンストラクタ
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'set_theme_cookie' ), 11 );
		add_action( 'plugins_loaded', array( $this, 'mobile_switcher' ), 11 );
		add_filter( 'wp_footer', array( $this, 'render_switcher' ), 11 );
	}

	/**
	 * cookieをセット
	 */
	public function set_theme_cookie() {
		if ( function_exists( 'mobileDetect' ) ) {
			if ( isset( $_GET[self::FLG] ) ) {
				$viewmode = $_GET[self::FLG];
				if ( $viewmode === 'pc' || $viewmode === 'mb' ) {
					setcookie( self::FLG, $viewmode, null, '/' );
					// header( 'Location: '.esc_attr( $_SERVER['SCRIPT_NAME'] ) );
					$requestUri = $_SERVER['REQUEST_URI'];
					$requestUri = preg_replace( '/^(.+?)(\?.*)$/', '$1', $requestUri );
					$args = $_GET;
					unset( $args[self::FLG] );
					if ( !empty( $args ) ) {
						$args = '?'.http_build_query( $args );
						$requestUri = $requestUri.$args;
					}
					wp_redirect( esc_attr( $requestUri ) );
					exit;
				}
			}
		}
	}

	/**
	 * mobileDetect関数をremove
	 */
	public function mobile_switcher() {
		if ( function_exists( 'mobileDetect' ) ) {
			if ( !empty( $_COOKIE[self::FLG] ) ) {
				$viewmode = $_COOKIE[self::FLG];
				if ( $viewmode === 'pc' ) {
					remove_filter( 'stylesheet', 'mobileDetect' );
					remove_filter( 'template', 'mobileDetect' );
				}
			}
		}
	}

	/**
	 * ボタン表示
	 */
	public function render_switcher() {
		global $mobile_current_template;
		if ( function_exists( 'mobileDetect' ) && !empty( $mobile_current_template ) ) {
		?>
		<div class="renderSwitcher">
			<ul>
				<li class="pc"><a href="?<?php echo self::FLG; ?>=pc">PC表示</a></li><!--
				--><li class="mobile"><a href="?<?php echo self::FLG; ?>=mb">モバイル表示</a></li>
			</ul>
		<!-- end .renderSwitcher --></div>
		<?php
		}
	}
}
$mw_theme_switcher_on_wptap = new mw_theme_switcher_on_wptap();
