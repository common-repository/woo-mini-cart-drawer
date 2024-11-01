<?php
/**
 * Pos Warehouse Database Model
 *
 * @package VitePos_Lite\Models\Database
 */

namespace VitePos_Lite\Models\Database;

use VitePos_Lite\Core\ViteposModelLite;

/**
 * Class Mapbd_Pos_Cash_Drawer
 *
 * @package VitePos_Lite\Models\Database
 */
class Mapbd_Pos_Cash_Drawer extends ViteposModelLite {
	/**
	 * Its property id
	 *
	 * @var int
	 */
	public $id;
	/**
	 * Its property outlet_id
	 *
	 * @var int
	 */
	public $outlet_id;
	/**
	 * Its property opening_balance
	 *
	 * @var float
	 */
	public $opening_balance;
	/**
	 * Its property opened_by
	 *
	 * @var int
	 */
	public $opened_by;

	/**
	 * Its property closed_by
	 *
	 * @var int
	 */
	public $closed_by;
	/**
	 * Its property opening_balance
	 *
	 * @var float
	 */
	public $closing_balance;
	/**
	 * Its property counter_id
	 *
	 * @var int
	 */
	public $counter_id;
	/**
	 * Its property opening_time
	 *
	 * @var String
	 */
	public $opening_time;
	/**
	 * Its property closing_time
	 *
	 * @var String
	 */
	public $closing_time;
	/**
	 * Its property status
	 *
	 * @var String
	 */
	public $status;


	/**
	 * Mapbd_Pos_Cash_Drawer constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->set_validation();
		$this->table_name     = 'apbd_pos_cash_drawer';
		$this->primary_key    = 'id';
		$this->unique_key     = array();
		$this->multi_key      = array();
		$this->auto_inc_field = array( 'id' );
		$this->app_base_name  = 'apbd-vite-pos';
	}


	/**
	 * The set validation is generated by appsbd
	 */
	public function set_validation() {
		$this->validations = array(
			'id'              => array(
				'Text' => 'Id',
				'Rule' => 'max_length[10]|integer',
			),
			'outlet_id'       => array(
				'Text' => 'Outlet Id',
				'Rule' => 'max_length[10]|integer',
			),
			'opening_balance' => array(
				'Text' => 'Opening Balance',
				'Rule' => 'max_length[11]|numeric',
			),
			'closing_balance' => array(
				'Text' => 'Closing Balance',
				'Rule' => 'max_length[11]|numeric',
			),
			'counter_id'      => array(
				'Text' => 'Counter Id',
				'Rule' => 'max_length[10]|integer',
			),
			'opening_time'    => array(
				'Text' => 'Opening Time',
				'Rule' => 'max_length[20]',
			),
			'closing_time'    => array(
				'Text' => 'Closing Time',
				'Rule' => 'max_length[20]',
			),
			'status'          => array(
				'Text' => 'Status',
				'Rule' => 'max_length[1]',
			),

		);
	}

	/**
	 * The get property raw options is generated by appsbd
	 *
	 * @param mixed $property Its property.
	 * @param false $is_with_select False if no select.
	 *
	 * @return array|string[]
	 */
	public function get_property_raw_options( $property, $is_with_select = false ) {
		$return_obj = array();
		switch ( $property ) {
			case 'status':
				$return_obj = array(
					'O' => 'Open',
					'C' => 'Close',
				);
				break;
			default:
		}
		if ( $is_with_select ) {
			return array_merge( array( '' => 'Select' ), $return_obj );
		}
		return $return_obj;
	}

	/**
	 * The set close drawer is generated by appsbd
	 */
	public function set_close_drawer() {
		$this_obj = new self();
		$this_obj->status( 'C' );
		$this_obj->closing_time( gmdate( 'Y-m-d H:i:s' ) );
		$this_obj->closed_by( get_current_user_id() );
		$this_obj->set_where_update( 'id', $this->id );
		if ( $this_obj->update() ) {
						Mapbd_Pos_Cash_Drawer_Log::AddLog( $this->id, 'Drawer closed', $this->closing_balance, 0, 'D', '', '' );
			return true;
		}
		return false;
	}

	/**
	 * The get by counter is generated by appsbd
	 *
	 * @param mixed $outlet_id Its outlet_id param.
	 * @param mixed $counter_id Its counter_id param.
	 * @param mixed $user_id Its user id param.
	 *
	 * @return Mapbd_Pos_Cash_Drawer|null
	 */
	public static function get_by_counter( $outlet_id, $counter_id, $user_id ) {
		$this_obj = new self();
		$this_obj->outlet_id( $outlet_id );
		$this_obj->counter_id( $counter_id );
		$this_obj->opened_by( $user_id );
		$this_obj->status( 'O' );
		if ( $this_obj->select() ) {
			return $this_obj;
		}
		return null;
	}

	/**
	 * The get cash drawer list is generated by appsbd
	 *
	 * @param mixed $user_id Its user id param.
	 *
	 * @return array
	 */
	public static function get_cash_drawer_list( $user_id ) {
		$drawers = self::fetch_all();
		return $drawers;
	}

	/**
	 * The create by counter is generated by appsbd
	 *
	 * @param mixed $opening_balance Its opening_balance param.
	 * @param mixed $outlet_id Its outlet_id param.
	 * @param mixed $counter_id Its counter_id param.
	 * @param mixed $user_id Its user_id param.
	 *
	 * @return |null
	 */
	public static function create_by_counter( $opening_balance, $outlet_id, $counter_id, $user_id ) {
		$close_existing = self::get_by_counter( $outlet_id, $counter_id, $user_id );
		if ( $close_existing ) {
			$close_existing->set_close_drawer();
		}
		$this_obj = new self();
		$this_obj->outlet_id( $outlet_id );
		$this_obj->counter_id( $counter_id );
		$this_obj->opened_by( $user_id );
		$this_obj->opening_time( gmdate( 'Y-m-d H:i:s' ) );
		$this_obj->opening_balance( $opening_balance );
		$this_obj->closing_balance( $opening_balance );
		$this_obj->closing_time( $this_obj->opening_time );
		$this_obj->status( 'O' );
		if ( $this_obj->save() ) {
			Mapbd_Pos_Cash_Drawer_Log::AddLog( $this_obj->id, 'Drawer Opened', 0, $opening_balance, 'C', '', '' );
			return $this_obj;
		}
		return null;
	}

	/**
	 * The add order is generated by appsbd
	 *
	 * @param mixed $user_id Its user_id param.
	 * @param mixed $amount Its amount param.
	 * @param mixed $order_id Its order_id param.
	 * @param mixed $outlet_id Its outlet_id param.
	 * @param mixed $counter_id Its counter_id param.
	 *
	 * @return bool
	 */
	public static function add_order( $user_id, $amount, $order_id, $outlet_id, $counter_id ) {
		$cashdrawer = self::get_by_counter( $outlet_id, $counter_id, $user_id );
		$amount     = doubleval( $amount );
		if ( $cashdrawer ) {
			$this_obj = new self();
			$this_obj->closing_balance( "closing_balance + $amount", true );
			$this_obj->closing_time( gmdate( 'Y-m-d H:i:s' ) );
			$this_obj->set_where_update( 'id', $cashdrawer->id );
			if ( $this_obj->update() ) {
				$after_update = self::get_by_counter( $outlet_id, $counter_id, $user_id );
				if ( ! empty( $after_update ) ) {
					$closing_balance = $after_update->closing_balance;
				} else {
					$closing_balance = $cashdrawer->closing_balance + $amount;
				}
								Mapbd_Pos_Cash_Drawer_Log::AddLog(
									$cashdrawer->id,
									'Order Processed',
									$cashdrawer->closing_balance,
									$amount,
									'D',
									'O',
									$order_id
								);

				return true;
			}
		}
		return false;
	}

	/**
	 * The add order is generated by appsbd
	 *
	 * @param mixed $user_id Its user_id param.
	 * @param mixed $amount Its amount param.
	 * @param mixed $order_id Its order_id param.
	 * @param mixed $outlet_id Its outlet_id param.
	 * @param mixed $counter_id Its counter_id param.
	 *
	 * @return bool
	 */
	public static function add_change_log( $user_id, $amount, $order_id, $outlet_id, $counter_id ) {
		$cashdrawer = self::get_by_counter( $outlet_id, $counter_id, $user_id );
		$amount     = doubleval( $amount );
		if ( $cashdrawer ) {
			$this_obj = new self();
			$this_obj->closing_balance( "closing_balance - $amount", true );
			$this_obj->closing_time( gmdate( 'Y-m-d H:i:s' ) );
			$this_obj->set_where_update( 'id', $cashdrawer->id );
			if ( $this_obj->update() ) {
				$after_update = self::get_by_counter( $outlet_id, $counter_id, $user_id );
				if ( ! empty( $after_update ) ) {
					$closing_balance = $after_update->closing_balance;
				} else {
					$closing_balance = $cashdrawer->closing_balance - $amount;
				}
								Mapbd_Pos_Cash_Drawer_Log::AddLog(
									$cashdrawer->id,
									'Order Change amount',
									$cashdrawer->closing_balance,
									$amount,
									'C',
									'O',
									$order_id
								);

				return true;
			}
		}
		return false;
	}
	/**
	 * The create db table is generated by appsbd
	 */
	public static function create_db_table() {
		$this_obj = new static();
		$table    = $this_obj->db->prefix . $this_obj->table_name;
		if ( $this_obj->db->get_var( "show tables like '{$table}'" ) != $table ) {
			$sql = "CREATE TABLE `{$table}` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `outlet_id` int(10) unsigned NOT NULL DEFAULT 0,
					  `counter_id` int(10) unsigned NOT NULL DEFAULT 0,
					  `opened_by` int(11) unsigned DEFAULT 0,
  					  `closed_by` int(11) unsigned DEFAULT 0,
					  `opening_balance` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
					  `closing_balance` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
					  `opening_time` timestamp NOT NULL DEFAULT current_timestamp(),
					  `closing_time` timestamp NOT NULL DEFAULT current_timestamp(),
					  `status` char(1) NOT NULL DEFAULT 'O' COMMENT 'radio(O=Open,C=Close)',
					  PRIMARY KEY (`id`) USING BTREE
					) ";
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}
	}
}
