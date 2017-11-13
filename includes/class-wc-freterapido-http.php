<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Freterapido_Http {
	static function do_request( $url, $params = array(), $method = 'POST' ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		$data_string = json_encode( $params );

		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string );
		curl_setopt(
			$ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen( $data_string ),
			)
		);

		$result = curl_exec( $ch );
		$info   = curl_getinfo( $ch );

		curl_close( $ch );

		return [
			'info'   => $info,
			'result' => json_decode( $result, true ),
		];
	}
}
