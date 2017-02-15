<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Freterapido_Hire_Shipping {
    const API_URL = 'https://freterapido.com/api/external/embarcador/v1/quote/ecommerce/%s/offer/%s?token=%s';

    private $token;
    private $sender;
    private $receiver;
    private $dispatcher;

    public function __construct($token) {
        $this->token = $token;
    }

    public function add_sender(array $sender) {
        $this->sender = $sender;

        return $this;
    }

    public function add_receiver(array $receiver) {
        $this->receiver = $receiver;

        return $this;
    }

    public function add_dispatcher(array $dispatcher) {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    private function format_request() {
        $request = array();

        if ($this->dispatcher) {
            $request['expedidor'] = $this->dispatcher;
        }

        return array_merge(
            $request,
            array(
                'remetente' => $this->sender,
                'destinatario' => $this->receiver,
            )
        );
    }

    public function hire_quote($simulation_token, $offer_id) {
        $api_url = sprintf(self::API_URL, $simulation_token, $offer_id, $this->token);

        $response = WC_Freterapido_Http::do_request($api_url, $this->format_request());

        if ((int)$response['info']['http_code'] === 401) {
            throw new InvalidArgumentException();
        }

        $result = $response['result'];

        if (!$result || !isset($result['id_frete'])) {
            throw new UnexpectedValueException();
        }

        return $result;
    }
}
