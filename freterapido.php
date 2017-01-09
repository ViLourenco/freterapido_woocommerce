<?php
/**
 * Plugin Name: WooCommerce Frete Rápido
 * Plugin URI: https://github.com/...
 * Description: Frete Rápido para WooCommerce
 * Author: Frete Rápido
 * Author URI: http://www.freterapido.com
 * Version: 2.1.0
 * License: GPLv2 or later
 * Text Domain: freterapido
 * Domain Path: languages/
 */

define( 'WOO_FR_PATH', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Freterapido_Main' ) ) :

    /**
     * Frete Rápido main class.
     */
    class WC_Freterapido_Main {
        /**
         * Plugin version.
         *
         * @var string
         */
        const VERSION = '2.1.0';

        /**
         * Instance of this class.
         *
         * @var object
         */
        protected static $instance = null;

        /**
         * Initialize the plugin
         */
        private function __construct() {
            add_action( 'init', array( $this, 'load_plugin_textdomain' ), -1 );

            add_action( 'wp_ajax_ajax_simulator', array( 'WC_Freterapido_Shipping_Simulator', 'ajax_simulator' ) );
            add_action( 'wp_ajax_nopriv_ajax_simulator', array( 'WC_Freterapido_Shipping_Simulator', 'ajax_simulator' ) );

            // Checks with WooCommerce is installed.
            if ( class_exists( 'WC_Integration' ) ) {
                include_once WOO_FR_PATH . 'includes/class-wc-freterapido.php';
                include_once WOO_FR_PATH . 'includes/class-wc-freterapido-shipping-simulator.php';

                add_filter( 'woocommerce_shipping_methods', array( $this, 'wcfreterapido_add_method' ) );

            } else {
                add_action( 'admin_notices', array( $this, 'wcfreterapido_woocommerce_fallback_notice' ) );
            }

            if ( ! class_exists( 'SimpleXmlElement' ) ) {
                add_action( 'admin_notices', 'wcfreterapido_extensions_missing_notice' );
            }
        }

        /**
         * Return an instance of this class.
         *
         * @return object A single instance of this class.
         */
        public static function get_instance() {
            // If the single instance hasn't been set, set it now.
            if ( null === self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * Load the plugin text domain for translation.
         */
        public function load_plugin_textdomain() {
            load_plugin_textdomain( 'freterapido', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }

        /**
         * Get main file.
         *
         * @return string
         */
        public static function get_main_file() {
            return __FILE__;
        }

        /**
         * Get plugin path.
         *
         * @return string
         */
        public static function get_plugin_path() {
            return plugin_dir_path( __FILE__ );
        }

        /**
         * Get templates path.
         *
         * @return string
         */
        public static function get_templates_path() {
            return self::get_plugin_path() . 'templates/';
        }

        /**
         * Add the Frete Rápido to shipping methods.
         *
         * @param array $methods
         *
         * @return array
         */
        function wcfreterapido_add_method( $methods ) {
            $methods['freterapido'] = 'WC_Freterapido';

            return $methods;
        }

    }

    add_action( 'plugins_loaded', array( 'WC_Freterapido_Main', 'get_instance' ) );

    // ///////////////////////////
    // create custom fields in category page
    // ///////////////////////////

    //Product Cat creation page
    function text_domain_taxonomy_add_new_meta_field() {
        ?>
            <hr>
            <h1>Configurações do Frete Rápido</h1>
            <div class="form-field">
                <!-- <label for="term_meta[wh_meta_title]"><?php _e('Meta Title', 'text_domain'); ?></label> -->
                <label for="fr_category"><?php _e('Categoria no Frete Rápido', 'text_domain'); ?></label>
                <select name="fr_category" id="fr_category">
                    <option value="0" selected>-- Selecione --</option>
                    <option value="1">Abrasivos</option>
                    <option value="2">Adubos / Fertilizantes</option>
                    <option value="3">Alimentos</option>
                    <option value="4">Artigos para Pesca</option>
                    <option value="5">Auto Peças</option>
                    <option value="6">Bebidas / Destilados</option>
                    <option value="7">Brindes</option>
                    <option value="8">Brinquedos</option>
                    <option value="9">Calçados</option>
                    <option value="62">Cargas Refrigeradas/Congeladas</option>
                    <option value="10">CD / DVD / Blu-Ray</option>
                    <option value="11">Combustíveis / Óleos</option>
                    <option value="12">Confecção</option>
                    <option value="13">Cosméticos / Perfumaria</option>
                    <option value="14">Couro</option>
                    <option value="15">Derivados Petróleo</option>
                    <option value="16">Descartáveis</option>
                    <option value="17">Editorial</option>
                    <option value="18">Eletrônicos</option>
                    <option value="19">Eletrodomésticos</option>
                    <option value="20">Embalagens</option>
                    <option value="21">Explosivos / Pirotécnicos</option>
                    <option value="22">Farmacêutico / Medicamentos</option>
                    <option value="23">Ferragens</option>
                    <option value="24">Ferramentas</option>
                    <option value="25">Fibras Ópticas</option>
                    <option value="26">Fonográfico</option>
                    <option value="27">Fotográfico</option>
                    <option value="28">Fraldas / Geriátricas</option>
                    <option value="29">Higiene / Limpeza</option>
                    <option value="30">Impressos</option>
                    <option value="31">Informática / Computadores</option>
                    <option value="32">Instrumento Musical</option>
                    <option value="33">Livro(s)</option>
                    <option value="34">Materiais Escolares</option>
                    <option value="35">Materiais Esportivos</option>
                    <option value="36">Materiais Frágeis</option>
                    <option value="37">Material de Construção</option>
                    <option value="38">Material de Irrigação</option>
                    <option value="39">Material Elétrico / Lâmpada(s)</option>
                    <option value="40">Material Gráfico</option>
                    <option value="41">Material Hospitalar</option>
                    <option value="42">Material Odontológico</option>
                    <option value="43">Material Pet Shop / Rações</option>
                    <option value="50">Material Plástico</option>
                    <option value="44">Material Veterinário</option>
                    <option value="45">Móveis / Utensílios</option>
                    <option value="46">Moto Peças</option>
                    <option value="47">Mudas / Plantas</option>
                    <option value="48">Papelaria / Documentos</option>
                    <option value="63">Papelão</option>
                    <option value="49">Perfumaria</option>
                    <option value="51">Pneus e Borracharia</option>
                    <option value="52">Produtos Cerâmicos</option>
                    <option value="53">Produtos Químicos</option>
                    <option value="54">Produtos Veterinários</option>
                    <option value="55">Revistas</option>
                    <option value="56">Sementes</option>
                    <option value="57">Suprimentos Agrícolas / Rurais</option>
                    <option value="58">Têxtil</option>
                    <option value="59">Vacinas</option>
                    <option value="60">Vestuário</option>
                    <option value="61">Vidros / Frágil</option>
                    <option value="999">Outros</option>
                </select>
                <!-- <p class="description"><?php _e('Enter a meta title, <= 60 character', 'text_domain'); ?></p> -->
            </div>
            <h2>Endereço de Origem:</h2>
            <span>Dados de endereço específicos por categoria</span>
            <div class="form-field">
                <label for="fr_origin_cep"><?php _e('Cep', 'text_domain'); ?></label>
                <input type="text" name="fr_origin_cep" id="fr_origin_cep">
                <p class="description"><?php _e('Apenas Números', 'text_domain'); ?></p>
            </div>
            <div class="form-field">
                <label for="fr_origin_rua"><?php _e('Rua', 'text_domain'); ?></label>
                <input type="text" name="fr_origin_rua" id="fr_origin_rua">
                <!-- <p class="description"><?php _e('Digite o CEP de origem para esta', 'text_domain'); ?></p> -->
            </div>
            <div class="form-field">
                <label for="fr_origin_numero"><?php _e('Número', 'text_domain'); ?></label>
                <input type="text" name="fr_origin_numero" id="fr_origin_numero">
                <!-- <p class="description"><?php _e('Digite o CEP de origem para esta', 'text_domain'); ?></p> -->
            </div>
            <div class="form-field">
                <label for="fr_origin_bairro"><?php _e('Bairro', 'text_domain'); ?></label>
                <input type="text" name="fr_origin_bairro" id="fr_origin_bairro">
                <!-- <p class="description"><?php _e('Digite o CEP de origem para esta', 'text_domain'); ?></p> -->
            </div>
            <div class="form-field">
                <label for="fr_origin_complemento"><?php _e('Complemento', 'text_domain'); ?></label>
                <input type="text" name="fr_origin_complemento" id="fr_origin_complemento">
                <!-- <p class="description"><?php _e('Digite o CEP de origem para esta', 'text_domain'); ?></p> -->
            </div>
            <hr>
        <?php
    }

    add_action('product_cat_add_form_fields', 'text_domain_taxonomy_add_new_meta_field', 10, 2);

    //Product Cat Edit page
    function text_domain_taxonomy_edit_meta_field($term) {

        //getting term ID
        $term_id = $term->term_id;

        // retrieve the existing value(s) for this meta field. This returns an array
        $term_meta = get_option("taxonomy_" . $term_id);
        ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                </th>
                <td>
                    <hr>
                    <h1>Configurações do Frete Rápido</h1>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="fr_category"><?php _e('Categoria no Frete Rápido', 'text_domain'); ?></label>
                </th>
                <td>
                    <!-- value="<?php echo esc_attr($term_meta['wh_meta_title']) ? esc_attr($term_meta['wh_meta_title']) : ''; ?>" -->
                    <select name="fr_category" id="fr_category">
                        <option value="0" selected>-- Selecione --</option>
                        <option value="1">Abrasivos</option>
                        <option value="2">Adubos / Fertilizantes</option>
                        <option value="3">Alimentos</option>
                        <option value="4">Artigos para Pesca</option>
                        <option value="5">Auto Peças</option>
                        <option value="6">Bebidas / Destilados</option>
                        <option value="7">Brindes</option>
                        <option value="8">Brinquedos</option>
                        <option value="9">Calçados</option>
                        <option value="62">Cargas Refrigeradas/Congeladas</option>
                        <option value="10">CD / DVD / Blu-Ray</option>
                        <option value="11">Combustíveis / Óleos</option>
                        <option value="12">Confecção</option>
                        <option value="13">Cosméticos / Perfumaria</option>
                        <option value="14">Couro</option>
                        <option value="15">Derivados Petróleo</option>
                        <option value="16">Descartáveis</option>
                        <option value="17">Editorial</option>
                        <option value="18">Eletrônicos</option>
                        <option value="19">Eletrodomésticos</option>
                        <option value="20">Embalagens</option>
                        <option value="21">Explosivos / Pirotécnicos</option>
                        <option value="22">Farmacêutico / Medicamentos</option>
                        <option value="23">Ferragens</option>
                        <option value="24">Ferramentas</option>
                        <option value="25">Fibras Ópticas</option>
                        <option value="26">Fonográfico</option>
                        <option value="27">Fotográfico</option>
                        <option value="28">Fraldas / Geriátricas</option>
                        <option value="29">Higiene / Limpeza</option>
                        <option value="30">Impressos</option>
                        <option value="31">Informática / Computadores</option>
                        <option value="32">Instrumento Musical</option>
                        <option value="33">Livro(s)</option>
                        <option value="34">Materiais Escolares</option>
                        <option value="35">Materiais Esportivos</option>
                        <option value="36">Materiais Frágeis</option>
                        <option value="37">Material de Construção</option>
                        <option value="38">Material de Irrigação</option>
                        <option value="39">Material Elétrico / Lâmpada(s)</option>
                        <option value="40">Material Gráfico</option>
                        <option value="41">Material Hospitalar</option>
                        <option value="42">Material Odontológico</option>
                        <option value="43">Material Pet Shop / Rações</option>
                        <option value="50">Material Plástico</option>
                        <option value="44">Material Veterinário</option>
                        <option value="45">Móveis / Utensílios</option>
                        <option value="46">Moto Peças</option>
                        <option value="47">Mudas / Plantas</option>
                        <option value="48">Papelaria / Documentos</option>
                        <option value="63">Papelão</option>
                        <option value="49">Perfumaria</option>
                        <option value="51">Pneus e Borracharia</option>
                        <option value="52">Produtos Cerâmicos</option>
                        <option value="53">Produtos Químicos</option>
                        <option value="54">Produtos Veterinários</option>
                        <option value="55">Revistas</option>
                        <option value="56">Sementes</option>
                        <option value="57">Suprimentos Agrícolas / Rurais</option>
                        <option value="58">Têxtil</option>
                        <option value="59">Vacinas</option>
                        <option value="60">Vestuário</option>
                        <option value="61">Vidros / Frágil</option>
                        <option value="999">Outros</option>
                    </select>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                </th>
                <td>
                    <h2>Endereço de Origem:</h2>
                    <span>Dados de endereço específicos por categoria</span>
                </td>
            </tr>
            <!-- <tr class="form-field">
                <th scope="row" valign="top"><label for="term_meta[wh_meta_desc]"><?php _e('Meta Description', 'text_domain'); ?></label></th>
                <td>
                    <textarea name="term_meta[wh_meta_desc]" id="term_meta[wh_meta_desc]"><?php echo esc_attr($term_meta['wh_meta_desc']) ? esc_attr($term_meta['wh_meta_title']) : ''; ?></textarea>
                    <p class="description"><?php _e('Enter a meta description', 'text_domain'); ?></p>
                </td>
            </tr> -->
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="fr_origin_cep"><?php _e('Cep', 'text_domain'); ?></label>
                </th>
                <td>
                    <input type="text" name="fr_origin_cep" id="fr_origin_cep" value="<?php echo esc_attr($term_meta['wh_meta_desc']) ? esc_attr($term_meta['wh_meta_title']) : ''; ?>">
                    <p class="description"><?php _e('Apenas Números', 'text_domain'); ?></p>
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="fr_origin_rua"><?php _e('Rua', 'text_domain'); ?></label>
                </th>
                <td>
                    <input type="text" name="fr_origin_rua" id="fr_origin_rua" value="<?php echo esc_attr($term_meta['wh_meta_desc']) ? esc_attr($term_meta['wh_meta_title']) : ''; ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="fr_origin_numero"><?php _e('Número', 'text_domain'); ?></label>
                </th>
                <td>
                    <input type="text" name="fr_origin_numero" id="fr_origin_numero" value="<?php echo esc_attr($term_meta['wh_meta_desc']) ? esc_attr($term_meta['wh_meta_title']) : ''; ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="fr_origin_bairro"><?php _e('Bairro', 'text_domain'); ?></label>
                </th>
                <td>
                    <input type="text" name="fr_origin_bairro" id="fr_origin_bairro" value="<?php echo esc_attr($term_meta['wh_meta_desc']) ? esc_attr($term_meta['wh_meta_title']) : ''; ?>">
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label for="fr_origin_complemento"><?php _e('Complemento', 'text_domain'); ?></label>
                </th>
                <td>
                    <input type="text" name="fr_origin_complemento" id="fr_origin_complemento" value="<?php echo esc_attr($term_meta['wh_meta_desc']) ? esc_attr($term_meta['wh_meta_title']) : ''; ?>">
                </td>
            </tr>
            <tr>
                <th scope="row" valign="top">
                </th>
                <td>
                    <hr>
                </td>
            </tr>
        <?php
    }

    add_action('product_cat_edit_form_fields', 'text_domain_taxonomy_edit_meta_field', 10, 2);

    // Save extra taxonomy fields callback function.
    function save_taxonomy_custom_meta($term_id) {
        if (isset($_POST['term_meta'])) {
            $term_meta = get_option("taxonomy_" . $term_id);
            $cat_keys = array_keys($_POST['term_meta']);
            foreach ($cat_keys as $key) {
                if (isset($_POST['term_meta'][$key])) {
                $term_meta[$key] = $_POST['term_meta'][$key];
                }
            }
            // Save the option array.
            update_option("taxonomy_" . $term_id, $term_meta);
        }
    }

    add_action('edited_product_cat', 'save_taxonomy_custom_meta', 10, 2);
    add_action('create_product_cat', 'save_taxonomy_custom_meta', 10, 2);

endif;
