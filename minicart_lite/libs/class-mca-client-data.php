<?php
/**
 * Its used for Client Data
 *
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package Minicart_Lite\Libs
 */

namespace Minicart_Lite\Libs;

use MiniCart_lite\Core\Minicart_Lite;
use Minicart_Lite\Modules\MCN_Settings;
use Symfony\Component\Validator\Constraints\Time;

/**
 * Class Mca_Client_Data
 *
 * @package Minicart_Lite\Libs
 */
class Mca_Client_Data {

	/**
	 * Its property msg
	 *
	 * @var Mca_Client_Msg
	 */
	public $msg;

	/**
	 * Its property cart_items
	 *
	 * @var array
	 */
	public $cart_items = array();

	/**
	 * Its property coupuns
	 *
	 * @var array
	 */
	public $coupons = array();
	/**
	 * Its property sub_total.
	 *
	 * @var string
	 */
	public $sub_total = '';
	/**
	 * Its property cart_total
	 *
	 * @var string
	 */
	public $cart_total = '';

	/**
	 * Its property total_lines
	 *
	 * @var array
	 */
	public $total_lines = array();
	/**
	 * Its property undo_product
	 *
	 * @var \stdClass
	 */
	public $undo_product;
	/**
	 * Its property ts
	 *
	 * @var int
	 */
	public $ts = 0;

	/**
	 * Mca_Client_Data constructor.
	 */
	public function __construct() {
	}

	/**
	 * The    is generated by appsbd
	 *
	 * @param mixed $str Its str param.
	 * @param mixed ...$args Its args.
	 *
	 * @return mixed
	 */
	public function __( $str, ...$args ) {
		return Minicart_Lite::get_instance()->__( $str, $args );
	}

	/**
	 * The set from wc cart is generated by appsbd
	 */
	public function set_from_wc_cart() {
		$this->ts = time();
		WC()->cart->calculate_totals();
		$cart  = WC()->cart;
		$items = $cart->get_cart();
		$fees  = $cart->get_fees();
		foreach ( $items as $cart_item_key => $cart_item ) {
			/**
			 * Its for check is there any change before process
			 *
			 * @since 4.0
			 */
			$wc_product = apply_filters(
				'woocommerce_cart_item_product',
				$cart_item['data'],
				$cart_item,
				$cart_item_key
			);
			/**
			 * Its for check is there any change before process
			 *
			 * @since 4.0
			 */
			$wc_product_id = apply_filters(
				'woocommerce_cart_item_product_id',
				$cart_item['product_id'],
				$cart_item,
				$cart_item_key
			);

			/**
			 * Its for check is there any change before process
			 *
			 * @since 4.0
			 */
			$wc_product_link = apply_filters(
				'woocommerce_cart_item_permalink',
				$wc_product->is_visible() ? $wc_product->get_permalink( $cart_item ) : '',
				$cart_item,
				$cart_item_key
			);

			$item_name = $wc_product->get_name();

			$mca_item             = new \stdClass();
			$mca_item->id         = $cart_item_key;
			$mca_item->product_id = $wc_product_id;
			$mca_item->link       = $wc_product_link;

			$cart_item_quantity = $cart_item['quantity'];
			if ( ! MCN_Settings::get_module_instance()->is_valid_quantity(
				$cart_item['data'],
				$cart_item_quantity,
				$cart_item_quantity - 1
			) ) {
				$mca_item->is_dis_de = true;
			}
			if ( ! MCN_Settings::get_module_instance()->is_valid_quantity(
				$cart_item['data'],
				$cart_item_quantity,
				$cart_item_quantity + 1
			) ) {
				$mca_item->is_dis_in = true;
			}
			/**
			 * Its for check is there any change before process
			 *
			 * @since 4.0
			 */
			$mca_item->title = apply_filters(
				'woocommerce_cart_item_name',
				$item_name,
				$cart_item,
				$cart_item_key
			);
			if ( wc_prices_include_tax() ) {
				$mca_item->line_total = wc_price( $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'] );
			} else {
				$mca_item->line_total = wc_price( $cart_item['line_subtotal'] );
			}

			$mca_item->line_total_tax = wc_price( $cart_item['line_subtotal_tax'] );
			/**
			 * Its for check is there any change before process
			 *
			 * @since 4.0
			 */
			$mca_item->price       = apply_filters(
				'woocommerce_cart_item_price',
				WC()->cart->get_product_price( $wc_product ),
				$cart_item,
				$cart_item_key
			);
			$mca_item->item_tax    = wc_price( $cart_item['line_subtotal_tax'] / $cart_item_quantity );
			$mca_item->image       = get_the_post_thumbnail_url( $wc_product_id );
			$mca_item->description = '';
			$mca_item->quantity    = $cart_item_quantity;
			$this->add_cart_item( $mca_item );
		};

		$total = WC()->cart->get_total( '' );
		if ( ! wc_prices_include_tax() ) {
			$subtotal       = WC()->cart->get_subtotal();
			$discount_total = WC()->cart->get_cart_discount_total();
		} else {
			$subtotal       = WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax();
			$discount_total = WC()->cart->get_cart_discount_total() + WC()->cart->get_cart_discount_tax_total();
		}
		$inserted_other_entry = false;

		$shipping_total = WC()->cart->get_shipping_total();
		$fee_total      = WC()->cart->get_fee_total();
		$taxes_total    = WC()->cart->get_taxes_total();

		$is_show_dis_total = MCN_Settings::get_module_option( 'is_show_dis_total', 'N' ) == 'Y';
		$is_show_all_fee   = MCN_Settings::get_module_option( 'is_show_all_fee', 'N' ) == 'Y';
						$cart_total = $total;
		$this->add_total_line( 'Sub Total', $subtotal );
		$this->sub_total   = (float) $subtotal;
		$this->cart_total  = (float) $cart_total;
		$this->coupons     = WC()->cart->get_applied_coupons();
		$this->fees        = $fees;
		$dynamic_discounts = array();
		$dynamic_fees      = array();
				$fee_total = 0;
		$has_dd    = false;
		foreach ( $fees as $dkey => $fee ) {
			if ( $fee->total < 0 ) {
				if ( wc_prices_include_tax() ) {
					$fee->total = $fee->total + $fee->tax;
				}
				$this->add_total_line( $fee->name, $fee->total, false, true );
				$has_dd = true;
			}
		}

		if ( $is_show_dis_total ) {
			if ( $discount_total > 0 ) {
				$this->add_total_line( 'Discount', $discount_total );
			}

			if ( $is_show_all_fee ) {

				if ( $shipping_total > 0 ) {
					$this->add_total_line( 'Shipping', $shipping_total );
				}
				if ( $fee_total > 0 ) {
					$this->add_total_line( 'Total Fee', $fee_total );
				}
				if ( ! wc_prices_include_tax() && $taxes_total > 0 ) {
					$this->add_total_line( 'Tax Total', WC()->cart->get_taxes_total() );
				}

				$this->add_total_line(
					'Total',
					/**
					 * Its for check is there any change before process
					 *
					 * @since 4.0
					 */
					apply_filters( 'woocommerce_cart_total', wc_price( $total ) ),
					true
				);
			}
		}

		$remove_contains = WC()->cart->get_removed_cart_contents();
		if ( ! empty( $remove_contains ) ) {
			$last_removed = end( $remove_contains );
			WC()->cart->set_removed_cart_contents( array( $last_removed ) );
			if ( ! empty( $last_removed ) ) {
				$wc_product = wc_get_product( $last_removed['product_id'] );
				if ( ! empty( $wc_product ) ) {
					/**
					 * Its for check is there any change before process
					 *
					 * @since 4.0
					 */
					$wc_product_link = apply_filters(
						'woocommerce_cart_item_permalink',
						$wc_product->is_visible() ? $wc_product->get_permalink( $last_removed ) : '',
						$last_removed,
						$last_removed['key']
					);

					$undo_obj           = new \stdClass();
					$undo_obj->id       = $last_removed['key'];
					$undo_obj->link     = $wc_product_link;
					$undo_obj->title    = $wc_product->get_title() . ' -' . $this->__( 'Removed' );
					$undo_obj->image    = get_the_post_thumbnail_url( $wc_product->get_id() );
					$this->undo_product = $undo_obj;
				}
			}
		} else {
			$this->undo_product = null;
		}
	}


	/**
	 * The get mca data is generated by appsbd
	 *
	 * @return Mca_Client_Data
	 */
	public static function get_mca_data() {
		$obj = new self();
		$obj->set_from_wc_cart();

		return $obj;
	}

	/**
	 * The set msg is generated by appsbd
	 *
	 * @param mixed $msg_str Its msg_str param.
	 * @param mixed $msg_per Its msg_per param.
	 *
	 * @return $this
	 */
	public function set_msg( $msg_str, $msg_per ) {
		$msg          = new \stdClass();
		$msg->msg     = $msg_str;
		$msg->msg_per = $msg_per;
		$this->msg    = $msg;

		return $this;
	}

	/**
	 * The disable msg is generated by appsbd
	 */
	public function disable_msg() {
		$this->msg = null;
	}

	/**
	 * The add cart item is generated by appsbd
	 *
	 * @param mixed $cart_item Its cart item.
	 *
	 * @return $this
	 */
	public function add_cart_item( $cart_item ) {
		$this->cart_items[] = $cart_item;

		return $this;
	}

	/**
	 * The add coupuns is generated by appsbd
	 *
	 * @param mixed $coupon Its coupon param.
	 *
	 * @return $this
	 */
	public function add_coupuns( $coupon ) {
		$this->coupons[] = $coupon;

		return $this;
	}

	/**
	 * The add total line is generated by appsbd
	 *
	 * @param mixed $title Its title param.
	 * @param mixed $amount Its amount param.
	 * @param bool  $is_formated Its is_formated param.
	 * @param bool  $is_translated Its is_translated param.
	 *
	 * @return $this
	 */
	public function add_total_line( $title, $amount, $is_formated = false, $is_translated = false ) {
		$item = new \stdClass();
		if ( ! $is_translated ) {
			$item->title = $this->__( $title );
		} else {
			$item->title = $title;
		}
		if ( $is_formated ) {
			$item->val = $amount;
		} else {
			$item->val = wc_price( $amount );
		}
		$this->total_lines[] = $item;

		return $this;
	}
}