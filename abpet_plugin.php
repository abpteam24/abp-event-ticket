<?php
	/**
	 * Plugin Name: ABP Event Ticket
	 * Description: WooCommerce event ticketing system for selling general admission tickets and reserved seats.
	 * Version: 1.0.2
	 * Author: abpteam
	 * Author URI: https://abp-team.com
	 * Text Domain: abp-event-ticket
	 * Domain Path: /languages
	 * WC requires at least: 8.0.0
	 *  WC tested up to: 9.4
	 *  Requires PHP: 7.4
	 *  Requires MySQL: 5.7+
	 *  License: GPLv2 or later
	 *  License URI: https://www.gnu.org/licenses/gpl-2.0.html
	 */
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Plugin' ) ) {
		class ABPET_Plugin {
			public function __construct() {
				add_action( 'admin_init', function () {
					if ( ! function_exists( 'is_plugin_active' ) ) {
						require_once ABSPATH . 'wp-admin/includes/plugin.php';
					}
				} );
				add_action(
					'before_woocommerce_init', // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
					function () {
						if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
							\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
								'custom_order_tables',
								__FILE__,
								true
							);
						}
					}
				);
				$this->load_plugin();
			}

			private function load_plugin(): void {
				if ( ! defined( 'ABPET_Plugin_FILE' ) ) {
					define( 'ABPET_Plugin_FILE', __FILE__ );
				}
				if ( ! defined( 'ABPET_VERSION' ) ) {
					define( 'ABPET_VERSION', '1.0.2' );
				}
				if ( ! defined( 'ABPET_DIR' ) ) {
					define( 'ABPET_DIR', plugin_dir_path( __FILE__ ) );
				}
				if ( ! defined( 'ABPET_URL' ) ) {
					define( 'ABPET_URL', plugin_dir_url( __FILE__ ) );
				}
				if ( ! defined( 'ABPET_BASE' ) ) {
					define( 'ABPET_BASE', basename( __FILE__ ) );
				}
				if ( ! defined( 'ABPET_BLANK_IMG_URL' ) ) {
					define( 'ABPET_BLANK_IMG_URL', ABPET_URL . 'assets/images/blank_image.png' );
				}
				require_once ABPET_DIR . 'includes/abpet_dependencies.php';
				if ( ! defined( 'ABPET_WC' ) ) {
					define( 'ABPET_WC', ABPET_Function::check_wc() );
				}
				if ( ! defined( 'ABPET_Configuration' ) ) {
					define( 'ABPET_Configuration', ABPET_Function::get_option( 'abpet_configuration' ) );
				}
				if ( ! defined( 'ABPET_Date_Config' ) ) {
					define( 'ABPET_Date_Config', ABPET_Function::get_option( 'abpet_date_config' ) );
				}
				if ( ! defined( 'ABPET_Dates' ) ) {
					define( 'ABPET_Dates', ABPET_Function::get_option( 'abpet_dates' ) );
				}
				if ( ! defined( 'ABPET_Category' ) ) {
					define( 'ABPET_Category', ABPET_Function::get_option( 'abpet_category' ) );
				}
				if ( ! defined( 'ABPET_Organizer' ) ) {
					define( 'ABPET_Organizer', ABPET_Function::get_option( 'abpet_organizer' ) );
				}
				if ( ! defined( 'ABPET_Feature' ) ) {
					define( 'ABPET_Feature', ABPET_Function::get_option( 'abpet_feature' ) );
				}
				if ( ! defined( 'ABPET_Location' ) ) {
					define( 'ABPET_Location', ABPET_Function::get_option( 'abpet_location' ) );
				}
				if ( ! defined( 'ABPET_Brand' ) ) {
					define( 'ABPET_Brand', ABPET_Function::get_option( 'abpet_brand' ) );
				}
				if ( ! defined( 'ABPET_ids' ) ) {
					define( 'ABPET_ids', ABPET_Query::get_post_id());
				}
				if ( ! defined( 'ABPET_Ticket' ) ) {
					define( 'ABPET_Ticket', ABPET_Function::get_option( 'abpet_ticket' ) );
				}
				if ( ! defined( 'ABPET_Decor' ) ) {
					define( 'ABPET_Decor', ABPET_Function::get_option( 'abpet_decor' ) );
				}
				if ( ! defined( 'ABPET_Ticket_SP' ) ) {
					define( 'ABPET_Ticket_SP', ABPET_Function::get_option( 'abpet_ticket_sp' ) );
				}
				if ( ! defined( 'ABPET_On_Off' ) ) {
					define( 'ABPET_On_Off', ABPET_Function::get_option( 'abpet_on_off' ) );
				}
				if ( ! defined( 'ABPET_JS_Date_Format' ) ) {
					define( 'ABPET_JS_Date_Format', ABPET_Function::date_format_js() );
				}
				if ( ! defined( 'ABPET_Time_Format' ) ) {
					define( 'ABPET_Time_Format', ABPET_Date_Config['time_format'] ?? get_option( 'time_format' ) );
				}
			}
		}
		new ABPET_Plugin();
		register_activation_hook( __FILE__, function () {
			if ( class_exists( 'ABPET_Dependencies' ) ) {
				ABPET_Dependencies::activation();
			}
		} );
		register_deactivation_hook( __FILE__, function () {
			if ( class_exists( 'ABPET_Dependencies' ) ) {
				ABPET_Dependencies::deactivate();
			}
		} );
		register_uninstall_hook( __FILE__, 'abpet_uninstall' );
	}

	/**
	 * Clean up plugin data on uninstall.
	 *
	 * Called automatically by WordPress when the plugin is deleted
	 * via register_uninstall_hook, and also invoked manually from
	 * uninstall.php for backward compatibility.
	 */
	function abpet_uninstall(): void {
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			return;
		}

		$abpet_on_off = get_option( 'abpet_on_off', array() );
		$remove_data  = is_array( $abpet_on_off ) && isset( $abpet_on_off['remove_uninstall'] ) ? sanitize_text_field( $abpet_on_off['remove_uninstall'] ) : 'off';

		if ( 'on' !== $remove_data ) {
			return;
		}

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $wpdb->prefix . 'abpet_orders' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $wpdb->prefix . 'abpet_sp' ) );

		$abpet_options = array(
			'abpet_configuration',
			'abpet_on_off',
			'abpet_date_config',
			'abpet_dates',
			'abpet_category',
			'abpet_location',
			'abpet_organizer',
			'abpet_brand',
			'abpet_feature',
			'abpet_ticket',
			'abpet_ticket_sp',
			'abpet_decor',
			'abpet_additional',
			'abpet_form',
			'abpet_faq',
			'abpet_tc',
			'abpet_color',
			'abpet_css_var',
			'abpet_slider',
			'abpet_contact',
			'abpet_per_page_item',
			'abpet_dummy_registry',
			'abpet_orders_schema_version',
			'abpet_activation_redirect',
		);

		foreach ( $abpet_options as $option ) {
			delete_option( $option );
		}

		$posts = get_posts( array(
			'post_type'      => 'abpet_post',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		) );
		foreach ( $posts as $post_id ) {
			wp_delete_post( $post_id, true );
		}

		$taxonomies = array( 'abpet_category', 'abpet_location', 'abpet_organizer', 'abpet_brand' );
		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'fields'     => 'ids',
			) );
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term_id ) {
					wp_delete_term( $term_id, $taxonomy );
				}
			}
		}
	}