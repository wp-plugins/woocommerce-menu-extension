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

/* Used to return the correct title for the double basket menu item */
function aiwoo_basket_title( $title )
{
	global $woocommerce;
	if ( WC()->cart->get_cart_contents_count() == 0 )
		return esc_html( isset( $title ) ? $title : $title );
	else
		return sprintf (_n( '%d item', '%d items', WC()->cart->cart_contents_count ), WC()->cart->cart_contents_count ) .' - '. WC()->cart->get_cart_total();
}

/* The main code, this replace the #keyword# by the correct links with nonce ect */
add_filter( 'wp_setup_nav_menu_item', 'aiwoo_setup_nav_menu_item' );
function aiwoo_setup_nav_menu_item( $item )
{
	global $pagenow, $woocommerce;
	if( $pagenow!='nav-menus.php' && !defined('DOING_AJAX') && isset( $item->url ) && strstr( $item->url, '#aiwoo' ) != '' ) {
		$item_url = substr( $item->url, 0, strpos( $item->url, '#', 1 ) ) . '#';
		$item_redirect = str_replace( $item_url, '', $item->url );
		
		if(!empty($item_redirect))
			$item_redirect = $item_redirect;
		else
			$item_redirect = get_permalink( woocommerce_get_page_id( 'myaccount' ) );
		
		switch( $item_url ) {
			case '#aiwooshop#'		 :	$item->url = get_permalink( woocommerce_get_page_id( 'shop' ) ); break;
			
			case '#aiwoocart#'		 :	$item->url = get_permalink( woocommerce_get_page_id( 'cart' ) ); break;
			
			case '#aiwoobasket#'	 :	$item->url = get_permalink( woocommerce_get_page_id( 'cart' ) ); 
										$item->title = aiwoo_basket_title( $item->title ); break;
			
			case '#aiwoologin#'		 : 	$item->url = get_permalink( woocommerce_get_page_id( 'myaccount' ) ); break;
			
			case '#aiwoologout#'	 : 	$item->url = wp_logout_url( $item_redirect ); break;
			
			case '#aiwoologinout#'	 :	$item->url = is_user_logged_in() ? wp_logout_url( $item_redirect ) : get_permalink( woocommerce_get_page_id( 'myaccount' ) );
										$item->title = aiwoo_loginout_title( $item->title ) ; break;
			
			case '#aiwoocheckout#'	 :	$item->url = get_permalink( woocommerce_get_page_id( 'checkout' ) ); break;	
			
			case '#aiwooterms#'	 	 :	$item->url = get_permalink( woocommerce_get_page_id( 'terms' ) ); break;
			
			case '#aiwoomyaccount#'	 :	$item->url = get_permalink( woocommerce_get_page_id( 'myaccount' ) ); break;

			case '#aiwoosearch#'	 :	$titles = explode( '|', $item->title );
			$item->title = '<form action="'.esc_url( home_url( '/'  ) ).'" class="woocommerce-product-search" method="get" role="search">
				<input type="search" title="'.esc_attr_x( 'Search for:', 'label' ).'" name="s" value="'.get_search_query().'" placeholder="'.esc_html( isset( $titles[0] ) ? $titles[0] : $item->title ).'" class="search-field">
				<input type="submit" value="'.esc_html( isset( $titles[1] ) ? $titles[1] : esc_attr_x( 'Search', 'submit button' ) ).'">
				<input type="hidden" value="product" name="post_type">
			</form>'; break;
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

/* [aishop] shortcode */
add_shortcode( 'aishop', 'aiwoo_shortcode_shop' );
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

/* [aicart] shortcode */
add_shortcode( 'aicart', 'aiwoo_shortcode_cart' );
function aiwoo_shortcode_cart( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$href = get_permalink( woocommerce_get_page_id( 'cart' ) );
	$content = $content != '' ? $content : __( 'Cart' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [aibasket] shortcode */
add_shortcode( 'aibasket', 'aiwoo_shortcode_basket' );
function aiwoo_shortcode_basket( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$href = get_permalink( woocommerce_get_page_id( 'cart' ) );
	$content = sprintf (_n( '%d item', '%d items', WC()->cart->cart_contents_count ), WC()->cart->cart_contents_count ) .' - '. WC()->cart->get_cart_total();
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [ailogin] shortcode */
add_shortcode( 'ailogin', 'aiwoo_shortcode_login' );
function aiwoo_shortcode_login( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$href = get_permalink( woocommerce_get_page_id( 'myaccount' ) );
	$content = $content != '' ? $content : __( 'Log In' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [ailogout] shortcode */
add_shortcode( 'ailogout', 'aiwoo_shortcode_logout' );
function aiwoo_shortcode_logout( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => "",
		"redirect" => get_permalink( woocommerce_get_page_id( 'myaccount' ) )
	), $atts ) );
	$href = wp_logout_url( $redirect );
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$content = $content != '' ? $content : __( 'Logout' );
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		return 'Hello ' . $current_user->user_login . ', <a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content .  '</a>';
	}
}

/* [ailoginout] shortcode */
add_shortcode( 'ailoginout', 'aiwoo_shortcode_loginout' );
function aiwoo_shortcode_loginout( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => "",
		"redirect" => get_permalink( woocommerce_get_page_id( 'myaccount' ) )
	), $atts ) );
	$edit_tag = strip_tags( $edit_tag );
	$href = is_user_logged_in() ? wp_logout_url( $redirect ) : get_permalink( woocommerce_get_page_id( 'myaccount' ) );
	if( $content && strstr( $content, '|' ) != '' ) { // the "|" char is used to split titles
		$content = explode( '|', $content );
		$content = is_user_logged_in() ? $content[1] : $content[0];
	}else{
		$current_user = wp_get_current_user();
		$message = is_user_logged_in() ? __( 'Hello ' ) . $current_user->user_login . ', ' : '';
		$content = is_user_logged_in() ? __( 'Logout' ) : __( 'Log In' );
	}
	return $message . '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [aicheckout] shortcode */
add_shortcode( 'aicheckout', 'aiwoo_shortcode_checkout' );
function aiwoo_shortcode_checkout( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$href = get_permalink( woocommerce_get_page_id( 'checkout' ) );
	$content = $content != '' ? $content : __( 'Checkout' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [aiterms] shortcode */
add_shortcode( 'aiterms', 'aiwoo_shortcode_terms' );
function aiwoo_shortcode_terms( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$href = get_permalink( woocommerce_get_page_id( 'terms' ) );
	$content = $content != '' ? $content : __( 'Terms' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [aimyaccount] shortcode */
add_shortcode( 'aimyaccount', 'aiwoo_shortcode_myaccount' );
function aiwoo_shortcode_myaccount( $atts, $content = null )
{
	global $woocommerce;
	extract(shortcode_atts(array(
		"edit_tag" => ""
	), $atts ) );
	
	$edit_tag = esc_html( strip_tags( $edit_tag ) );
	$href = get_permalink( woocommerce_get_page_id( 'myaccount' ) );
	$content = $content != '' ? $content : __( 'Myaccount' );
	return '<a href="' . esc_url( $href ) . '"' .$edit_tag . '>' . $content . '</a>';
}

/* [aisearch] shortcode */
add_shortcode( 'aisearch', 'aiwoo_shortcode_search' );
function aiwoo_shortcode_search( $atts, $content = null )
{
	global $woocommerce;
	$atts = shortcode_atts(array(
		"button" => "Search",
	), $atts );
	
	$content = $content != '' ? $content : __( 'Search Products…' );
	$search = '<form action="'.esc_url( home_url( '/'  ) ).'" class="woocommerce-product-search" method="get" role="search">
				<input type="search" title="'.esc_attr_x( 'Search for:', 'label' ).'" name="s" value="'.get_search_query().'" placeholder="'.$content.'" class="search-field">
				<input type="submit" value="'.$atts['button'].'">
				<input type="hidden" value="product" name="post_type">
			</form>';
	return $search;
}