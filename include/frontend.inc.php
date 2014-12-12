<?php
if( !defined( 'ABSPATH' ) )
	die( 'Error' );

/* Used to return the correct title for the double login/logout menu item */
function aiwoo_loginout_title( $title )
{
	$titles = explode( '|', $title );
	if ( ! is_user_logged_in() )
		return esc_html( isset( $titles[0] ) ? $titles[0] : $title );
	else
		return esc_html( isset( $titles[1] ) ? $titles[1] : $title );
}

/* The main code, this replace the #keyword# by the correct links with nonce ect */
add_filter( 'wp_setup_nav_menu_item', 'aiwoo_setup_nav_menu_item' );
function aiwoo_setup_nav_menu_item( $item )
{
	global $pagenow, $woocommerce;
	if( $pagenow!='nav-menus.php' && !defined('DOING_AJAX') && isset( $item->url ) && strstr( $item->url, '#aiwoo' ) != '' ){
		$item_url = substr( $item->url, 0, strpos( $item->url, '#', 1 ) ) . '#';
		$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
		
		if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' )
			$aiwooregister = get_permalink( $myaccount_page_id );
		else
			$aiwooregister = $woocommerce->cart->get_checkout_url();
			
		switch( $item_url ) {
			case '#aiwooshop#'     :	$item->url = get_permalink( woocommerce_get_page_id( 'shop' ) ); break;
		
			case '#aiwooaccount#'  :	$item->url = get_permalink( $myaccount_page_id ); break;
	
			case '#aiwoocart#'	   :	$item->url = $woocommerce->cart->get_cart_url(); break;
			
			case '#aiwoologinout#' :	$item->url = is_user_logged_in() ? wp_logout_url( get_permalink( $myaccount_page_id ) ) : get_permalink( $myaccount_page_id );
										$item->title = aiwoo_loginout_title( $item->title ) ; break;
									
			case '#aiwoologin#'    : 	$item->url = get_permalink( $myaccount_page_id ); break;
			
			case '#aiwoologout#'   : 	$item->url = wp_logout_url( get_permalink( $myaccount_page_id ) ); break;
			
			case '#aiwoocheckout#' :	$item->url = $woocommerce->cart->get_checkout_url(); break;
			
			case '#aiwoopayment#'  :	$item->url = get_permalink( woocommerce_get_page_id( 'pay' ) ); break;
			
			case '#aiwooregister#' : 	if( is_user_logged_in() ) $item->title = '#aiwooregister#'; else $item->url = $aiwooregister; break;
			
			
		}
		$item->url = esc_url( $item->url );
	}
	return $item;
}

add_filter( 'wp_nav_menu_objects', 'aiwoo_wp_nav_menu_objects' );
function aiwoo_wp_nav_menu_objects( $sorted_menu_items )
{
	foreach( $sorted_menu_items as $k=>$item )
		if( $item->title==$item->url && $item->title=='#aiwooregister#' )
			unset( $sorted_menu_items[$k] );
	return $sorted_menu_items;
}

/* [shop] shortcode */
add_shortcode( 'shop', 'aiwoo_shortcode_shop' );
function aiwoo_shortcode_shop( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$href = get_permalink( woocommerce_get_page_id( 'shop' ) );
	$content = $content != '' ? $content : __( 'Shop' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [account] shortcode */
add_shortcode( 'account', 'aiwoo_shortcode_account' );
function aiwoo_shortcode_account( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
	$href = get_permalink( $myaccount_page_id );
	$content = $content != '' ? $content : __( 'Account' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [cart] shortcode */
add_shortcode( 'cart', 'aiwoo_shortcode_cart' );
function aiwoo_shortcode_cart( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$href = $woocommerce->cart->get_cart_url();
	$content = $content != '' ? $content : __( 'Cart' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [login] shortcode */
add_shortcode( 'login', 'aiwoo_shortcode_login' );
function aiwoo_shortcode_login( $atts, $content = null )
{
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
	$href = get_permalink( $myaccount_page_id );
	$content = $content != '' ? $content : __( 'Log In' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [loginout] shortcode */
add_shortcode( 'loginout', 'aiwoo_shortcode_loginout' );
function aiwoo_shortcode_loginout( $atts, $content = null )
{
	$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	$edit_tag = strip_tags( $edit_tag );
	$href = is_user_logged_in() ? wp_logout_url( get_permalink( $myaccount_page_id ) ) : get_permalink( $myaccount_page_id );
	if( $content && strstr( $content, '|' ) != '' ) { // the "|" char is used to split titles
		$content = explode( '|', $content );
		$content = is_user_logged_in() ? $content[1] : $content[0];
	}else{
		$content = is_user_logged_in() ? __( 'Logout' ) : __( 'Log In' );
	}
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [logout] shortcode */
add_shortcode( 'logout', 'aiwoo_shortcode_logout' );
function aiwoo_shortcode_logout( $atts, $content = null )
{
	$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	$href = wp_logout_url( get_permalink( $myaccount_page_id ) );
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$content = $content != '' ? $content : __( 'Logout' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [checkout] shortcode */
add_shortcode( 'checkout', 'aiwoo_shortcode_checkout' );
function aiwoo_shortcode_checkout( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$href = $item->url = $woocommerce->cart->get_checkout_url();
	$content = $content != '' ? $content : __( 'Checkout' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [payment] shortcode */
add_shortcode( 'payment', 'aiwoo_shortcode_payment' );
function aiwoo_shortcode_payment( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$href = $item->url = get_permalink( woocommerce_get_page_id( 'pay' ) );
	$content = $content != '' ? $content : __( 'Payment' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [register] shortcode */
add_shortcode( 'register', 'aiwoo_shortcode_register' );
function aiwoo_shortcode_register( $atts, $content = null )
{
	$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
	if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' )
		$aiwooregister = get_permalink( $myaccount_page_id );
	else
		$aiwooregister = $woocommerce->cart->get_checkout_url();
		
	if( is_user_logged_in() )
		return '';
		
	$href = $aiwooregister;
	$content = $content != '' ? $content : __( 'Register' );
	$link = '<a href="' . $href. '">' . $content . '</a>';
	return $link;
}