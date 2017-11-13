<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Freterapido_Helpers {
	static function fix_zip_code( $zip ) {
		$fixed = preg_replace( '([^0-9])', '', $zip );

		return $fixed;
	}

	/**
	 * example: array_order_by($data, 'column_name', SORT_DESC, 'other_column_name', SORT_ASC);
	 * @return mixed
	 */
	static function array_order_by() {
		$args = func_get_args();
		$data = array_shift( $args );

		foreach ( $args as $n => $field ) {
			if ( is_string( $field ) ) {
				$tmp = array();
				foreach ( $data as $key => $row ) {
					$tmp[ $key ] = $row[ $field ];
				}
				$args[ $n ] = $tmp;
			}
		}

		$args[] = &$data;

		call_user_func_array( 'array_multisort', $args );
		return array_pop( $args );
	}
}
