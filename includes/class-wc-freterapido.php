<?php

class Shipping {
    const API_URL = 'http://api-externa.freterapido.app/embarcador/v1/quote-simulator';

    private $config;
    private $sender;
    private $receiver;
    private $dispatcher;
    private $volumes;

    private $default_dimensions = [
        'height' => 0.5,
        'width' => 0.5,
        'length' => 0.5,
        'weight' => 1
    ];

    public function __construct(array $config) {
        $this->config = array_merge([
            'tipo_cobranca' => 1,
            'tipo_frete' => 1,
            'ecommerce' => true,
        ], $config);
    }

    public function set_default_dimensions(array $dimensions) {
        foreach ($this->default_dimensions as $dimension => $value) {
            $new_value = (float) $dimensions[$dimension];

            if ($new_value < $value) {
                continue;
            }

            $this->default_dimensions[$dimension] = $new_value;
        }

        return $this;
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
        $this->volumes = array_map(function ($volume) {
            if (!$volume['altura']) {
                $volume['altura'] = $this->default_dimensions['height'];
            }

            if (!$volume['largura']) {
                $volume['largura'] = $this->default_dimensions['width'];
            }

            if (!$volume['comprimento']) {
                $volume['comprimento'] = $this->default_dimensions['length'];
            }

            if (!$volume['peso']) {
                $volume['peso'] = $this->default_dimensions['weight'] * $volume['quantidade'];
            }

            return $volume;
        }, $volumes);

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
            $this->config['limite'] = $limit;
        }

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

        $result['transportadoras'] = array_map(function ($carrier) {
            if (strtolower($carrier['nome']) === 'correios') {
                $carrier['nome'] .= " - {$carrier['servico']}";
            }

            return $carrier;
        }, $result['transportadoras']);

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
        $this->method_title = __('Frete Rápido', 'freterapido');
        $this->title = __('Frete Rápido', 'freterapido');

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
        $this->additional_time = $this->get_option('additional_time', 0);
        $this->additional_price = $this->get_option('additional_price', 0);
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
        $this->instance_form_fields  = include( 'data-wf-settings.php' );

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
                'altura' => $height,
                'largura' => $width,
                'comprimento' => $length,
                'peso' => $weight * $item['quantity'],
                'valor' => $item['line_total'],
                'sku' => $product->sku,
                'tipo' => $fr_category['code'],
                'origem' => $dispatcher,
                'prazo_fabricacao' => $manufacturing_deadline
            );
        }, $package['contents']);

        $chunks = array();

        $products_to_chunk = $products;

        // Agrupa os volumes por origem
        while (count($products_to_chunk) > 0) {
            $product = array_shift($products_to_chunk);
            $new_chunk = array($product);

            $same_origin = array_filter($products_to_chunk, function ($_product) use ($product) {
                return $_product['origem'] == $product['origem'];
            });

            $products_to_chunk = array_diff_assoc($products_to_chunk, $same_origin);
            $new_chunk = array_merge($new_chunk, $same_origin);
            $chunks[] = $new_chunk;
        }

        $quotes = [];

        // Realiza uma cotação para cada origem diferente
        foreach ($chunks as $chunk) {
            $dispatcher = $chunk[0]['origem'];
            $shipping = new Shipping([
                'token' => $this->token,
                'codigo_plataforma' => 'woocomm26',
                'custo_adicional' => $this->additional_price,
                'prazo_adicional' => $this->additional_time,
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
                    ->set_default_dimensions([
                        'length' => $this->get_option('min_length', 0),
                        'width' => $this->get_option('min_width', 0),
                        'height' => $this->get_option('min_height', 0),
                    ])
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

        $manufacturing_deadline = array_reduce($products, function ($carry, $product) {
            if ($carry < $product['prazo_fabricacao']) {
                return (int) $product['prazo_fabricacao'];
            }

            return $carry;
        }, 0);

        $merged_quote['prazo_entrega'] += $manufacturing_deadline;

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
