<?php
/**
 * WC_Freterapido class.
 */
class WC_Freterapido extends WC_Shipping_Method {

	/**
	 * Initialize the Frete Rápido shipping method.
	 *
	 * @return void
	 */
	public function __construct($instance_id = 0 ) {
        $this->id           = 'freterapido';
        $this->instance_id 	= absint( $instance_id );
		$this->method_title = __( 'Frete Rápido', 'woo-shipping-gateway' );

        $this->supports              = array(
            'shipping-zones',
            'instance-settings'
        );

		$this->init();
	}

	/**
	 * Initializes the method.
	 *
	 * @return void
	 */
	public function init() {
		// Frete Rápido Web Service.
		$this->webservice = 'http://services.frenet.com.br/logistics/ShippingQuoteWS.asmx?wsdl';

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->enabled            = $this->get_option('enabled');
		$this->cnpj              = $this->get_option('cnpj');
    $this->results       = $this->get_option('results');
    $this->limit              = $this->get_option('limit');
    $this->additional_time    = $this->get_option('additional_time');
    $this->additional_price              = $this->get_option( 'additional_price' );
    $this->token              = $this->get_option('token');
		// $this->debug              = $this->get_option('debug');

		// Active logs.
		if ( 'yes' == $this->debug ) {
			if ( class_exists( 'WC_Logger' ) ) {
				$this->log = new WC_Logger();
			} else {
				$this->log = $this->woocommerce_method()->logger();
			}
		}

		// Actions.
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
	 * Backwards compatibility with version prior to 2.1.
	 *
	 * @return object Returns the main instance of WooCommerce class.
	 */
	protected function woocommerce_method() {
		if ( function_exists( 'WC' ) ) {
			return WC();
		} else {
			global $woocommerce;
			return $woocommerce;
		}
	}

	/**
	 * Admin options fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'Informações' => array(
				'title' => __(
					'<div style="background: #ffffff; border: none; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1)">
            <div style="padding:15px;text-align: center; vertical-align:bottom;">
            	<a href="http://www.freterapido.com" target="_blank">
            		<img src="https://freterapido.com/imgs/frete_rapido.png" style="margin: auto" />
            	</a>
            	<div style="padding-top: 20px;">
                Configure abaixo a sua conta com os dados da loja para obter as cotações de frete através do Frete Rápido.
                </br>
                O token e as configurações dos Correios estão disponíveis no seu <a href="https://freterapido.com/painel/" target="_blank">Painel administrativo</a>.
                </br>
                Em caso de dúvidas, reporte de bugs ou sugestão de melhorias, acesse a <a href="https://github.com/freterapido/freterapido_woocommerce" target="_blank">documentação deste módulo no Github</a>.
                </br>
            	</div>
          	</div>
            <div class="clear"></div>
          </div>', 'woo-shipping-gateway'),
				'type' => 'title'
			),
			'enabled' => array(
				'title'            => __( 'Enable/Disable', 'woo-shipping-gateway' ),
				'type'             => 'checkbox',
				'label'            => __( 'Habilitar plugin', 'woo-shipping-gateway' ),
				'default'          => 'yes'
			),
			'cnpj' => array(
				'title'            => __( 'CNPJ', 'woo-shipping-gateway' ),
				'type'             => 'text',
				'description'      => __( 'CNPJ da sua loja.', 'woo-shipping-gateway' ),
				'desc_tip'         => true
			),
      'results' => array(
          'title' => __('Resultados', 'woo-shipping-gateway'),
          'type' => 'select',
					'options' => array('0' => 'Sem filtro (todas as ofertas)', '1' => 'Somente oferta com menor preço', '2' => 'Retornar somente a oferta com menor prazo de entrega'),
          'label' => __('Ativado', 'woo-shipping-gateway'),
          'description' => __('Como você gostaria de receber os resultados?', 'woo-shipping-gateway'),
          'desc_tip' => true,
          'default' => 'yes'
      ),
      'limit' => array(
          'title' => __('Limite', 'woo-shipping-gateway'),
          'type' => 'select',
					'options' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
						'13' => '13',
						'14' => '14',
						'15' => '15',
						'16' => '16',
						'17' => '17',
						'18' => '18',
						'19' => '19',
						'19' => '19',
						'20' => '20',
					),
          'description' => __('Escolha um limite de resultados', 'woo-shipping-gateway'),
          'desc_tip' => true,
          'default' => 'yes'
      ),
      'additional_time' => array(
          'title'            => __( 'Prazo adicional de envio/postagem (dias)', 'woo-shipping-gateway' ),
          'type'             => 'text',
          'description'      => __( 'Será acrecido no prazo do frete.', 'woo-shipping-gateway' ),
          'desc_tip'         => true,
          'default'          => '0',
          'placeholder'      => '0'
      ),
			'additional_price' => array(
				'title'            => __( 'Custo adicional de envio/postagem (R$)', 'woo-shipping-gateway' ),
				'type'             => 'text',
				'description'      => __( 'Será acrecido no valor do frete.', 'woo-shipping-gateway' ),
				'desc_tip'         => true,
				'default'          => '2'
			),
			'token' => array(
				'title'            => __( 'Token', 'woo-shipping-gateway' ),
				'type'             => 'text',
				'description'      => __( 'Token de integração com o Frete Rápido.', 'woo-shipping-gateway' ),
				'desc_tip'         => true
			)
			// 'testing' => array(
			// 	'title'            => __( 'Testing', 'woo-shipping-gateway' ),
			// 	'type'             => 'title'
			// ),
			// 'debug' => array(
			// 	'title'            => __( 'Debug Log', 'woo-shipping-gateway' ),
			// 	'type'             => 'checkbox',
			// 	'label'            => __( 'Enable logging', 'woo-shipping-gateway' ),
			// 	'default'          => 'no',
			// 	'description'      => sprintf( __( 'Log Frete Rápido events, such as WebServices requests, inside %s.', 'woo-shipping-gateway' ), '<code>woocommerce/logs/frenet-' . sanitize_file_name( wp_hash( 'frenet' ) ) . '.txt</code>' )
			// )
		);

        $this->form_fields = $this->instance_form_fields;
	}

	/**
	 * Frete Rápido options page.
	 *
	 * @return void
	 */
	public function admin_options() {
		echo '<h3>' . $this->method_title . '</h3>';
		echo '<p>' . __( 'Frete Rápido is a brazilian delivery plataform.', 'woo-shipping-gateway' ) . '</p>';
		echo '<table class="form-table">';
		$this->generate_settings_html();
		echo '</table>';
	}

	/**
	 * Checks if the method is available.
	 *
	 * @param array $package Order package.
	 *
	 * @return bool
	 */
	public function is_available( $package ) {
		$is_available = true;

		if ( 'no' == $this->enabled ) {
			$is_available = false;
		}

		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
	}

	/**
	 * Replace comma by dot.
	 *
	 * @param  mixed $value Value to fix.
	 *
	 * @return mixed
	 */
	private function fix_format( $value ) {
		$value = str_replace( ',', '.', $value );

		return $value;
	}

	/**
	 * Fix number format for SimpleXML.
	 *
	 * @param  float $value  Value with dot.
	 *
	 * @return string        Value with comma.
	 */
	private function fix_simplexml_format( $value ) {
		$value = str_replace( '.', ',', $value );

		return $value;
	}

	/**
	 * Fix Zip Code format.
	 *
	 * @param mixed $zip Zip Code.
	 *
	 * @return int
	 */
	protected function fix_zip_code( $zip ) {
		$fixed = preg_replace( '([^0-9])', '', $zip );

		return $fixed;
	}

	/**
	 * Get fee.
	 *
	 * @param  mixed $fee
	 * @param  mixed $total
	 *
	 * @return float
	 */
	public function get_fee( $fee, $total ) {
		if ( strstr( $fee, '%' ) ) {
			$fee = ( $total / 100 ) * str_replace( '%', '', $fee );
		}

		return $fee;
	}

	/**
	 * Calculates the shipping rate.
	 *
	 * @param array $package Order package.
	 *
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		$rates  = array();
        $errors = array();
				 var_dump($package);
        if (isset($this->token) && $this->token != '')
            $shipping_values = $this->freterapido_calculate_json( $package );
        else
            $shipping_values = $this->freterapido_calculate( $package );

        if ( ! empty( $shipping_values ) ) {
            foreach ( $shipping_values as $code => $shipping ) {

                if(!isset($shipping->ShippingPrice))
                    continue;

                // Set the shipping rates.
                $label='';
                $date=0;
                if(isset($shipping->ServiceDescription) )
                    $label=$shipping->ServiceDescription;

                if (isset($shipping->DeliveryTime))
                    $date=$shipping->DeliveryTime;

                $label = ( 'yes' == $this->display_date ) ? $this->estimating_delivery( $label, $date, $this->additional_time ) : $label;
                $cost  = floatval(str_replace(",", ".", (string) $shipping->ShippingPrice));

                array_push(
                    $rates,
                    array(
                        'id'    => 'FRETERAPIDO_' . $shipping->ServiceCode,
                        'label' => $label,
                        'cost'  => $cost,
                    )
                );
            }
            // Add rates.
            foreach ( $rates as $rate ) {
                $this->add_rate( $rate );
            }
        }
	}

    /**
     * Estimating Delivery.
     *
     * @param string $label
     * @param string $date
     * @param int    $additional_time
     *
     * @return string
     */
    protected function estimating_delivery( $label, $date, $additional_time = 0 ) {
        $name = $label;
        $additional_time = intval( $additional_time );

        if ( $additional_time > 0 ) {
            $date += intval( $additional_time );
        }

        if ( $date > 0 ) {
            $name .= ' (' . sprintf( _n( 'Delivery in %d working day', 'Delivery in %d working days', $date, 'woo-shipping-gateway' ),  $date ) . ')';
        }

        return $name;
    }

    protected function freterapido_calculate_json( $package ){
        $values = array();
        try
        {

            $RecipientCEP = $package['destination']['postcode'];
            $RecipientCountry = $package['destination']['country'];

            // Checks if services and zipcode is empty.
            if (empty( $RecipientCEP ) && $RecipientCountry=='BR')
            {
                if ( 'yes' == $this->debug ) {
                    $this->log->add( $this->id,"ERRO: CEP destino não informado");
                }
                return $values;
            }
            if(empty( $this->zip_origin ))
            {
                if ( 'yes' == $this->debug ) {
                    $this->log->add( $this->id,"ERRO: CEP origem não configurado");
                }
                return $values;
            }

            // product array
            $shippingItemArray = array();
            $count = 0;

            // Shipping per item.
            foreach ( $package['contents'] as $item_id => $values ) {
                $product = $values['data'];
                $qty = $values['quantity'];

                if ( 'yes' == $this->debug ) {
                    $this->log->add( $this->id, 'Product: ' . print_r($product, true));
                }

                $shippingItem = new stdClass();

                if ( $qty > 0 && $product->needs_shipping() ) {

                    if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
                        $_height = wc_get_dimension( $this->fix_format( $product->height ), 'cm' );
                        $_width  = wc_get_dimension( $this->fix_format( $product->width ), 'cm' );
                        $_length = wc_get_dimension( $this->fix_format( $product->length ), 'cm' );
                        $_weight = wc_get_weight( $this->fix_format( $product->weight ), 'kg' );
                    } else {
                        $_height = woocommerce_get_dimension( $this->fix_format( $product->height ), 'cm' );
                        $_width  = woocommerce_get_dimension( $this->fix_format( $product->width ), 'cm' );
                        $_length = woocommerce_get_dimension( $this->fix_format( $product->length ), 'cm' );
                        $_weight = woocommerce_get_weight( $this->fix_format( $product->weight ), 'kg' );
                    }

                    if(empty($_height))
                        $_height= $this->minimum_height;

                    if(empty($_width))
                        $_width= $this->minimum_width;

                    if(empty($_length))
                        $_length = $this->minimum_length;

                    if(empty($_weight))
                        $_weight = 1;


                    $shippingItem->Weight = $_weight * $qty;
                    $shippingItem->Length = $_length;
                    $shippingItem->Height = $_height;
                    $shippingItem->Width = $_width;
                    $shippingItem->Diameter = 0;
                    $shippingItem->SKU = $product->get_sku();

                    // wp_get_post_terms( your_id, 'product_cat' );
                    $shippingItem->Category = '';
                    $shippingItem->isFragile=false;

                    if ( 'yes' == $this->debug ) {
                        $this->log->add( $this->id, 'shippingItem: ' . print_r($shippingItem, true));
                    }

                    $shippingItemArray[$count] = $shippingItem;

                    $count++;
                }
            }

            if ( 'yes' == $this->debug ) {

                $this->log->add( $this->id, 'CEP ' . $package['destination']['postcode'] );
            }

            $service_param = array (
                    'Token' => $this->token,
                    'SellerCEP' => $this->zip_origin,
                    'RecipientCEP' => $RecipientCEP,
                    'RecipientDocument' => '',
                    'ShipmentInvoiceValue' => WC()->cart->cart_contents_total,
                    'ShippingItemArray' => $shippingItemArray,
                    'RecipientCountry' => $RecipientCountry
            );

            if ( 'yes' == $this->debug ) {
                $this->log->add( $this->id, 'Requesting the Frete Rápido WebServices...');
                $this->log->add( $this->id, print_r($service_param, true));
            }

            // Gets the WebServices response.

            $service_url = 'http://api.frenet.com.br/v1/Shipping/GetShippingQuote?data=' . json_encode($service_param);

            if ( 'yes' == $this->debug ) {
                $this->log->add( $this->id, 'URL: ' . $service_url );
            }

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $service_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $curl_response = curl_exec($curl);
            curl_close($curl);

            if ( 'yes' == $this->debug ) {
                $this->log->add( $this->id, 'Curl response: ' . $curl_response );
            }


            if ( is_wp_error( $curl_response ) ) {
                if ( 'yes' == $this->debug ) {
                    $this->log->add( $this->id, 'WP_Error: ' . $curl_response->get_error_message() );
                }
            } else
            {
                $response = json_decode($curl_response);

                if ( isset( $response->ShippingSevicesArray ) ) {

                    $servicosArray = (array)$response->ShippingSevicesArray;

                    if(!empty($servicosArray))
                    {
                        foreach($servicosArray as $servicos){

                            if ( 'yes' == $this->debug ) {
                                $this->log->add( $this->id, 'Percorrendo os serviços retornados');
                            }

                            if (!isset($servicos->ServiceCode) || $servicos->ServiceCode . '' == '' || !isset($servicos->ShippingPrice)) {
                                if ( 'yes' == $this->debug ) {
                                    $this->log->add( $this->id, '*continue*');
                                }
                                continue;
                            }

                            $code = (string) $servicos->ServiceCode;

                            if ( 'yes' == $this->debug ) {
                                $this->log->add( $this->id, 'WebServices response [' . $servicos->ServiceDescription . ']: ' . print_r( $servicos, true ) );
                            }

                            $values[ $code ] = $servicos;
                        }
                    }

                }
            }
        }
        catch (Exception $e)
        {
            if ( 'yes' == $this->debug ) {
                $this->log->add( $this->id, var_dump($e->getMessage()));
            }
        }

        return $values;

    }

    protected function frenet_calculate( $package ){
        $values = array();

        $RecipientCEP = $package['destination']['postcode'];
        $RecipientCountry = $package['destination']['country'];

        // Checks if services and zipcode is empty.
        if (empty( $RecipientCEP ) && $RecipientCountry=='BR')
        {
            if ( 'yes' == $this->debug ) {
                $this->log->add( $this->id,"ERRO: CEP destino não informado");
            }
            return $values;
        }
        if(empty( $this->zip_origin ))
        {
            if ( 'yes' == $this->debug ) {
                $this->log->add( $this->id,"ERRO: CEP origem não configurado");
            }
            return $values;
        }

        // product array
        $shippingItemArray = array();
        $count = 0;

        // Shipping per item.
        foreach ( $package['contents'] as $item_id => $values ) {
            $product = $values['data'];
            $qty = $values['quantity'];

            if ( 'yes' == $this->debug ) {
                $this->log->add( $this->id, 'Product: ' . print_r($product, true));
            }

            $shippingItem = new stdClass();

            if ( $qty > 0 && $product->needs_shipping() ) {

                if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
                    $_height = wc_get_dimension( $this->fix_format( $product->height ), 'cm' );
                    $_width  = wc_get_dimension( $this->fix_format( $product->width ), 'cm' );
                    $_length = wc_get_dimension( $this->fix_format( $product->length ), 'cm' );
                    $_weight = wc_get_weight( $this->fix_format( $product->weight ), 'kg' );
                } else {
                    $_height = woocommerce_get_dimension( $this->fix_format( $product->height ), 'cm' );
                    $_width  = woocommerce_get_dimension( $this->fix_format( $product->width ), 'cm' );
                    $_length = woocommerce_get_dimension( $this->fix_format( $product->length ), 'cm' );
                    $_weight = woocommerce_get_weight( $this->fix_format( $product->weight ), 'kg' );
                }

                if(empty($_height))
                    $_height= $this->minimum_height;

                if(empty($_width))
                    $_width= $this->minimum_width;

                if(empty($_length))
                    $_length = $this->minimum_length;

                if(empty($_weight))
                    $_weight = 1;


                $shippingItem->Weight = $_weight * $qty;
                $shippingItem->Length = $_length;
                $shippingItem->Height = $_height;
                $shippingItem->Width = $_width;
                $shippingItem->Diameter = 0;
                $shippingItem->SKU = $product->get_sku();

                // wp_get_post_terms( your_id, 'product_cat' );
                $shippingItem->Category = '';
                $shippingItem->isFragile=false;

                if ( 'yes' == $this->debug ) {
                    $this->log->add( $this->id, 'shippingItem: ' . print_r($shippingItem, true));
                }

                $shippingItemArray[$count] = $shippingItem;

                $count++;
            }
        }

        if ( 'yes' == $this->debug ) {

            $this->log->add( $this->id, 'CEP ' . $package['destination']['postcode'] );
        }

        $service_param = array (
            'quoteRequest' => array(
                'Username' => $this->login,
                'Password' => $this->password,
                'SellerCEP' => $this->zip_origin,
                'RecipientCEP' => $RecipientCEP,
                'RecipientDocument' => '',
                'ShipmentInvoiceValue' => WC()->cart->cart_contents_total,
                'ShippingItemArray' => $shippingItemArray,
                'RecipientCountry' => $RecipientCountry
            )
        );

        if ( 'yes' == $this->debug ) {
            $this->log->add( $this->id, 'Requesting the Frete Rápido WebServices...');
            $this->log->add( $this->id, print_r($service_param, true));
        }

        // Gets the WebServices response.
        $client = new SoapClient($this->webservice, array("soap_version" => SOAP_1_1,"trace" => 1));
        $response = $client->__soapCall("GetShippingQuote", array($service_param));

        if ( 'yes' == $this->debug ) {
            $this->log->add( $this->id, $client->__getLastRequest());
            $this->log->add( $this->id, $client->__getLastResponse());
        }

        if ( is_wp_error( $response ) ) {
            if ( 'yes' == $this->debug ) {
                $this->log->add( $this->id, 'WP_Error: ' . $response->get_error_message() );
            }
        } else
        {
            if ( isset( $response->GetShippingQuoteResult ) ) {
                if(count($response->GetShippingQuoteResult->ShippingSevicesArray->ShippingSevices)==1)
                    $servicosArray[0] = $response->GetShippingQuoteResult->ShippingSevicesArray->ShippingSevices;
                else
                    $servicosArray = $response->GetShippingQuoteResult->ShippingSevicesArray->ShippingSevices;

                if(!empty($servicosArray))
                {
                    foreach($servicosArray as $servicos){

                        if ( 'yes' == $this->debug ) {
                            $this->log->add( $this->id, 'Percorrendo os serviços retornados');
                        }

                        if (!isset($servicos->ServiceCode) || $servicos->ServiceCode . '' == '' || !isset($servicos->ShippingPrice)) {
                            continue;
                        }

                        $code = (string) $servicos->ServiceCode;

                        if ( 'yes' == $this->debug ) {
                            $this->log->add( $this->id, 'WebServices response [' . $servicos->ServiceDescription . ']: ' . print_r( $servicos, true ) );
                        }

                        $values[ $code ] = $servicos;
                    }
                }

            }
        }

        return $values;

    }

    /**
     * Safe load XML.
     *
     * @param  string $source
     * @param  int    $options
     *
     * @return SimpleXMLElement|bool
     */
    protected function safe_load_xml( $source, $options = 0 ) {
        $old = null;

        if ( function_exists( 'libxml_disable_entity_loader' ) ) {
            $old = libxml_disable_entity_loader( true );
        }

        $dom    = new DOMDocument();

        $return = $dom->loadXML( $source, $options );

        if ( ! is_null( $old ) ) {
            libxml_disable_entity_loader( $old );
        }

        if ( ! $return ) {
            return false;
        }

        if ( isset( $dom->doctype ) ) {
            throw new Exception( 'Unsafe DOCTYPE Detected while XML parsing' );

            return false;
        }

        return simplexml_import_dom( $dom );
    }


}
