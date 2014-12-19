<?php
if( !defined( 'ABSPATH' ) )
	die( 'Error' );

/* Add a metabox in admin menu page */
add_action('admin_head-nav-menus.php', 'aiwoo_add_nav_menu_metabox');
function aiwoo_add_nav_menu_metabox() {
	add_meta_box( 'aiwoo', __( 'AI WooCommerce Links' ) . ' v' . AIWOO_VERSION, 'aiwoo_nav_menu_metabox', 'nav-menus', 'side', 'default' );
}

/* The metabox code */
function aiwoo_nav_menu_metabox( $object )
{
	global $nav_menu_selected_id;

	$elems = array( '#aiwooshop#' => __( 'Shop' ), '#aiwooaccount#' => __( 'Account' ), '#aiwoocart#' => __( 'Cart' ), '#aiwoologin#' => __( 'Log In' ), '#aiwoologout#' => __( 'Log Out' ), '#aiwoologinout#' => __( 'Log In' ).'|'.__( 'Log Out' ), '#aiwoocheckout#' => __( 'Checkout' ), '#aiwoopayment#' => __( 'Payment' ), '#aiwooregister#' => __( 'Register' )  );
	class aiwoologItems {
		public $db_id = 0;
		public $object = 'aiwoolog';
		public $object_id;
		public $menu_item_parent = 0;
		public $type = 'custom';
		public $title;
		public $url;
		public $target = '';
		public $attr_title = '';
		public $classes = array();
		public $xfn = '';
	}

	$elems_obj = array();
	foreach ( $elems as $value => $title ) {
		$elems_obj[$title] = new aiwoologItems();
		$elems_obj[$title]->object_id	= esc_attr( $value );
		$elems_obj[$title]->title		= esc_attr( $title );
		$elems_obj[$title]->url			= esc_attr( $value );
	}

	$walker = new Walker_Nav_Menu_Checklist( array() );
	?>
	<div id="login-links" class="loginlinksdiv">

		<div id="tabs-panel-login-links-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
			<ul id="login-linkschecklist" class="list:login-links categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $elems_obj ), 0, (object)array( 'walker' => $walker ) ); ?>
			</ul>
		</div>

		<p class="button-controls">
			<span class="add-to-menu">
				<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-login-links-menu-item" id="submit-login-links" />
				<span class="spinner"></span>
			</span>
		</p>

	</div>
	<?php
}

/* Modify the "type_label" */
add_filter( 'wp_setup_nav_menu_item', 'aiwoo_nav_menu_type_label' );
function aiwoo_nav_menu_type_label( $menu_item )
{
	$elems = array( '#aiwooaccount#', '#aiwooshop#', '#aiwoocart#', '#aiwoologin#', '#aiwoologout#', '#aiwoologinout#', '#aiwoocheckout#',  '#aiwoopayment#', '#aiwooregister#' );
	if ( isset($menu_item->object, $menu_item->url) && $menu_item->object == 'custom' && in_array($menu_item->url, $elems) )
		$menu_item->type_label = ( 'AI WooCommerce' );

	return $menu_item;
}