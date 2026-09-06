<?php
	/**
	 * Plugin Name: ABP Event Ticket
	 * Description: WooCommerce event ticketing system for selling general admission tickets and reserved seats.
	 * Version: 1.0.0
	 * Author: abpteam
	 * Author URI: https://abp-team.com
	 * Text Domain: abp-event-ticket
	 * Domain Path: /languages
	 * WC requires at least: 8.0.0
	 *  WC tested up to: latest
	 *  Requires PHP: 7.4
	 *  Requires MySQL: 5.7+
	 *  License: GPLv3
	 *  License URI: https://www.gnu.org/licenses/gpl-3.0.html
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
					define( 'ABPET_VERSION', '1.0.0' );
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
	}