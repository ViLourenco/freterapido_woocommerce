<?php
/**
 * Admin orders actions.
 *
 * @package WooCommerce_Correios/Admin/Orders
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios orders.
 */
class WC_Freterapido_Orders {

	/**
	 * Initialize the order actions.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'save_tracking_code' ) );
		add_filter( 'woocommerce_resend_order_emails_available', array( $this, 'resend_tracking_code_email' ) );
	}

	/**
	 * Register tracking code metabox.
	 */
	public function register_metabox() {
		add_meta_box(
			'wc_freterapido',
			'Frete Rápido',
			array( $this, 'metabox_content' ),
			'shop_order',
			'side',
			'default'
		);
	}

	/**
	 * Tracking code metabox content.
	 *
	 * @param WP_Post $post Post data.
	 */
	public function metabox_content( $post ) {
		$shippings = wc_get_order_item_meta( $post->ID, 'freterapido_shippings' ) ?: array();

		echo '<div><p><strong>' . _n( 'Shipping code contracted:', 'Shipping codes contracted:', count( $shippings ), 'freterapido' ) . '</strong></p><table id="newmeta"><tbody>';
		if ( empty( $shippings ) ) {
			echo '<tr><td>' . __( 'Waiting for status:', 'freterapido' ) . ' <b>' . __( 'Awaiting shipment', 'freterapido' ) . '</b></td></tr>';
		}

		foreach ( $shippings as $shipping ) {
			echo "<tr><td><a target='_blank' href='https://freterapido.com/rastreio/#/" .
				preg_replace( '/\W/', '', $shipping ) .
				"'>{$shipping}</a></td></tr>";
		}

		echo    '</tbody></table></div>';
	}

	/**
	 * Save tracking code.
	 *
	 * @param int $post_id Current post type ID.
	 */
	public function save_tracking_code( $post_id ) {
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) {
			return;
		}

		if ( isset( $_POST['correios_tracking'] ) ) {
			wc_correios_update_tracking_code( $post_id, wp_unslash( $_POST['correios_tracking'] ) );
		}
	}
}

new WC_Freterapido_Orders();
