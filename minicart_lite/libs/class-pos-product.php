<?php
/**
 * Its pos product model
 *
 * @since: 21/09/2021
 * @author: Sarwar Hasan
 * @version 1.0.0
 * @package VitePos_Lite\Libs
 */

namespace VitePos_Lite\Libs;

use Automattic\WooCommerce\Utilities\NumberUtil;
use VitePos_Lite\Models\Database\Mapbd_Post;
use VitePos_Lite\Models\Database\Mapbd_Pos_Warehouse;
use VitePos_Lite\Models\Database\Mapbd_postmeta;
use VitePos_Lite\Modules\POS_Settings;


/**
 * Class POS Product
 *
 * @package VitePos_Lite\Libs
 */
class POS_Product {

	/**
	 * Its property id.
	 *
	 * @var int
	 */
	public $id;                 /**
								 * Its property barcode.
								 *
								 * @var int
								 */
	public $barcode;            /**
								 * Its property name.
								 *
								 * @var string
								 */
	public $name;
	/**
	 * Its property is new.
	 *
	 * @var boolean
	 */
	public $is_new;
	/**
	 * Its property image.
	 *
	 * @var string
	 */
	public $image;
	/**
	 * Its property image.
	 *
	 * @var array
	 */
	public $image_gallery;

	/**
	 * Its property sale_price.
	 *
	 * @var float
	 */
	public $sale_price;         /**
								 * Its property regular_price.
								 *
								 * @var float
								 */
	public $regular_price;      /**
								 * Its property price_html.
								 *
								 * @var string
								 */
	public $price_html;             /**
									 * Its property price.
									 *
									 * @var float
									 */
	public $price;              /**
								 * Its property cross_sale.
								 *
								 * @var string
								 */
	public $cross_sale;         /**
								 * Its property up_sale.
								 *
								 * @var string
								 */
	public $up_sale;            /**
								 * Its property attributes.
								 *
								 * @var string
								 */
	public $attributes;
	/**
	 * Its property variations.
	 *
	 * @var string
	 */
	public $variations;         /**
								 * Its property group_product.
								 *
								 * @var string
								 */
	public $group_product;      /**
								 * Its property parent_product.
								 *
								 * @var string
								 */
	public $parent_product;
	/**
	 * Its property status.
	 *
	 * @var string
	 */
	public $status;
	/**
	 * Its property manage_stock.
	 *
	 * @var string
	 */
	public $manage_stock;
	/**
	 * Its property stock_quantity.
	 *
	 * @var int
	 */
	public $stock_quantity;
	/**
	 * Its property stock_status.
	 *
	 * @var bool
	 */
	public $stock_status;
	/**
	 * Its property low_stock_amount.
	 *
	 * @var int
	 */
	public $low_stock_amount;
	/**
	 * Its property purchasable.
	 *
	 * @var string
	 */
	public $purchasable;        /**
								 * Its property average_rating.
								 *
								 * @var int
								 */
	public $average_rating;
	/**
	 * Its property rating_count.
	 *
	 * @var int
	 */
	public $rating_count;       /**
								 * Its property slug.
								 *
								 * @var string
								 */
	public $slug;       /**
						 * Its property sku.
						 *
						 * @var string
						 */
	public $sku;                /**
								 * Its property description.
								 *
								 * @var string
								 */
	public $description;        /**
								 * Its property purchase_cost.
								 *
								 * @var int
								 */
	public $purchase_cost;      /**
								 * Its property taxable.
								 *
								 * @var bool
								 */

	public $is_favorite = 'N';
	/**
	 * Its property taxable.
	 *
	 * @var bool
	 */

	public $taxable = false;
	/**
	 * Its property tax_status
	 *
	 * @var bool
	 */
	public $tax_status;
	/**
	 * Its property tax_class
	 *
	 * @var string
	 */
	public $tax_class;

			/**
			 * Its property type.
			 *
			 * @var string
			 */
	public $type;
	/**
	 * Its property weight.
	 *
	 * @var float
	 */
	public $weight;
	/**
	 * Its property height.
	 *
	 * @var float
	 */
	public $height;
	/**
	 * Its property width.
	 *
	 * @var float
	 */
	public $width;
	/**
	 * Its property length.
	 *
	 * @var float
	 */
	public $length;
	/**
	 * Its addons.
	 *
	 * @var array
	 */
	public $addons = array();

	/**
	 * The set search props is generated by appsbd
	 *
	 * @param any $filter Its string.
	 * @param any $src_props Its string.
	 */
	public static function set_search_props( &$filter, $src_props ) {
		 global $wpdb;
		if ( ! empty( $src_props ) ) {
			$filter['api_src'] = array();
			foreach ( $src_props as $src_prop ) {
				if ( '*' == $src_prop['prop'] ) {
					$filter['meta_query'] ['relation'] = 'OR';
					$filter['meta_query'][]            = array(
						'key'     => '_sku',
						'value'   => esc_sql( $src_prop['val'] ),
						'compare' => 'like',
					);
					$filter['meta_query'][]            = array(
						'key'     => '_sku',
						'value'   => esc_sql( $src_prop['val'] ),
						'compare' => 'not like',
					);
				}
				if ( '_vt_is_favorite' == $src_prop['prop'] ) {
					if ( 'Y' != $src_prop['val'] ) {
						$filter['meta_query'] ['relation'] = 'OR';
						$filter['meta_query'][]            = array(
							'key'     => '_vt_is_favorite',
							'value'   => esc_sql( $src_prop['val'] ),
							'compare' => '=',
						);
						$filter['meta_query'][]            = array(
							'key'     => '_vt_is_favorite',
							'value'   => esc_sql( $src_prop['val'] ),
							'compare' => 'NOT EXISTS',
						);
					} else {
						$filter['meta_query'][] = array(
							'key'     => '_vt_is_favorite',
							'value'   => esc_sql( $src_prop['val'] ),
							'compare' => '=',
						);
					}
				}
				if ( '_vt_purchase_price_change' == $src_prop['prop'] ) {
					if ( 'Y' == $src_prop['val'] ) {
						$filter['meta_query'][] = array(
							'key'     => '_vt_purchase_price_change',
							'value'   => esc_sql( $src_prop['val'] ),
							'compare' => '=',
						);
					}
				}
				if ( ! empty( $src_prop['prop'] ) && isset( $src_prop['val'] ) ) {
					if ( 'category_id' == $src_prop['prop'] && isset( $src_prop['val'] ) && 'all_cat' != $src_prop['val'] ) {
						$filter['tax_query'] = array(
							array(
								'taxonomy' => 'product_cat',
								'field'    => 'term_id',
								'terms'    => array( (int) $src_prop['val'] ),
								'operator' => 'IN',
							),
						);
					} elseif ( 'category' == $src_prop['prop'] && isset( $src_prop['val'] ) && 'all_cat' != $src_prop['val'] ) {
						$filter['tax_query'] = array(
							array(
								'taxonomy' => 'product_cat',
								'field'    => 'slug',
								'terms'    => array( $src_prop['val'] ),
								'operator' => 'IN',
							),
						);
					} elseif ( 'manage_stock' == $src_prop['prop'] && isset( $src_prop['val'] ) ) {
						$filter['meta_query'][] = array(
							'key'     => '_manage_stock',
							'value'   => ! empty( $src_prop['val'] ) ? 'yes' : 'no',
							'compare' => '=',
						);
					} elseif ( 'name' == $src_prop['prop'] || '*' == $src_prop['prop'] ) {
						$filter['meta_query'] ['relation'] = 'OR';
						$filter['meta_query'][]            = array(
							'key'     => '_sku',
							'compare' => 'EXISTS',
						);
						$src_prop['val']                   = trim( $src_prop['val'] );
						$filter['api_src'][]               = ' AND ( 
						(' . $wpdb->postmeta . ".meta_key = '_sku' AND " . $wpdb->postmeta . ".meta_value like '%" . esc_sql( $wpdb->esc_like( $src_prop['val'] ) ) . "%' ) 
						OR (" . $wpdb->posts . ".post_title LIKE '%" . esc_sql( $wpdb->esc_like( $src_prop['val'] ) ) . "%') OR (" . $wpdb->posts . ".ID = '" . esc_sql( $src_prop['val'] ) . "'))";

					} elseif ( 'price' == $src_prop['prop'] ) {
						$filter['meta_key'] = '_price';
						if ( 'bt' == $src_prop['opr'] && isset( $src_prop['val'] ) ) {
							if ( isset( $src_prop['val'] ) && isset( $src_prop['val']['start'] ) && '' != $src_prop['val']['start'] ) {
								$filter['meta_query'][] = array(
									'key'     => '_price',
									'value'   => floatval( $src_prop['val']['start'] ),
									'compare' => '>=',
									'type'    => 'NUMERIC',
								);
							}
							if ( isset( $src_prop['val'] ) && ! empty( $src_prop['val']['end'] ) ) {
								$filter['meta_query'][] = array(
									'key'     => '_price',
									'value'   => floatval( $src_prop['val']['end'] ),
									'compare' => '<=',
									'type'    => 'NUMERIC',
								);
							}
						} elseif ( in_array( $src_prop['opr'], array( 'gt', 'lt', 'ge', 'le', 'eq' ) ) && isset( $src_prop['val'] ) ) {
							$opr                    = array(
								'eq' => '=',
								'gt' => '>',
								'lt' => '<',
								'ge' => '>=',
								'le' => '<=',
							);
							$filter['meta_query'][] = array(
								'key'     => '_price',
								'value'   => floatval( $src_prop['val'] ),
								'compare' => ! empty( $opr[ $src_prop['opr'] ] ) ? $opr[ $src_prop['opr'] ] : '>=',
								'type'    => 'NUMERIC',
							);
						}
					}
				}
			}
		}
	}

	/**
	 * The set sort props is generated by appsbd
	 *
	 * @param any $props Sorting property.
	 * @param any $sort_param Sorting params.
	 */
	public static function set_sort_props( $props, &$sort_param ) {
		foreach ( $props as $prop ) {
			if ( ! empty( $prop['prop'] ) ) {
				if ( 'is_favorite' == $prop['prop'] ) {
					$prop['prop'] = '_vt_is_favorite';
				}
				$prop['prop'] = strtolower( trim( $prop['prop'] ) );
				$prop['ord']  = strtolower( trim( $prop['ord'] ) );
				if ( in_array( $prop['ord'], array( 'asc', 'desc' ) ) ) {
					$sort_param['orderby'] = $prop['prop'];
					$sort_param['order']   = $prop['ord'];
				}
			}
		}
	}

	/**
	 * Its a function get_product_from_woo_products
	 *
	 * @param int   $page Its int.
	 * @param int   $limit Its int.
	 * @param array $src_props Its array.
	 * @param array $sort_props Its array.
	 *
	 * @return stdClass
	 */
	public static function get_product_from_woo_products( $page = 1, $limit = 10, $src_props = array(), $sort_props = array() ) {
		$filter          = array();
		$filter['page']  = $page;
		$filter['limit'] = $limit;
		$filter          = self::query_args( $filter );
		if ( ! empty( $sort_props ) ) {
			self::set_sort_props( $sort_props, $filter );
		} else {
			$filter['orderby'] = 'id';
			$filter['order']   = 'asc';
		}
		self::set_search_props( $filter, $src_props );
		$product_query         = new \WP_Query( $filter );
		$product_obj           = new \stdClass();
		$product_obj->products = array();
		$product_obj->records  = $product_query->found_posts;
		foreach ( $product_query->posts as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( ! empty( $product ) ) {
				if ( $product instanceof \WC_Product_Variation ) {
					$product = wc_get_product( $product->get_parent_id() );
				}
				if ( $product->get_type() !== 'grouped' ) {
					$pos_product = self::get_product_data( $product );
				}
				if ( ! empty( $pos_product ) ) {
					$product_obj->products[] = $pos_product;
				}
			}
		}
		return $product_obj;
	}

	/**
	 * The get product from woo products with variations is generated by appsbd
	 *
	 * @param int   $page Its Page id.
	 * @param int   $limit Its limit.
	 * @param array $src_props Its other search property.
	 * @param array $orders Its orders property.
	 *
	 * @return \stdClass
	 */
	public static function get_product_from_woo_products_with_variations( $page = 1, $limit = 10, $src_props = array(), $orders = array() ) {
		$filter              = array();
		$filter['page']      = $page;
		$filter['limit']     = $limit;
		$filter              = self::query_args( $filter );
		$filter['post_type'] = array( 'product', 'product_variation' );
		self::set_search_props( $filter, $src_props );
		self::set_sort_props( $orders, $filter );
		$product_query         = new \WP_Query( $filter );
		$product_obj           = new \stdClass();
		$product_obj->products = array();
		$product_obj->records  = $product_query->found_posts;
		foreach ( $product_query->posts as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( ! empty( $product ) ) {
				if ( $product->get_type() !== 'grouped' ) {
					$pos_product = self::get_product_variation_data( $product, false, true );
				}
				$product_obj->products[] = $pos_product;
			}
		}
		return $product_obj;
	}

	/**
	 * The get categories name is generated by appsbd
	 *
	 * @param any    $category Its a category param.
	 * @param string $seperator Its separator param.
	 *
	 * @return string
	 */
	public static function get_categories_name( $category, $seperator = ' » ' ) {
		if ( $category instanceof \WP_Term ) {
			if ( empty( $category->parent ) ) {
				return $category->name;
			} else {
				$parent_category = get_term( $category->parent, 'product_cat' );
				return self::get_categories_name( $parent_category, $seperator ) . $seperator . $category->name;
			}
		} elseif ( is_string( $category ) ) {
			return $category;
		} else {
			return '';
		}
	}
	/**
	 * The get categroies is generated by appsbd
	 *
	 * @param false $is_parent Is parent category.
	 * @param bool  $hide_empty its hide emplt param.
	 *
	 * @return array
	 */
	public static function get_categories( $is_parent = false, $hide_empty = true ) {
		$orderby  = 'name';
		$order    = 'asc';
		$cat_args = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
		);
		if ( ! $is_parent ) {
			$cat_args['parent'] = 0;
		}
		$product_categories = get_terms( 'product_cat', $cat_args );
		$final_response     = array();
		if ( ! empty( $product_categories ) ) {
			foreach ( $product_categories as $key => $category ) {
				$el_category     = new Product_Category();
				$el_category->id = $category->term_id;
				if ( $is_parent ) {
					$el_category->name = self::get_categories_name( $category );
				} else {
					$el_category->name = $category->name;
				}
				$el_category->slug             = $category->slug;
				$el_category->image            = wp_get_attachment_url( $category->term_id );
				$el_category->parent_id        = $category->parent;
				$el_category->product_count    = $category->count;
				$el_category->taxonomy         = $category->taxonomy;
				$el_category->term_taxonomy_id = $category->term_taxonomy_id;
				if ( ! $is_parent ) {
					$el_category->child = array();
					$el_category->child = self::get_sub_categories( $category->term_id );
				}

				$final_response[] = $el_category;
				unset( $product_categories[ $key ] );
			}
		}
		return $final_response;
	}

	/**
	 * The get sub categories is generated by appsbd
	 *
	 * @param null $parent Its parent.
	 *
	 * @return array
	 */
	public static function get_sub_categories( $parent = null ) {
		$category  = array();
		$child_arg = array(
			'hide_empty' => false,
			'parent'     => $parent,
		);
		$child_cat = get_terms( 'product_cat', $child_arg );
		foreach ( $child_cat as $key => $item ) {
			if ( $item->parent == $parent ) {
				if ( ! isset( $item->child ) ) {
					$item->child = array();
				}
				$item->is_selected = '';
				$item->child       = self::get_sub_categories( $item->term_id );
				$item->id          = $item->term_id;
				unset( $item->term_id );
				$category[] = $item;
			}
		}
		return $category;
	}

	/**
	 * The set barcode of product is generated by appsbd
	 *
	 * @param \WC_Product $product Its wc product.
	 * @param null        $parent_product Its parent product if exists.
	 *
	 * @return int|string
	 */
	public static function get_barcode_of_product( $product, &$parent_product = null ) {
		if ( ! ( $product instanceof \WC_Product ) ) {
			return '';
		}
		$barcode_type = POS_Settings::get_module_option( 'barcode_field' );
		if ( 'CUS' == $barcode_type ) {
			return $product->get_meta( '_vt_barcode' );
		} elseif ( 'SKU' == $barcode_type ) {
			return $product->get_sku();
		} else {
			return $product->get_id();
		}
	}
	/**
	 * The get product data is generated by appsbd
	 *
	 * @param any   $product Its string.
	 * @param false $add_ctg_id Its bool.
	 * @param false $is_no_variable Its bool.
	 *
	 * @return POS_Product
	 */
	public static function get_product_data( $product, $add_ctg_id = false, $is_no_variable = false ) {
		if ( ! ( $product instanceof \WC_Product ) ) {
			return;
		}

		$pos_product                = new self();
		$pos_product->id            = $product->get_id();
		$pos_product->type          = $product->get_type();
		$pos_product->name          = $product->get_title();
		$pos_product->image         = self::get_wc_product_image( $product, 'woocommerce_thumbnail' );
		$pos_product->image_gallery = array();
		$pos_product->outlet_id     = 0;

		/**
		 * Its for product feature update
		 *
		 * @since 1.0
		 */
		$outlet_info = apply_filters( 'vitepos/filter/current-outlet', null );
		if ( ! empty( $outlet_info->id ) ) {
			$pos_product->outlet_id = absint( $outlet_info->id );
		}
		$attachment_ids = $product->get_gallery_image_ids();
		foreach ( $attachment_ids as $attachment_id ) {
			$image                        = new \stdClass();
			$image->id                    = $attachment_id;
			$image->url                   = wp_get_attachment_url( $attachment_id );
			$pos_product->image_gallery[] = $image;
		}
		$pos_product->price_html     = $product->get_price_html();
		$pos_product->status         = $product->get_status();
		$pos_product->price          = $product->get_price();
		$pos_product->description    = $product->get_description();
		$pos_product->regular_price  = $product->get_regular_price();
		$pos_product->stock_quantity = $product->get_stock_quantity() ? $product->get_stock_quantity() : 0;
		$pos_product->sale_price     = $product->get_sale_price() ? $product->get_sale_price() : 0;
		$pos_product->cross_sale     = array_map( 'absint', $product->get_cross_sell_ids() );
		$pos_product->up_sale        = array_map( 'absint', $product->get_upsell_ids() );
		$categories                  = wc_get_object_terms( $product->get_id(), 'product_cat' );
		foreach ( $categories as $category ) {
			$pos_product->category_ids[] = $category->term_id;
			if ( $add_ctg_id ) {
				$pos_product->categories[] = $category->term_id;
			} else {
				$pos_product->categories[] = $category->name;
			}
		}
		$pos_product->low_stock_amount = $product->get_low_stock_amount();
		$pos_product->manage_stock     = $product->get_manage_stock();
		$pos_product->stock_status     = $product->get_stock_status();
		$pos_product->sku              = $product->get_sku();
		$pos_product->taxable          = $product->is_taxable() ? 'Y' : 'N';
		$pos_product->tax_status       = $product->get_tax_status();
		$pos_product->tax_class        = $product->get_tax_class();
		$pos_product->height           = $product->get_height();
		$pos_product->width            = $product->get_width();
		$pos_product->length           = $product->get_length();
		$pos_product->weight           = $product->get_weight();
		$pos_product->is_new           = false;
		$duration                      = (int) POS_Settings::get_module_option( 'new_badge_duration', 0 );
		$create_date                   = $product->get_date_created();
		if ( ! empty( $create_date ) ) {
			$todate = strtotime( "+ {$duration} DAYS", $create_date->getTimestamp() );
			if ( $todate >= time() ) {
				$pos_product->is_new = true;
			}
		}
		$pos_product->tax_rate = NumberUtil::round( ( floatval( wc_get_price_including_tax( $product ) ) - floatval( $pos_product->price ) ), wc_get_price_decimals() );
		if ( ! $is_no_variable ) {
			if ( $product->is_type( 'variable' ) && $product->has_child() ) {
				$pos_product->variations = self::get_variation_data( $product );

			} else {

				$pos_product->variations = array();
			}
		}

		$pos_product->attributes = self::get_attributes( $product );
		$pos_product->barcode    = (string) self::get_barcode_of_product( $product, $parent_product );

		if ( $parent_product instanceof \WC_Product ) {
			$pos_product->parent_product = self::get_product_data( $parent_product );
		}

		if ( $product->is_type( 'grouped' ) && $product->has_child() ) {
			$pos_product->group_product = self::get_grouped_products_data( $product );
		}
		$pos_product->rating_count   = $product->get_rating_count();
		$pos_product->average_rating = wc_format_decimal( $product->get_average_rating(), 2 );
		$pos_product->slug           = $product->get_slug();
		$pos_product->sku            = $product->get_sku();
		$pos_product->purchasable    = 'Y';
		$pos_product->purchase_cost  = $product->meta_exists( '_vt_purchase_cost' ) ? $product->get_meta( '_vt_purchase_cost' ) : 0;

		if ( $product->meta_exists( '_vt_is_favorite' ) ) {
			$pos_product->is_favorite = $product->get_meta( '_vt_is_favorite' );
		} else {
			$pos_product->is_favorite = 'N';
		}
		if ( POS_Settings::is_restaurant_mode() ) {
			/**
			 * Its for check is there any change before process
			 *
			 * @since 2.0
			 */
			$pos_product->addons = apply_filters( 'vitepos/filter/product-details', array(), $pos_product, $product );
		}
		return $pos_product;
	}

	/**
	 * The get product variation data is generated by appsbd
	 *
	 * @param mixed $product Its wc product.
	 *
	 * @return \stdClass|void
	 */
	public static function get_product_variation_data( $product ) {
		if ( ! ( $product instanceof \WC_Product ) ) {
			return;
		}
		$pos_product            = new \stdClass();
		$pos_product->id        = $product->get_id();
		$pos_product->outlet_id = 0;
		/**
		 * Its for product feature update
		 *
		 * @since 1.0
		 */
		$outlet_info = apply_filters( 'vitepos/filter/current-outlet', null );
		if ( ! empty( $outlet_info->id ) ) {
			$pos_product->outlet_id = absint( $outlet_info->id );
		}
		$pos_product->barcode          = self::get_barcode_of_product( $product, $parent_product );
		$pos_product->type             = $product->get_type();
		$pos_product->name             = $product->get_name();
		$pos_product->image            = self::get_wc_product_image( $product, 'woocommerce_thumbnail' );
		$pos_product->price            = empty( $product->get_price() ) ? 0 : $product->get_price();
		$pos_product->regular_price    = empty( $product->get_regular_price() ) ? 0 : $product->get_regular_price();
		$pos_product->sale_price       = $product->get_sale_price() ? $product->get_sale_price() : 0;
		$pos_product->manage_stock     = $product->get_manage_stock();
		$pos_product->stock_status     = $product->get_stock_status();
		$pos_product->stock_quantity   = $product->get_stock_quantity() ? $product->get_stock_quantity() : 0;
		$pos_product->low_stock_amount = $product->get_low_stock_amount();
		$pos_product->parent_product   = null;

		if ( $parent_product instanceof \WC_Product ) {
			$pos_product->parent_product          = new \stdClass();
			$pos_product->parent_product->id      = $parent_product->get_id();
			$pos_product->parent_product->barcode = self::get_barcode_of_product( $parent_product );
			$pos_product->parent_product->type    = $parent_product->get_type();
			$pos_product->parent_product->name    = $parent_product->get_name();
		}

		$pos_product->slug               = $product->get_slug();
		$pos_product->sku                = $product->get_sku();
		$pos_product->purchasable        = 'Y';
		$pos_product->purchase_cost      = $product->meta_exists( '_vt_purchase_cost' ) ? $product->get_meta( '_vt_purchase_cost' ) : '0.00';
		$pos_product->prev_purchase_cost = $product->meta_exists( '_vt_prev_purchase_price' ) ? $product->get_meta( '_vt_prev_purchase_price' ) : '0.00';

		return $pos_product;
	}
	/**
	 * The getAttributes is generated by appsbd
	 *
	 * @param any $product Its string.
	 *
	 * @return array
	 */
	public static function get_attributes2( &$product ) {
		$return_attributes = array();
		if ( $product->is_type( 'variable' ) ) {
			foreach ( $product->get_available_variations() as $key => $variation ) {

				foreach ( $variation['attributes'] as $attribute => $term_slug ) {
										$taxonmomy = str_replace( 'attribute_', '', $attribute );

										$attr_label_name = wc_attribute_label( $taxonmomy );

										$term_name = get_term_by( 'slug', $term_slug, $taxonmomy );
					if ( ! empty( $term_name->name ) ) {
						$attr_label_name = $term_name->name;
					} else {
						$attr_label_name = $term_slug;
					}
					if ( ! isset( $pos_product->attributes[ $taxonmomy ] ) ) {
						$return_attributes[ $taxonmomy ] = array();
					}
					$return_attributes[ $taxonmomy ][ $term_slug ] = $attr_label_name;

				}
			}
		}
		return $return_attributes;
	}
	/**
	 * Get grouped products data
	 *
	 * @param WC_Product $product Its string.
	 *
	 * @return array
	 * @since  2.5.0
	 */
	private static function get_grouped_products_data( $product ) {
		 $products = array();

		foreach ( $product->get_children() as $child_id ) {
			$_product = wc_get_product( $child_id );

			if ( ! $_product || ! $_product->exists() ) {
				continue;
			}

			$products[] = self::get_product_data( $_product );

		}

		return $products;
	}

	/**
	 * The get wc product image is generated by appsbd
	 *
	 * @param any    $product Its string.
	 * @param string $size Its string.
	 *
	 * @return false|string
	 */
	public static function get_wc_product_image( $product, $size = 'woocommerce_thumbnail' ) {
		 $image = '';
		if ( $product->get_image_id() ) {
			$image = wp_get_attachment_image_url( $product->get_image_id(), $size );
		} elseif ( $product->get_parent_id() ) {
			$parent_product = wc_get_product( $product->get_parent_id() );
			if ( $parent_product ) {
				$image = wp_get_attachment_image_url( $parent_product->get_image_id(), $size );
			}
		} else {
			$image = wc_placeholder_img_src( $size );

		}
		return $image;
	}

	/**
	 * The get wc product ids is generated by appsbd
	 *
	 * @param array $filter Its array.
	 *
	 * @return mixed
	 */
	private static function get_wc_product_ids( $filter = array() ) {
		global $wpdb;
		$query = self::query_products( $filter );
		return $query->posts;
	}

	/**
	 * Helper method to get product post objects
	 *
	 * @param array $args request arguments for filtering query.
	 * @return array
	 * @since 2.1
	 */
	private static function query_args( $args ) {
				$query_args = array(
					'fields'      => 'ids',
					'post_type'   => 'product',
					'post_status' => POS_Settings::get_module_instance()->get_product_status(),
					'meta_query'  => array(),
				);

				if ( ! empty( $args['sku'] ) ) {
					if ( ! is_array( $query_args['meta_query'] ) ) {
						$query_args['meta_query'] = array();
					}

					$query_args['meta_query'][] = array(
						'key'     => '_sku',
						'value'   => $args['sku'],
						'compare' => '=',
					);

					$query_args['post_type'] = array( 'product', 'product_variation' );
				}

				$query_args = self::merge_query_args( $query_args, $args );
				return $query_args;
	}

	/**
	 * Add common request arguments to argument list before \WP_Query is run
	 *
	 * @param array $base_args required arguments for the query (e.g. `post_type`, etc).
	 * @param array $request_args arguments provided in the request.
	 * @return array
	 * @since 2.1
	 */
	public static function merge_query_args( $base_args, $request_args ) {
		$args = array();

		if ( ! empty( $request_args['created_at_min'] ) || ! empty( $request_args['created_at_max'] ) || ! empty( $request_args['updated_at_min'] ) || ! empty( $request_args['updated_at_max'] ) ) {

			$args['date_query'] = array();

			if ( ! empty( $request_args['created_at_min'] ) ) {
				$args['date_query'][] = array(
					'column'    => 'post_date_gmt',
					'after'     => self::parse_datetime( $request_args['created_at_min'] ),
					'inclusive' => true,
				);
			}

			if ( ! empty( $request_args['created_at_max'] ) ) {
				$args['date_query'][] = array(
					'column'    => 'post_date_gmt',
					'before'    => self::parse_datetime( $request_args['created_at_max'] ),
					'inclusive' => true,
				);
			}

			if ( ! empty( $request_args['updated_at_min'] ) ) {
				$args['date_query'][] = array(
					'column'    => 'post_modified_gmt',
					'after'     => self::parse_datetime( $request_args['updated_at_min'] ),
					'inclusive' => true,
				);
			}

			if ( ! empty( $request_args['updated_at_max'] ) ) {
				$args['date_query'][] = array(
					'column'    => 'post_modified_gmt',
					'before'    => self::parse_datetime( $request_args['updated_at_max'] ),
					'inclusive' => true,
				);
			}
		}

		if ( ! empty( $request_args['q'] ) ) {
			$args['s'] = $request_args['q'];
		}

		if ( ! empty( $request_args['limit'] ) ) {
			$args['posts_per_page'] = $request_args['limit'];
		}

		if ( ! empty( $request_args['offset'] ) ) {
			$args['offset'] = $request_args['offset'];
		}

		if ( ! empty( $request_args['order'] ) ) {
			$args['order'] = $request_args['order'];
		}

		if ( ! empty( $request_args['orderby'] ) ) {
			$args['orderby'] = $request_args['orderby'];

			if ( ! empty( $request_args['orderby_meta_key'] ) ) {
				$args['meta_key'] = $request_args['orderby_meta_key'];
			}
		}
		if ( ! empty( $request_args['api_src'] ) ) {
			$args['api_src'] = $request_args['api_src'];
		}
		if ( ! empty( $request_args['api_sort'] ) ) {
			$args['api_sort'] = $request_args['api_sort'];
		}

		if ( ! empty( $request_args['post_status'] ) ) {
			$args['post_status'] = $request_args['post_status'];
			unset( $request_args['post_status'] );
		}

		if ( ! empty( $request_args['in'] ) ) {
			$args['post__in'] = explode( ',', $request_args['in'] );
			unset( $request_args['in'] );
		}

		if ( ! empty( $request_args['in'] ) ) {
			$args['post__in'] = explode( ',', $request_args['in'] );
			unset( $request_args['in'] );
		}

				$args['paged'] = ( isset( $request_args['page'] ) ) ? absint( $request_args['page'] ) : 1;
		/**
		 * Its for api query args.
		 *
		 * @since 1.0
		 */
		$args = apply_filters( 'woocommerce_api_query_args', $args, $request_args );

		return array_merge( $base_args, $args );
	}

	/**
	 * The get variation data is generated by appsbd
	 *
	 * @param any $product Its string.
	 *
	 * @return array
	 */
	private static function get_variation_data( $product ) {
		$variations = array();

		foreach ( $product->get_children() as $child_id ) {
			$variation = wc_get_product( $child_id );

			if ( ! $variation || ! $variation->exists() ) {
				continue;
			}
			$variation_obj                = new Product_Variant();
			$variation_obj->id            = $variation->get_id();
			$variation_obj->name          = $variation->get_name();
			$variation_obj->slug          = $variation->get_slug();
			$variation_obj->sku           = $variation->get_sku();
			$variation_obj->product_id    = $product->get_id();
			$variation_obj->sale_price    = $variation->get_sale_price() ? $variation->get_sale_price() : 0;
			$variation_obj->regular_price = $variation->get_regular_price() ? $variation->get_regular_price() : 0;
			$variation_obj->price         = $variation->get_price() ? $variation->get_price() : 0;
			$variation_obj->outlet_id     = 0;
			/**
			 * Its for product feature update
			 *
			 * @since 1.0
			 */
			$outletinfo = apply_filters( 'vitepos/filter/current-outlet', null );
			if ( ! empty( $outletinfo->id ) ) {
				$variation_obj->outlet_id = absint( $outletinfo->id );
			}
			$variation_obj->price_html     = wc_price( $variation_obj->price );
			$variation_obj->manage_stock   = $variation->managing_stock();
			$variation_obj->stock_quantity = $variation->get_stock_quantity() ? $variation->get_stock_quantity() : 0;

			$variation_obj->in_stock            = $variation->is_in_stock();
			$variation_obj->low_stock_amount    = $variation->get_low_stock_amount();
			$variation_obj->taxable             = $variation->is_taxable() ? 'Y' : 'N';
			$variation_obj->tax_status          = $variation->get_tax_status();
			$variation_obj->tax_class           = $variation->get_tax_class();
			$variation_obj->height              = $variation->get_height();
			$variation_obj->weight              = $variation->get_weight();
			$variation_obj->length              = $variation->get_length();
			$variation_obj->width               = $variation->get_width();
			$variation_obj->tax_rate            = NumberUtil::round( ( doubleval( wc_get_price_including_tax( $variation ) ) - doubleval( $variation_obj->price ) ), wc_get_price_decimals() );
			$variation_obj->on_sale             = $variation->is_on_sale() ? 'Y' : 'N';
			$variation_obj->image               = self::get_wc_product_image( $variation, 'woocommerce_thumbnail' );
			$variation_obj->attributes          = self::get_attributes( $variation );
			$variation_obj->barcode             = (string) self::get_barcode_of_product( $variation );
			$variation_obj->purchase_cost       = $variation->meta_exists( '_vt_purchase_cost' ) ? $variation->get_meta( '_vt_purchase_cost' ) : '0.0';
			$variation_obj->is_parent_dimension = $variation->get_meta( 'is_parent_dimension' );
			if ( $variation_obj->is_parent_dimension ) {
				$variation_obj->is_parent_dimension = true;
			} else {
				$variation_obj->is_parent_dimension = false;
				$variation_obj->width               = $variation->get_width();
				$variation_obj->height              = $variation->get_height();
				$variation_obj->weight              = $variation->get_weight();
				$variation_obj->length              = $variation->get_length();
			}
			$variations[] = $variation_obj;
		}

		return $variations;
	}

	/**
	 * Get the attributes for a product or product variation
	 *
	 * @param \WC_Product|\WC_Product_Variation $product Its string.
	 * @return array
	 * @since 2.1
	 */
	private static function get_attributes( $product ) {
		$attributes = array();

		if ( $product->is_type( 'variation' ) ) {

			foreach ( $product->get_variation_attributes() as $attribute_name => $attribute ) {

					$attributes[] = array(
						'name'   => wc_attribute_label( str_replace( 'attribute_', '', $attribute_name ), $product ),
						'slug'   => str_replace( 'attribute_', '', wc_attribute_taxonomy_slug( $attribute_name ) ),
						'option' => $attribute,
					);
			}
		} else {

			foreach ( $product->get_attributes() as $attribute ) {
				$attributes[] = array(
					'id'        => $attribute['id'],
					'name'      => wc_attribute_label( $attribute['name'], $product ),
					'slug'      => wc_sanitize_taxonomy_name( $attribute['name'] ),
					'visible'   => (bool) $attribute['is_visible'],
					'variation' => (bool) $attribute['is_variation'],
					'options'   => self::get_attribute_options( $product->get_id(), $attribute ),
				);
			}
		}

		return $attributes;
	}

	/**
	 * Get attribute options.
	 *
	 * @param int   $product_id Its integer.
	 * @param array $attribute Its array.
	 * @return array
	 */
	protected static function get_attribute_options( $product_id, $attribute ) {
		$options = array();
		if ( isset( $attribute['is_taxonomy'] ) && $attribute['is_taxonomy'] ) {
			$found_options = wc_get_product_terms( $product_id, $attribute['name'] );
			foreach ( $found_options as $option ) {
				$attr       = new \stdClass();
				$attr->slug = $option->slug;
				$attr->name = $option->name;
				$options[]  = $attr;
			}
			return $options;
		} elseif ( isset( $attribute['value'] ) ) {
			$found_options = array_map( 'trim', explode( '|', $attribute['value'] ) );
			foreach ( $found_options as $option ) {
				$attr       = new \stdClass();
				$attr->slug = $option;
				$attr->name = $option;
				$options[]  = $attr;
			}
			return $options;
		}

		return array();
	}

	/**
	 * Parse an RFC3339 datetime into a MySQl datetime
	 *
	 * Invalid dates default to unix epoch
	 *
	 * @param string $datetime RFC3339 datetime.
	 * @return string MySQl datetime (YYYY-MM-DD HH:MM:SS).
	 * @since 2.1
	 */
	public static function parse_datetime( $datetime ) {
		if ( strpos( $datetime, '.' ) !== false ) {
			$datetime = preg_replace( '/\.\d+/', '', $datetime );
		}

				$datetime = preg_replace( '/[+-]\d+:+\d+$/', '+00:00', $datetime );

		try {

			$datetime = new DateTime( $datetime, new DateTimeZone( 'UTC' ) );

		} catch ( Exception $e ) {

			$datetime = new DateTime( '@0' );

		}

		return $datetime->format( 'Y-m-d H:i:s' );
	}
}
