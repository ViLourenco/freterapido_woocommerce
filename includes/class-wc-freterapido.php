<?php

class Shipping {
    const API_URL = 'http://api-externa.freterapido.app/embarcador/v1/quote-simulator';

    private $config;
    private $sender;
    private $receiver;
    private $dispatcher;
    private $volumes;

    public function __construct(array $config) {
        $this->config = array_merge([
            'tipo_cobranca' => 1,
            'tipo_frete' => 1,
            'ecommerce' => true,
        ], $config);
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

    public function add_volumes(array $volumes) {
        $this->volumes = $volumes;

        return $this;
    }

    /**
     * @param int $filter
     * @return $this
     */
    public function set_filter($filter) {
        if ($filter) {
            $this->config['filtro'] = $filter;
        }

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function set_limit($limit) {
        if ($limit) {
            $this->config['limit'] = $limit;
        }

        return $this;
    }

    private function format_request() {
        return array_merge(
            array(
                'remetente' => $this->sender,
                'destinatario' => $this->receiver,
                'volumes' => $this->volumes,

                'tipo_cobranca' => 1,
                'tipo_frete' => 1,
                'ecommerce' => true,
            ),
            $this->config
        );
    }

    public function get_quote() {
        $response = $this->do_request(self::API_URL, $this->format_request());

        if ((int)$response['info']['http_code'] === 401) {
            throw new InvalidArgumentException();
        }

        $result = $response['result'];

        if (!$result || !isset($result['transportadoras']) || count($result['transportadoras']) === 0) {
            throw new UnexpectedValueException();
        }

        return $result;
    }

    private function do_request($url, $params = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data_string = json_encode($params);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);

        return ['info' => $info, 'result' => json_decode($result, true)];
    }
}

/**
 * WC_Freterapido class.
 */
class WC_Freterapido extends WC_Shipping_Method {

    /**
     * Dimensões padrão em KG
     *
     * @var array
     */
    private $default_dimensions = [
        'height' => 0.5,
        'width' => 0.5,
        'length' => 0.5,
        'weight' => 1
    ];

    /**
     * Será usada pelo produto que não tenha uma categoria do FR definida para ele
     *
     * @var int
     */
    private $default_fr_category = 999;

    /**
     * Initialize the Frete Rápido shipping method.
     *
     * @param int $instance_id
     */
    public function __construct($instance_id = 0) {
        $this->id = 'freterapido';
        $this->instance_id = absint($instance_id);
        $this->method_title = __('Frete Rápido', 'woo-shipping-gateway');

        $this->title = __('Frete Rápido', 'woo-shipping-gateway'); // This can be added as an setting but for this example its forced.

        $this->init();
    }

	/**
	 * Initializes the method.
	 *
	 * @return void
	 */
	public function init() {
        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

		// Define user set variables.
        $this->enabled = $this->get_option('enabled');
        $this->cnpj = $this->get_option('cnpj');
        $this->results = $this->get_option('results');
        $this->limit = $this->get_option('limit');
        $this->additional_time = $this->get_option('additional_time');
        $this->additional_price = $this->get_option('additional_price');
        $this->token = $this->get_option('token');

        // Active logs.
        if ('yes' == $this->debug) {
            if (class_exists('WC_Logger')) {
                $this->log = new WC_Logger();
            }
        }

        // Actions.
        add_action('woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
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
                                <img src="https://freterapido.com/imgs/frete_rapido.png" style="margin: auto"/>
                            </a>
                            <div style="padding-top: 20px;">
                                Configure abaixo a sua conta com os dados da loja para obter as cotações de frete através do Frete Rápido.
                                </br>
                                O token e as configurações dos Correios estão disponíveis no seu 
                                <a href="https://freterapido.com/painel/" target="_blank">Painel administrativo</a>.
                                </br>
                                Em caso de dúvidas, reporte de bugs ou sugestão de melhorias, acesse a 
                                <a href="https://github.com/freterapido/freterapido_woocommerce" target="_blank">documentação deste módulo no Github</a>.
                                </br>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>',
                    'woo-shipping-gateway'
                ),
                'type' => 'title'
            ),
            'enabled' => array(
                'title' => __('Enable/Disable', 'woo-shipping-gateway'),
                'type' => 'checkbox',
                'label' => __('Habilitar plugin', 'woo-shipping-gateway'),
                'default' => 'yes'
            ),
            'cnpj' => array(
                'title' => __('CNPJ', 'woo-shipping-gateway'),
                'type' => 'text',
                'description' => __('CNPJ da sua loja.', 'woo-shipping-gateway'),
                'desc_tip' => true
            ),
            'results' => array(
                'title' => __('Resultados', 'woo-shipping-gateway'),
                'type' => 'select',
                'options' => array('0' => 'Sem filtro (todas as ofertas)', '1' => 'Somente oferta com menor preço', '2' => 'Retornar somente a oferta com menor prazo de entrega'),
                'label' => __('Ativado', 'woo-shipping-gateway'),
                'description' => __('Como você gostaria de receber os resultados?', 'woo-shipping-gateway'),
                'desc_tip' => true,
                'default' => 0
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
                'title' => __('Prazo adicional de envio/postagem (dias)', 'woo-shipping-gateway'),
                'type' => 'text',
                'description' => __('Será acrecido no prazo do frete.', 'woo-shipping-gateway'),
                'desc_tip' => true,
                'default' => '0',
                'placeholder' => '0'
            ),
            'additional_price' => array(
                'title' => __('Custo adicional de envio/postagem (R$)', 'woo-shipping-gateway'),
                'type' => 'text',
                'description' => __('Será acrecido no valor do frete.', 'woo-shipping-gateway'),
                'desc_tip' => true,
                'default' => '2'
            ),
            'token' => array(
                'title' => __('Token', 'woo-shipping-gateway'),
                'type' => 'text',
                'description' => __('Token de integração com o Frete Rápido.', 'woo-shipping-gateway'),
                'desc_tip' => true
            )
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
    public function is_available($package) {
        $is_available = true;

        if ('no' == $this->enabled) {
            $is_available = false;
        }

        return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package);
    }

    /**
     * Fix Zip Code format.
     *
     * @param mixed $zip Zip Code.
     *
     * @return int
     */
    protected function fix_zip_code($zip) {
        $fixed = preg_replace('([^0-9])', '', $zip);

        return $fixed;
    }

    /**
     * Calculates the shipping rate.
     *
     * @param array $package Order package.
     *
     * @return void
     */
    public function calculate_shipping($package = array()) {
        if (empty($this->token)) {
            return;
        }

        $products = array_map(function ($item) {
            $product = wc_get_product($item['product_id']);
            $manufacturing_deadline = get_post_meta($product->id, 'manufacturing_deadline', true);
            /** @var WC_Product_Simple $product */
            $product = $item['data'];
            /** @var WP_Term[] $product_categories */
            $product_categories = get_the_terms($product->id, 'product_cat');

            $find_fr_category = function (WP_Term $category) {
                return $this->find_category($category->term_id);
            };

            $not_null = function ($category) {
                return $category !== null;
            };

            $fr_categories = array_filter(array_map($find_fr_category, $product_categories), $not_null);
            $fr_category = ['code' => $this->default_fr_category];
            $dispatcher = [];

            // Determina a categoria do Frete Rápido para o volume
            while (count($fr_categories) > 0) {
                $dispatcher = [];
                $product_category = array_shift($fr_categories);
                $product_category_fr_data = get_option("taxonomy_" . $product_category->term_id);
                $has_dispatcher = $product_category_fr_data['fr_origin_cep'] && $product_category_fr_data['fr_origin_rua'] && $product_category_fr_data['fr_origin_numero'] && $product_category_fr_data['fr_origin_bairro'];

                if ($product_category_fr_data && ($has_dispatcher)) {
                    $dispatcher = array(
                        'cep' => $product_category_fr_data['fr_origin_cep'],
                        'rua' => $product_category_fr_data['fr_origin_rua'],
                        'numero' => $product_category_fr_data['fr_origin_numero'],
                        'complemento' => $product_category_fr_data['fr_origin_complemento'],
                        'bairro' => $product_category_fr_data['fr_origin_bairro'],
                    );
                }

                $fr_category = ['code' => $product_category_fr_data['fr_category']];

                if ($product_category_fr_data['fr_category'] && $has_dispatcher) {
                    $fr_categories = [];
                }
            }

            $height = wc_get_dimension($product->height, 'm');
            $width = wc_get_dimension($product->width, 'm');
            $length = wc_get_dimension($product->length, 'm');
            $weight = wc_get_weight($product->weight, 'kg');

            return array(
                'quantidade' => $item['quantity'],
                'altura' => $height ?: $this->default_dimensions['height'],
                'largura' => $width ?: $this->default_dimensions['width'],
                'comprimento' => $length ?: $this->default_dimensions['length'],
                'peso' => $weight ?: ($this->default_dimensions['weight'] * $item['quantity']),
                'valor' => $item['line_total'],
                'sku' => $product->sku,
                'tipo' => $fr_category['code'],
                'origem' => $dispatcher,
                'prazo_fabricacao' => $manufacturing_deadline
            );
        }, $package['contents']);

        $chunks = array();

        // Agrupa os volumes por origem
        while (count($products) > 0) {
            $product = array_shift($products);
            $new_chunk = array($product);

            $same_origin = array_filter($products, function ($_product) use ($product) {
                return $_product['origem'] == $product['origem'];
            });

            $products = array_diff_assoc($products, $same_origin);
            $new_chunk = array_merge($new_chunk, $same_origin);
            $chunks[] = $new_chunk;
        }

        $quotes = [];

        // Realiza uma cotação para cada origem diferente
        foreach ($chunks as $chunk) {
            $dispatcher = $chunk[0]['origem'];
            $shipping = new Shipping([
                'token' => $this->token,
                'codigo_plataforma' => 'woocomm26'
            ]);

            $volumes = array_map(function ($volume) {
                unset($volume['origem']);
                return $volume;
            }, array_values($chunk));

            try {
                $quotes[] = $shipping
                    ->add_receiver([
                        'tipo_pessoa' => 1,
                        'endereco' => [
                            'cep' => $this->fix_zip_code($package['destination']['postcode'])
                        ]
                    ])
                    ->add_sender(['cnpj' => $this->cnpj])
                    ->add_volumes($volumes)
                    ->set_filter($this->results)
                    ->set_limit($this->limit)
                    ->add_dispatcher($dispatcher)
                    ->get_quote();
            } catch (Exception $invalid_argument) {
                return;
            }
        }

        $merged_quote = array_reduce($quotes, function ($carry, $item) {
            $offer = array_shift($item['transportadoras']);
            if (!$carry) {
                return $offer;
            }

            if ($offer['prazo_entrega'] > $carry['prazo_entrega']) {
                $carry['prazo_entrega'] = $offer['prazo_entrega'];
            }

            $carry['preco_frete'] += $offer['preco_frete'];
            $carry['custo_frete'] += $offer['custo_frete'];

            return $carry;
        });

        $deadline = $merged_quote['prazo_entrega'];
        $deadline_text = '(' . sprintf(_n('Delivery in %d working day', 'Delivery in %d working days', $deadline, 'freterapido'), $deadline) . ')';

        $rate = array(
            'id' => $this->id,
            'label' => "{$this->title} {$deadline_text}",
            'cost' => $merged_quote['preco_frete'],
        );

        $this->add_rate($rate);
    }

    private function find_category($category_id) {
        $category = get_term($category_id, 'product_cat');

        $fr_category = get_option("taxonomy_" . $category_id);

        if ($fr_category['fr_category']) {
            return $category;
        }

        // Não relacionou nenhuma das categorias vinculadas ao produto com uma categoria do Frete Rápido
        if ($category->parent == 0) {
            return null;
        }

        return $this->find_category($category->parent);
    }
}
