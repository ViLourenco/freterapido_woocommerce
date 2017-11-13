<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Freterapido_Hire_Shipping {
	private $token;
	private $sender;
	private $receiver;
	private $dispatcher;
	private $order_number;

	public function __construct( $token ) {
		$this->token = $token;
	}

	public function add_sender( array $sender ) {
		$this->sender = $sender;

		return $this;
	}

	public function add_receiver( array $receiver ) {
		$this->receiver = $receiver;

		return $this;
	}

	public function add_dispatcher( array $dispatcher ) {
		$this->dispatcher = $dispatcher;

		return $this;
	}

	public function add_order( $number ) {
		$this->order_number = $number;

		return $this;
	}

	private function format_request() {
		$request = array( [ 'numero_pedido' => $this->order_number ] );

		if ( $this->dispatcher ) {
			$request['expedidor'] = $this->dispatcher;
		}

		return array_merge(
			$request,
			array(
				'remetente'    => $this->sender,
				'destinatario' => $this->receiver,
			)
		);
	}

	public function hire_quote( $simulation_token, $offer_id ) {
		$api_url = sprintf( FR_API_URL . 'embarcador/v1/quote/ecommerce/%s/offer/%s?token=%s', $simulation_token, $offer_id, $this->token );

		$response = WC_Freterapido_Http::do_request( $api_url, $this->format_request() );

		if ( 401 === (int) $response['info']['http_code'] ) {
			throw new InvalidArgumentException();
		}

		$result = $response['result'];

		if ( ! $result || ! isset( $result['id_frete'] ) ) {
			throw new UnexpectedValueException();
		}

		return $result;
	}
}
