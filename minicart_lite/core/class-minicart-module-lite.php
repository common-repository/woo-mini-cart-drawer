<?php
/**
 * Vitepos Model
 *
 * @package VitePos\Core
 */

namespace Minicart_Lite\Core;

use Appsbd_Lite\V2\Core\BaseModule;

/**
 * Class ViteposModel
 *
 * @package VitePos\Core
 */
abstract class Minicart_Module_Lite extends BaseModule {

	/**
	 * The on init is generated by appsbd
	 */
	public function on_init() {
		$this->add_admin_ajax_action( 'option', array( $this, 'ajax_request_callback' ) );
		$this->add_admin_ajax_action( 'get-option', array( $this, 'get_admin_options' ) );
		$this->add_admin_ajax_action( 'data', array( $this, 'data' ) );
		$this->add_admin_ajax_action( 'confirm', array( $this, 'confirm' ) );
	}
	/**
	 * The check user action access is generated by appsbd
	 *
	 * @param mixed $action_name Its action_name param.
	 *
	 * @return bool
	 */
	public function check_user_action_access( $action_name ) {
		return $this->check_user_access();
	}
	/**
	 * The check ajax referer is generated by appsbd
	 *
	 * @param bool $is_return Its checking security.
	 *
	 * @return bool
	 */
	public function app_check_ajax_referer( $is_return = false ) {

		if ( ! check_ajax_referer( 'appsbd', '_wpnonce', false ) ) {
			if ( $is_return ) {
				return false;
			}
			$main_response = new Ajax_Confirm_Response();
			$this->add_error( 'Nonce error' );
			$main_response->display_with_response( false, null, 403 );
		}

		return true;
	}

	/**
	 * The AddAjaxAction is generated by appsbd
	 *
	 * @param any      $action_name Its action_name param.
	 * @param callable $function_to_add Its function_to_add param.
	 */
	public function add_admin_ajax_action( $action_name, $function_to_add ) {
		if ( ! $this->check_user_action_access( $action_name ) ) {
			$action_name = $this->get_action_name( $action_name );
			add_action(
				'wp_ajax_' . $action_name,
				function () {
					$main_response = new Ajax_Confirm_Response();
					$this->add_error( 'User privilege error' );
					$main_response->display_with_response( false, null, 403 );
				}
			);
			return;
		}
		$action_name = $this->get_action_name( $action_name );
		if ( $this->app_check_ajax_referer( true ) ) {
			add_action( 'wp_ajax_' . $action_name, $function_to_add );
		} else {
			add_action(
				'wp_ajax_' . $action_name,
				function () {
					$main_response = new Ajax_Confirm_Response();
					$this->add_error( 'Nonce error' );
					$main_response->display_with_response( false, null, 403 );
				}
			);
		}
	}
}
