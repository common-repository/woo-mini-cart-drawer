<?php
/**
 * Its for EPOS settings module
 *
 * @package VitePos_Lite\Modules
 */

namespace Minicart_Lite\Modules;

use Appsbd_Lite\V2\libs\Ajax_Confirm_Response;
use Appsbd_Lite\V2\libs\AppInput;
use Minicart_Lite\Core\Minicart_Module_Lite;
use Minicart_Lite\Libs\App_Language;
use Minicart_Lite\Libs\Mca_Client_Data;
use phpseclib3\Math\PrimeField\Integer;


/**
 * Class APBD_EPOS_Settings
 */
class MCN_Settings extends Minicart_Module_Lite {

	/**
	 * The initialize is generated by appsbd
	 */
	public function initialize() {
	}

	/**
	 * The on init is generated by appsbd
	 */
	public function on_init() {

		$this->add_admin_ajax_action( 'option', array( $this, 'ajax_request_callback' ) );
		$this->add_admin_ajax_action( 'get-option', array( $this, 'get_admin_options' ) );
		$this->add_admin_ajax_action( 'data', array( $this, 'data' ) );
		$this->add_admin_ajax_action( 'confirm', array( $this, 'confirm' ) );

		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'cart_items_fragment' ) );

		$this->add_ajax_both_action( 'update-qty', array( $this, 'update_qty' ) );
		$this->add_ajax_both_action( 'remove-item', array( $this, 'remove_cart_item' ) );
		$this->add_ajax_both_action( 'remove-undo', array( $this, 'remove_undo' ) );
		$this->add_ajax_both_action( 'no-undo', array( $this, 'no_undo' ) );
	}


	/**
	 * The update request callback is generated by appsbd
	 */
	public function update_request_option() {
		$before_save = $this->options;
		$app_posts   = AppInput::get_posted_data();
		if ( ! empty( $app_posts['action'] ) ) {
			unset( $app_posts['action'] );
		}
		$is_updated = false;
		$skip_keys  = array(
			'skin',
			'has_coupon',
			'hide_in_cart',
			'hide_in_checkout',
			'not_hide_in_undo',
			'hide_in_empty_item',
			'show_container_on_each_item',
			'is_show_cpn',
			'is_show_acpn',
			'empty_txt',
			'chk_menus',
			'hide_pages',
			'add_menu_item',
			'menu_show_icon',
			'menu_it_text',
			'menu_icon',
			'is_menu_item_counter',
			'm_counter_type',
		);
		foreach ( $app_posts as $key => $post ) {
			if ( in_array( $key, $skip_keys ) ) {
				continue;
			}
			if ( 'custom_css' == $key ) {
				update_option( 'apbd_nmca_ccss', $post );
				$this->options[ $key ] = $post;
				$is_updated            = true;
				continue;
			} else {
				$this->options[ $key ] = $post;
			}
		}
		/**
		 * Its for check is there any change before process
		 *
		 * @since 1.0
		 */
		$external_update = apply_filters( 'appsbd/nmca/filters/is-admin-settings-update', false, $app_posts );
		if ( ! $external_update && $before_save === $this->options ) {
			$this->add_error( 'No change for update' );
		} elseif ( $this->update_option() || $external_update ) {

				$is_updated = true;
				$this->add_info( 'Saved Successfully' );
		} else {
			$this->add_error( 'No change for update' );
		}

		return $is_updated;
	}


	/**
	 * The remove cart item is generated by appsbd
	 */
	public function remove_cart_item() {
		$item_id  = AppInput::post_value( 'id', null );
		$response = new Ajax_Confirm_Response();
		if ( ! empty( $item_id ) ) {
			if ( WC()->cart->remove_cart_item( $item_id ) ) {
				$this->add_error( 'Successfully removed' );
				$response->display_with_response( true, Mca_Client_Data::get_mca_data() );
			} else {
				$this->add_error( 'Cart item remove failed' );
				$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
			}
		} else {
			$this->add_error( 'ID & Qty both are required' );
			$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
		}
		$this->add_error( 'Unknown error' );
		$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
	}

	/**
	 * The update qty is generated by appsbd
	 */
	public function update_qty() {
				$item_id  = AppInput::post_value( 'id', null );
		$qty      = AppInput::post_value( 'qty', null );
		$response = new Ajax_Confirm_Response();
		if ( $item_id && $qty ) {
			$item          = WC()->cart->get_cart_item( $item_id );
			$error_message = '';
			$qty           = (int) $qty;
			/**
			 * Its for check is there any change before process
			 *
			 * @since 1.0
			 */
			$qty = apply_filters( 'woocommerce_add_to_cart_quantity', $qty, $item_id );
			if ( isset( $item['product_id'] ) && isset( $item['product_id'] ) && isset( $item['quantity'] ) ) {
				if ( $this->is_valid_quantity( $item['data'], $item['quantity'], $qty, $error_message ) ) {
					if ( WC()->cart->set_quantity( $item_id, (int) $qty ) ) {
						$this->add_info( 'Successfully updated' );
						$response->display_with_response( true, Mca_Client_Data::get_mca_data() );
					} else {
						$this->add_error( 'Cart item update failed' );
						$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
					}
				} else {
					$this->add_error( $error_message );
					$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
				}
			} else {
				$this->add_error( 'Cart item info not found' );
				$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
			}
		} else {
			$this->add_error( 'ID & Qty both are required' );
			$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
		}
		$this->add_error( 'Unknown error' );
		$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
	}

	/**
	 * The remove undo is generated by appsbd
	 */
	public function remove_undo() {
				$item_id  = AppInput::post_value( 'id', null );
		$response = new Ajax_Confirm_Response();
		if ( $item_id ) {
			if ( WC()->cart->restore_cart_item( $item_id ) ) {
				WC()->cart->set_removed_cart_contents();
				WC()->session->set( 'removed_cart_contents', WC()->cart->get_removed_cart_contents() );
				$this->add_info( 'Successfully removed' );
				$response->display_with_response( true, Mca_Client_Data::get_mca_data() );
			} else {
				$this->add_error( 'Cart item remove failed' );
				$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
			}
		} else {
			$this->add_error( 'ID is required' );
			$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
		}
		$this->add_error( 'Unknown Error' );
		$response->display_with_response( false, Mca_Client_Data::get_mca_data() );
	}

	/**
	 * The no undo is generated by appsbd
	 */
	public function no_undo() {
		$response = new Ajax_Confirm_Response();
		WC()->cart->set_removed_cart_contents();
		WC()->session->set( 'removed_cart_contents', WC()->cart->get_removed_cart_contents() );
		$this->add_info( 'Done' );
		$response->display_with_response( true, Mca_Client_Data::get_mca_data() );
	}


	/**
	 * The is valid quantity is generated by appsbd
	 *
	 * @param WC_Product $product_data Its the product data.
	 * @param Integer    $current_qty Its the current quantity.
	 * @param Integer    $new_qty Its the new quantity.
	 * @param string     $msg Its the message.
	 *
	 * @return bool
	 */
	public function is_valid_quantity( $product_data, $current_qty, $new_qty, &$msg = '' ) {
		try {

			if ( $new_qty <= 0 || ! $product_data || 'trash' === $product_data->get_status() ) {
				return false;
			}

			if ( $product_data->is_sold_individually() ) {
				if ( $new_qty > 1 ) {
					$msg = sprintf(
					/* translators: %$s is replaced with "Integer" */
						__(
							'You cannot add another "%s" to your cart.',
							'woocommerce'
						),
						$product_data->get_name()
					);

					return false;
				}
			}

			if ( ! $product_data->is_purchasable() ) {
				$msg = $this->__( 'Product is not purchasable' );

				return false;
			}

			if ( ! $product_data->has_enough_stock( $new_qty ) ) {
				/* translators: 1: product name 2: quantity in stock */
				$msg = sprintf(
				/* translators: %1$s is replaced with "Integer" and translators: %2$s is replaced with "Integer"*/
					__(
						'You cannot add that amount of &quot;%1$s&quot; to the cart because there is not enough stock (%2$s remaining).',
						'woocommerce'
					),
					$product_data->get_name(),
					wc_format_stock_quantity_for_display( $product_data->get_stock_quantity(), $product_data )
				);

				return false;
			}
			$msg = $this->__( 'Unknown error-393' );

			return true;

		} catch ( Exception $e ) {

			return false;
		}
	}

	/**
	 * The ajax request callback is generated by appsbd
	 */
	public function ajax_request_callback() {
		$response = new Ajax_Confirm_Response();
		if ( $this->update_request_option() ) {
			$this->get_admin_options();
		}
		$response->display_with_response( false, $this->get_admin_options() );
	}

	/**
	 * The get admin options is generated by appsbd
	 */
	public function get_admin_options() {
		if ( empty( $this->options ) ) {
			$this->set_default_options();
		}
		$options['drawer_type']                 = $this->get_option( 'drawer_type', 'D' );
		$options['position']                    = $this->get_option( 'position', 'LB' );
		$options['skin']                        = $this->get_option( 'skin', 'DF' );
		$options['color']                       = $this->get_option( 'color', 'default' );
		$options['hide_in_cart']                = $this->get_option( 'hide_in_cart', 'N' );
		$options['hide_in_checkout']            = $this->get_option( 'hide_in_checkout', 'N' );
		$options['hide_in_empty_item']          = $this->get_option( 'hide_in_empty_item', 'N' );
		$options['not_hide_in_undo']            = $this->get_option( 'not_hide_in_undo', 'N' );
		$options['show_container_on_each_item'] = $this->get_option( 'show_container_on_each_item', 'N' );
		$options['dont_wc_add_msg']             = $this->get_option( 'dont_wc_add_msg', 'N' );
		$options['add_menu_item']               = $this->get_option( 'add_menu_item', 'N' );
		$options['menu_show_icon']              = $this->get_option( 'menu_show_icon', 'N' );
		$options['menu_it_text']                = $this->get_option( 'menu_it_text', 'My cart' );
		$options['menu_icon']                   = $this->get_option( 'menu_icon', 'ap-cart' );
		$options['is_menu_item_counter']        = $this->get_option( 'is_menu_item_counter', 'N' );
		$options['m_counter_type']              = $this->get_option( 'm_counter_type', 'I' );
		$options['control_size']                = $this->get_option( 'control_size', 80 );
		$options['control_icon_size']           = $this->get_option( 'control_icon_size', 50 );
		$options['border_radius']               = $this->get_option( 'border_radius', 50 );
		$options['top_margin']                  = $this->get_option( 'top_margin', 30 );
		$options['left_margin']                 = $this->get_option( 'left_margin', 30 );
		$options['cart_container_size']         = $this->get_option( 'cart_container_size', 100 );
		$options['cart_container_width']        = $this->get_option( 'cart_container_width', 450 );
		$options['icon']                        = $this->get_option( 'icon', 'ap-cart' );
		$options['df_type']                     = $this->get_option( 'df_type', 'TA' );
		$options['df_amount_type']              = $this->get_option( 'df_amount_type', 'S' );
		$options['dr_anim']                     = $this->get_option( 'dr_anim', 'ape-slideInRight' );
		$options['dr_anim_out']                 = $this->get_option( 'dr_anim_out', 'ape-slideOutRight' );
		$options['hide_corner_circle']          = $this->get_option( 'hide_corner_circle', 'N' );
		$options['circle_type']                 = $this->get_option( 'circle_type', 'Q' );
		$options['title_text']                  = $this->get_option( 'title_text', 'My Cart' );
		$options['is_undo_remove']              = $this->get_option( 'is_undo_remove', 'Y' );
		$options['is_show_dis_total']           = $this->get_option( 'is_show_dis_total', 'Y' );
		$options['is_show_all_fee']             = $this->get_option( 'is_show_all_fee', 'Y' );
		$options['is_show_cpn']                 = $this->get_option( 'is_show_cpn', 'N' );
		$options['is_show_acpn']                = $this->get_option( 'is_show_acpn', 'N' );
		$options['is_cart_btn']                 = $this->get_option( 'is_cart_btn', 'Y' );
		$options['shadow_opacity']              = $this->get_option( 'shadow_opacity', 35 );

		$options['cart_shadow_opacity'] = $this->get_option( 'cart_shadow_opacity', 35 );
		$options['cart_border_radius']  = $this->get_option( 'cart_border_radius', 0 );

		$options['cart_btn_text']     = $this->get_option( 'cart_btn_text', 'View Full Cart' );
		$options['is_checkout_btn']   = $this->get_option( 'is_checkout_btn', 'Y' );
		$options['checkout_btn_text'] = $this->get_option( 'checkout_btn_text', 'Checkout' );
		$options['empty_txt']         = $this->get_option( 'empty_txt', 'Empty Cart' );
		$options['empty_cart_icon']   = $this->get_option( 'empty_cart_icon', 'ap-empty-cart-2' );
		$options['chk_menus']         = $this->get_option( 'chk_menus', array() );

		$options['custom_css']     = get_option( 'apbd_nmca_ccss', '' );
		$options['customizer_url'] = site_url();
		$options['admin_menu_url'] = admin_url( 'nav-menus.php' );

		$response = new Ajax_Confirm_Response();

		/**
		 * Its for check is there any change before process
		 *
		 * @since 1.0
		 */
		$options = apply_filters( 'appsbd/nmca/filters/get-admin-options', $options );

		$response->display_with_response( true, $options );
	}

	/**
	 * The set default options is generated by appsbd
	 */
	public function set_default_options() {
		$this->options['drawer_type']                 = 'D';
		$this->options['position']                    = 'LB';
		$this->options['skin']                        = 'DF';
		$this->options['color']                       = 'default';
		$this->options['hide_in_cart']                = 'N';
		$this->options['hide_in_checkout']            = 'N';
		$this->options['hide_in_empty_item']          = 'N';
		$this->options['not_hide_in_undo']            = 'N';
		$this->options['show_container_on_each_item'] = 'N';
		$this->options['dont_wc_add_msg']             = 'N';
		$this->options['add_menu_item']               = 'N';
		$this->options['menu_show_icon']              = 'N';
		$this->options['menu_it_text']                = 'My cart';
		$this->options['menu_icon']                   = 'ap-cart';
		$this->options['is_menu_item_counter']        = 'N';
		$this->options['m_counter_type']              = 'I';
		$this->options['control_size']                = 80;
		$this->options['control_icon_size']           = 50;
		$this->options['border_radius']               = 50;
		$this->options['top_margin']                  = 30;
		$this->options['left_margin']                 = 30;
		$this->options['cart_container_size']         = 100;
		$this->options['cart_container_width']        = 450;
		$this->options['icon']                        = 'ap-cart';
		$this->options['df_type']                     = 'TA';
		$this->options['df_amount_type']              = 'S';
		$this->options['dr_anim']                     = 'ape-slideInRight';
		$this->options['dr_anim_out']                 = 'ape-slideOutRight';
		$this->options['hide_corner_circle']          = 'N';
		$this->options['circle_type']                 = 'Q';
		$this->options['title_text']                  = 'My Cart';
		$this->options['is_undo_remove']              = 'Y';
		$this->options['is_show_dis_total']           = 'Y';
		$this->options['is_show_all_fee']             = 'Y';
		$this->options['is_show_cpn']                 = 'Y';
		$this->options['is_show_acpn']                = 'Y';
		$this->options['is_cart_btn']                 = 'Y';
		$this->options['shadow_opacity']              = 35;

		$this->options['cart_shadow_opacity'] = 35;
		$this->options['cart_border_radius']  = 0;

		$this->options['cart_btn_text']     = 'View Full Cart';
		$this->options['is_checkout_btn']   = 'Y';
		$this->options['checkout_btn_text'] = 'Checkout';
		$this->options['empty_txt']         = 'Empty Cart';
		$this->options['empty_cart_icon']   = 'ap-empty-cart-2';
		$this->options['chk_menus']         = array();
		$this->options['hide_pages']        = array();
		$this->update_option();
	}

	/**
	 * The on client scripts lite is generated by appsbd
	 */
	public function on_client_scripts_lite() {

		wp_register_script(
			'apbd-nmca-script',
			$this->get_plugin_url( 'cl-assets/js/client-script.js' ),
			array(),
			$this->kernel_object->plugin_version,
			true
		);
		$cart_color = $this->get_option( 'color', 'default' );
		$colors     = array( 'default', 'cyan', 'black', 'gray', 'orange', 'red', 'pink', 'violet', 'purple', 'green' );
		if ( ! in_array( $cart_color, $colors ) ) {
			$cart_color = 'default';
		}

		wp_register_style(
			'apbd-nmca-font',
			$this->get_plugin_url( 'assets/font.css' ),
			array(),
			$this->kernel_object->plugin_version
		);
		wp_enqueue_style( 'apbd-nmca-font' );

		wp_register_style(
			'apbd-nmca-color',
			$this->get_plugin_url( 'cl-assets/skin/all.css' ),
			array(),
			$this->kernel_object->plugin_version
		);
		wp_enqueue_style( 'apbd-nmca-color' );
		wp_register_style(
			'apbd-nmca-style',
			$this->get_plugin_url( 'cl-assets/css/client-style.css' ),
			array( 'apbd-nmca-color' ),
			$this->kernel_object->plugin_version
		);

		wp_enqueue_script( 'apbd-nmca-script' );
		wp_enqueue_style( 'apbd-nmca-style' );

		$jv_object             = new \stdClass();
		$jv_object->ajax_url   = wp_nonce_url( admin_url( 'admin-ajax.php' ) );
		$jv_object->ajax_nonce = wp_create_nonce( 'minicart' );
		$jv_object->base_slug  = $this->kernel_object->get_action_prefix();
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$jv_object->price_format = array(
				'decimal_separator'  => wc_get_price_decimal_separator(),
				'thousand_separator' => wc_get_price_thousand_separator(),
				'decimals'           => wc_get_price_decimals(),
				'symbol'             => get_woocommerce_currency_symbol(),
				'price_format'       => html_entity_decode(
					str_replace(
						array( '%1$s', '%2$s' ),
						array( get_woocommerce_currency_symbol(), '{{amt}}' ),
						get_woocommerce_price_format()
					)
				),
				'currency_pos'       => get_option( 'woocommerce_currency_pos' ),
			);
		} else {
			$jv_object->price_format = array(
				'decimal_separator'  => '.',
				'thousand_separator' => ',',
				'decimals'           => 2,
				'price_format'       => '${{amt}}',
			);
		}
		$jv_object->admin_data = array(
			'button_setting'  => array(
				'icon'               => $this->get_option( 'icon', 'ap-cart' ),
				'df_type'            => $this->get_option( 'df_type', 'TA' ),
				'amount_type'        => $this->get_option( 'df_amount_type', 'S' ),
				'hide_corner_circle' => $this->get_option( 'hide_corner_circle' ) == 'Y',
				'circle_type'        => $this->get_option( 'circle_type', 'Q' ),
				'ani_in'             => $this->get_option( 'dr_anim', 'ape-jello' ),
				'ani_out'            => $this->get_option( 'dr_anim_out', 'ape-jello' ),
				'control_size'       => $this->get_option( 'control_size', 80 ) . 'px',
				'control_icon_size'  => $this->get_option( 'control_icon_size', 50 ) . 'px',
				'border_radius'      => $this->get_option( 'border_radius', 50 ) . '%',
				'bottom_margin'      => $this->get_option( 'top_margin', 30 ) . 'px',
				'left_margin'        => $this->get_option( 'left_margin', 30 ) . 'px',
				'shadow_opacity'     => $this->get_option( 'shadow_opacity', 35 ),

			),
			'item_setting'    => array(
				'title_text'            => $this->get_option( 'title_text', 'My Cart' ),
				'empty_text'            => $this->get_option( 'empty_txt', 'Empty Text' ),
				'is_undo_remove'        => $this->get_option( 'is_undo_remove' ) == 'Y',
				'is_checkout_btn'       => $this->get_option( 'is_checkout_btn' ) == 'Y',
				'is_cart_btn'           => $this->get_option( 'is_cart_btn' ) == 'Y',
				'is_show_cpn'           => false,
				'is_show_acpn'          => false,
				'is_show_dis_total'     => $this->get_option( 'is_show_dis_total' ) == 'Y',
				'is_show_all_fee'       => $this->get_option( 'is_show_all_fee' ) == 'Y',
				'cart_btn_text'         => $this->get_option( 'cart_btn_text', 'Cart' ),
				'cart_btn_link'         => wc_get_cart_url(),
				'checkout_btn_text'     => $this->get_option( 'checkout_btn_text', 'Checkout' ),
				'checkout_btn_link'     => wc_get_checkout_url(),
				'empty_cart_icon'       => $this->get_option( 'empty_cart_icon', 'ap-empty-cart-2' ),
				'cart_container_height' => $this->get_option( 'cart_container_size', 100 ) . 'vh',
				'cart_container_width'  => $this->get_option( 'cart_container_width', 350 ) . 'px',
				'cart_shadow_opacity'   => $this->get_option( 'cart_shadow_opacity', 35 ),
				'cart_border_radius'    => $this->get_option( 'cart_border_radius', 0 ) . 'px',
			),
			'general_setting' => array(
				'color'              => $cart_color,
				'has_coupon'         => false,
				'skin'               => 'apbd-nmca-skin-' . strtolower( $this->get_option( 'skin', 'DF' ) ),
				'drawer_type'        => $this->get_option( 'drawer_type', 'D' ),
				'position'           => $this->get_option( 'position', 'LM' ),
				'hide_in_cart'       => false,
				'hide_in_checkout'   => false,
				'title_text'         => $this->get_option( 'title_text', 'My Cart' ),
				'cart_btn_text'      => $this->get_option( 'cart_btn_text', 'View Full Cart' ),
				'checkout_btn_text'  => $this->get_option( 'checkout_btn_text', 'Checkout' ),
				'chk_menus'          => $this->get_option( 'chk_menus', array() ),
				'hide_in_empty_item' => false,
				'hide_pages'         => array(),
			),
			'discount'        => array(
				'show_in_mini_cart' => false,
				'position'          => $this->get_option( 'position', 'top' ),
			),
			'sale_booster'    => array(
				'is_enable'              => false,
				'is_custom_color'        => false,
				'pb_complete_color'      => $this->get_option( 'pb_complete_color', '' ),
				'pb_bg_color'            => $this->get_option( 'pb_bg_color', '' ),
				'text_color'             => $this->get_option( 'text_color', '' ),
				'box_shadow_color'       => $this->get_option( 'box_shadow_color', '' ),
				'bg_color'               => $this->get_option( 'bg_color', '' ),
				'is_dd_enable'           => false,
				'is_ddc_enable'          => false,
				'dis_label'              => $this->get_option( 'dis_label', 'Special Discount' ),
				'dis_msg'                => $this->get_option( 'dis_msg', 'Spend more {{calculate_amount}} to get {{offer_value}} discount' ),
				'dis_msg_free'           => $this->get_option( 'dis_msg_free', 'Spend more {{calculate_amount}} to get free shipping' ),
				'is_cart_custom_color'   => false,
				'cart_position'          => $this->get_option( 'cart_position', 'top' ),
				'cart_pb_bg_color'       => $this->get_option( 'cart_pb_bg_color', '' ),
				'cart_pb_complete_color' => $this->get_option( 'border-bottom', '' ),
				'cart_text_color'        => $this->get_option( 'cart_text_color', '' ),
				'cart_bg_color'          => $this->get_option( 'cart_bg_color', '' ),
			),
			'rules'           => array(),

		);
		$jv_object->labels = App_Language::get_client_languages( $this->kernel_object );
		/**
		 * Its for check is there any change before process
		 *
		 * @since 1.0
		 */
		$jv_object = apply_filters( 'appsbd/nmca/settings', $jv_object );

		wp_localize_script( 'apbd-nmca-script', 'apbd_nmca', (array) $jv_object );
	}

	/**
	 * The on client styles lite is generated by appsbd
	 */
	public function on_client_styles_lite() {
	}

	/**
	 * The cart items fragment is generated by appsbd
	 *
	 * @param mixed $fragments Its fragments.
	 *
	 * @return mixed
	 */
	public function cart_items_fragment( $fragments ) {

		$fragments['mca_items'] = Mca_Client_Data::get_mca_data();

		return $fragments;
	}

	/**
	 * The wp footer is generated by appsbd
	 */
	public function wp_footer() {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return;

		}
		/**
		 * Its for check is there any change before process
		 *
		 * @since 1.0
		 */
		$is_displayable = apply_filters( 'appsbd/nmca/is-client-display', true );
		if ( empty( $is_displayable ) ) {
			return;
		}
		?>
		<script type="text/javascript">
			"use strict";
			let mca_data =<?php echo json_encode( Mca_Client_Data::get_mca_data() ); ?>;
			try {
				jQuery(document).ready(function ($) {
					try {
						window.mca_update(mca_data);
					} catch (e) {

					}
				});
			} catch (e) {
				document.addEventListener("DOMContentLoaded", function (event) {
					window.mca_update(mca_data);
				});
			}
		</script>
		<div id="apbd-mini-cart"></div>
		<?php
	}

	/**
	 * The get module option is generated by appsbd
	 *
	 * @param string $key It is key.
	 * @param string $default It is default.
	 *
	 * @return mixed|string
	 */
	public static function get_module_option( $key = '', $default = '' ) {
		return parent::get_module_option( $key, $default );     }
}
