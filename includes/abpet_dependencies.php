<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Dependencies' ) ) {
		class ABPET_Dependencies {
			private const ORDER_SCHEMA_VERSION = 3;
			public function __construct() {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ), 90 );
				add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ), 90 );
				$this->load_file();
				add_action( 'init', [ $this, 'register_cpt' ] );
				add_filter( 'use_block_editor_for_post_type', [ $this, 'disable_gutenberg' ], 10, 2 );
				add_filter( 'plugin_action_links', array( $this, 'plugin_settings_link' ), 10, 2 );
				add_action( 'upgrader_process_complete', [ $this, 'flush_rewrite' ] );
				add_action( 'admin_init', array( $this, 'activation_redirect' ) );
				add_action( 'init', array( $this, 'maybe_upgrade_order_schema' ), 5 );
				add_action( 'save_post_' . ABPET_Function::get_cpt(), function (): void {
					ABPET_Query::flush_cache();
				}, 20 );
			}
			public function admin_enqueue( $hook ): void {
				$screen    = get_current_screen();
				$post_type = $screen ? $screen->post_type : '';
				if ( ! str_contains( $hook, ABPET_Function::slug() ) && $post_type !== ABPET_Function::get_cpt() ) {
					return;
				}
				$label   = ABPET_Function::label();
				$post_id = get_the_ID();
				$this->global_enqueue();
				wp_enqueue_editor();
				wp_enqueue_media();
				//admin script
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-codemirror' );
				wp_enqueue_script( 'wp-codemirror' );
				//=============================//
				wp_enqueue_script( 'abpet_admin', ABPET_URL . 'assets/js/abpet_admin.js', array( 'jquery' ), time(), true );
				wp_localize_script( 'abpet_admin', 'abpet_admin_data', [
					'ajax_url'        => admin_url( 'admin-ajax.php' ),
					'nonce'           => wp_create_nonce( 'abpet_admin_ajax_nonce' ),
					'icon_url'        => ABPET_URL . 'assets/js/abpet_icons.json',
					'related_info'    => wp_json_encode( ABPET_Function::related_info_js( $post_id ) ),
					'abpet_feature'   => wp_json_encode( ABPET_Function::option_js( $post_id, 'abpet_feature' ) ),
					'abpet_category'  => wp_json_encode( ABPET_Function::option_js( $post_id, 'abpet_category' ) ),
					'abpet_organizer' => wp_json_encode( ABPET_Function::option_js( $post_id, 'abpet_organizer' ) ),
					'abpet_brand'     => wp_json_encode( ABPET_Function::option_js( $post_id, 'abpet_brand' ) ),
					'sp_data'         => wp_json_encode( ABPET_Seat_Plan::get_sp_js( $post_id ) ),
					'abpet_location'  => wp_json_encode( ABPET_Function::option_js( $post_id, 'abpet_location' ) ),
					'msg'             => [
						'confirm_delete'      => __( 'Are you sure you want to delete this item?', 'abp-event-ticket' ),
						'confirm_ok'          => __( '1. Ok : To Remove Item .', 'abp-event-ticket' ),
						'confirm_cancel'      => __( '2. Cancel : To Cancel .', 'abp-event-ticket' ),
						'saving'              => __( 'Saving.............!', 'abp-event-ticket' ),
						'saved'               => __( 'Saved...............!', 'abp-event-ticket' ),
						'date_content'        => __( 'Importing Global Date Configuration........', 'abp-event-ticket' ),
						'additional_content'  => __( 'Importing Global Additional Service Configuration........', 'abp-event-ticket' ),
						'client_form_content' => __( 'Importing Global Attendee Form Configuration........', 'abp-event-ticket' ),
						'faq_content'         => __( 'Importing Global FAQ Configuration........', 'abp-event-ticket' ),
						'tc_content'          => __( 'Importing Global term And Condition Configuration........', 'abp-event-ticket' ),
						'importing'           => __( 'Importing........', 'abp-event-ticket' ),
						'imported'            => __( 'Imported Successfully............. !', 'abp-event-ticket' ),
						'loading'             => __( 'Loading........', 'abp-event-ticket' ),
						'price_loading'       => __( 'Price Configuration Loading........', 'abp-event-ticket' ),
						'type_switch'         => __( 'Ticket Type Switching... Please Wait.......', 'abp-event-ticket' ),
						'loaded'              => __( 'Loaded Successfully............. !', 'abp-event-ticket' ),
						'order_loading'       => __( 'Order Loading........ !', 'abp-event-ticket' ),
						'error'               => __( 'An error occurred. Please try again.', 'abp-event-ticket' ),
						'deleting'            => __( 'Deleting.............', 'abp-event-ticket' ),
						'delete_success'      => __( 'Item Deleted Successfully............. !', 'abp-event-ticket' ),
						'select_stops'        => __( 'Select Location..', 'abp-event-ticket' ),
						'select_ticket'       => __( 'Select Ticket Type..', 'abp-event-ticket' ),
						'post_loading'        => $label . ' ' . __( 'List Loading.............', 'abp-event-ticket' ),
						'permanent_remove'    => $label . ' ' . __( 'Permanent Deleting.........!', 'abp-event-ticket' ),
						'move_trash'          => $label . ' ' . __( 'move to Trashing.........!', 'abp-event-ticket' ),
						'restore'             => $label . ' ' . __( 'Restoring.........!', 'abp-event-ticket' ),
						'wc_install_active'   => __( 'WooCommerce Downloading And Installing.........Please Wait...............!!', 'abp-event-ticket' ),
						'wc_active'           => __( 'WooCommerce  Installing.........Please Wait...............!!', 'abp-event-ticket' ),
						'create_post_page'    => __( 'Page Creating ........!', 'abp-event-ticket' ),
						'no_item'             => __( 'No More Item Found !', 'abp-event-ticket' ),
						'no_item_selected'    => __( 'No Item selected !', 'abp-event-ticket' ),
					],
				] );
				wp_enqueue_style( 'abpet_admin', ABPET_URL . 'assets/css/abpet_admin.css', array(), time() );
				wp_enqueue_script( 'abpet_sp', ABPET_URL . 'assets/js/abpet_sp.js', array( 'jquery' ), time(), true );
				wp_localize_script( 'abpet_sp', 'abpet_sp_config', [
					'seat_type'  => wp_json_encode( ABPET_Seat_Plan::get_ticket_type_js() ),
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'nonce'      => wp_create_nonce( 'abpet_admin_ajax_nonce' ),
					'decor_item' => wp_json_encode( ABPET_Seat_Plan::get_decor_js() ),
					'msg'        => [
						'sp_delete_confirm' => __( 'Are you sure you want to delete this Seat Plan?', 'abp-event-ticket' ),
						'sp_clear_confirm'  => __( 'Are you absolutely sure you want to clear the full layout?', 'abp-event-ticket' ),
						'sp_clear'          => __( 'Layout clear successfully.............!', 'abp-event-ticket' ),
						'sp_deleting'       => __( 'Seat Plan Deleting.............!', 'abp-event-ticket' ),
						'sp_saving'         => __( 'Seat Plan Saving.............!', 'abp-event-ticket' ),
						'sp_loading'        => __( 'Seat Plan Loading.............!', 'abp-event-ticket' ),
					],
				] );
				//=============================//
				do_action( 'abpet_admin_enqueue' );
			}
			public function frontend_enqueue(): void {
				// Always enqueued on the frontend so the plugin's shortcodes work on any page
				// and its styles apply wherever its UI is embedded (cart, checkout, account, etc.).
				if ( in_array( 'woocommerce/woocommerce.php', (array) get_option( 'active_plugins' ), true ) ) {
					wp_enqueue_script( 'wc-checkout' );
					wp_enqueue_style( 'select2' );
					wp_enqueue_script( 'select2' );
				}
				wp_enqueue_script( 'abpet_frontend', ABPET_URL . 'assets/js/abpet_frontend.js', array( 'jquery' ), time(), true );
				wp_enqueue_script( 'abpet_slick', ABPET_URL . 'assets/js/slick.min.js', array( 'jquery' ), ABPET_VERSION, true );
				$this->global_enqueue();
				do_action( 'abpet_frontend_enqueue' );
			}
			public function global_enqueue(): void {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_style( 'abpet_jquery_ui', ABPET_URL . 'assets/css/jquery-ui.min.css', array(), '1.13.2' );
				wp_enqueue_style( 'abpet_font_awesome', ABPET_URL . 'assets/css/font_awesome.min.css', array(), '5.15.4' );
				wp_enqueue_style( 'abpet_lib', ABPET_URL . 'assets/css/abpet_lib.css', array(), time() );
				wp_enqueue_script( 'abpet_lib', ABPET_URL . 'assets/js/abpet_lib.js', array( 'jquery' ), time(), true );
				if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
					wp_localize_script( 'abpet_lib', 'abpet_var', [
						'currency_symbol'     => get_woocommerce_currency_symbol(),
						'currency_position'   => get_option( 'woocommerce_currency_pos' ),
						'currency_decimal'    => wc_get_price_decimal_separator(),
						'thousands_separator' => wc_get_price_thousand_separator(),
						'decimal_num'         => ABPET_Function::get_option( 'woocommerce_price_num_decimals', 2 ),
						'currency_suffix'     => ABPET_Function::get_option( 'woocommerce_price_display_suffix', '' ),
						'blank_image'         => ABPET_BLANK_IMG_URL,
						'date_format'         => ABPET_JS_Date_Format,
					] );
				} else {
					wp_localize_script( 'abpet_lib', 'abpet_var', [
						'currency_symbol'     => '',
						'currency_position'   => '',
						'currency_decimal'    => '',
						'thousands_separator' => '',
						'decimal_num'         => '',
						'wc_suffix'           => '',
						'blank_image'         => ABPET_BLANK_IMG_URL,
						'date_format'         => ABPET_JS_Date_Format,
					] );
				}
				$colors          = ABPET_Function::get_option( 'abpet_color' );
				$available       = ! empty( $colors['available'] ) ? $colors['available'] : "#D4EDDA";
				$sold            = ! empty( $colors['sold'] ) ? $colors['sold'] : "#F8D7DA";
				$booked          = ! empty( $colors['booked'] ) ? $colors['booked'] : "#6C757D";
				$selected        = ! empty( $colors['selected'] ) ? $colors['selected'] : "#007BFF";
				$abpet_css_var   = ABPET_Function::get_option( 'abpet_css_var' );
				$default_color   = ( $abpet_css_var['color_default'] ?? null ) ?: '#303030';
				$color_theme     = ( $abpet_css_var['color_theme'] ?? null ) ?: '#95951c';
				$alternate_color = ( $abpet_css_var['color_theme_alternate'] ?? null ) ?: '#fff';
				$color_warning   = ( $abpet_css_var['color_warning'] ?? null ) ?: '#E67C30';
				$bg_section      = ( $abpet_css_var['bg_section'] ?? null ) ?: '#FAFCFE';
				$bg_button       = ( $abpet_css_var['bg_button'] ?? null ) ?: '#222';
				$color_button    = ( $abpet_css_var['color_button'] ?? null ) ?: $alternate_color;
				$color_theme_ee  = $color_theme . 'ee';
				$color_theme_cc  = $color_theme . 'cc';
				$color_theme_aa  = $color_theme . 'aa';
				$color_theme_88  = $color_theme . '88';
				$color_theme_77  = $color_theme . '77';
				$default_br      = ! empty( $abpet_css_var['br_default'] ) ? $abpet_css_var['br_default'] . 'px' : '5px';
				$br_xl           = ! empty( $abpet_css_var['br_default'] ) ? $abpet_css_var['br_default'] * 2 . 'px' : '10px';
				$fs_h1           = ! empty( $abpet_css_var['fs_h1'] ) ? $abpet_css_var['fs_h1'] . 'px' : '30px';
				$fs_h2           = ! empty( $abpet_css_var['fs_h2'] ) ? $abpet_css_var['fs_h2'] . 'px' : '26px';
				$fs_h3           = ! empty( $abpet_css_var['fs_h3'] ) ? $abpet_css_var['fs_h3'] . 'px' : '24px';
				$fs_h4           = ! empty( $abpet_css_var['fs_h4'] ) ? $abpet_css_var['fs_h4'] . 'px' : '20px';
				$fs_h5           = ! empty( $abpet_css_var['fs_h5'] ) ? $abpet_css_var['fs_h5'] . 'px' : '17px';
				$fs_h6           = ! empty( $abpet_css_var['fs_h6'] ) ? $abpet_css_var['fs_h6'] . 'px' : '15px';
				$fs_label        = ! empty( $abpet_css_var['fs_label'] ) ? $abpet_css_var['fs_label'] . 'px' : '14px';
				$default_fs      = ! empty( $abpet_css_var['fs_default'] ) ? $abpet_css_var['fs_default'] . 'px' : '12px';
				$button_fs       = ! empty( $abpet_css_var['fs_button'] ) ? $abpet_css_var['fs_button'] . 'px' : '14px';
				$off             = esc_html__( 'OFF', 'abp-event-ticket' );
				$on              = esc_html__( 'ON', 'abp-event-ticket' );
				$abpet_var       =
					":root {
						--et_br: {$default_br};						
						--et_br_xl: {$br_xl};						
						--et_text_off:'{$off}';
						--et_text_on: '{$on}';
						--et_fs: {$default_fs};				
						--et_fs_label: {$fs_label};
						--et_fs_h6: {$fs_h6};
						--et_fs_h5: {$fs_h5};
						--et_fs_h4: {$fs_h4};
						--et_fs_h3: {$fs_h3};
						--et_fs_h2: {$fs_h2};
						--et_fs_h1: {$fs_h1};						
						--et_button_bg: {$bg_button};
						--et_button_color: {$color_button};
						--et_button_fs: {$button_fs};						
						--et_color_default: {$default_color};						
						--et_color_section: {$bg_section};
						--et_color_theme: {$color_theme};
						--et_color_theme_ee: {$color_theme_ee};
						--et_color_theme_cc: {$color_theme_cc};
						--et_color_theme_aa: {$color_theme_aa};
						--et_color_theme_88: {$color_theme_88};
						--et_color_theme_77: {$color_theme_77};
						--et_color_theme_alter: {$alternate_color};
						--et_color_warning:{$color_warning};						
						--et_color_available:{$available};						
						--et_color_sold:{$sold};						
						--et_color_booked:{$booked};						
						--et_color_seclected:{$selected};						
					}";
				wp_add_inline_style( 'abpet_lib', wp_kses_post( $abpet_var ) );
				wp_enqueue_style( 'abpet', ABPET_URL . 'assets/css/abpet.css', array(), time() );
				wp_enqueue_script( 'abpet_infos', ABPET_URL . 'assets/js/abpet.js', array( 'jquery' ), time(), true );
				$rental_data = array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'abpet_ajax_nonce' ),
					'now'      => current_time( 'Y-m-d H:i' ),
					'msg'      => [
						'date_loading'        => __( 'Date  Loading.............', 'abp-event-ticket' ),
						'end_date_loading'    => __( 'Return Date  Loading.............', 'abp-event-ticket' ),
						'select_post'         => __( 'Please Select', 'abp-event-ticket' ) . ' ' . ABPET_Function::label(),
						'select_start_date'   => __( 'Please Select Journey Date', 'abp-event-ticket' ),
						'select_session_time' => __( 'Please Select Session Time', 'abp-event-ticket' ),
						'free'                => __( 'FREE', 'abp-event-ticket' ),
						'loading'             => __( 'Loading..............!', 'abp-event-ticket' ),
					],
				);
				wp_localize_script( 'abpet_infos', 'abpet_infos', $rental_data );
				do_action( 'abpet_global_script' );
			}
			private function load_file(): void {
				require_once ABPET_DIR . 'includes/abpet_static.php';
				require_once ABPET_DIR . 'includes/abpet_function.php';
				require_once ABPET_DIR . 'includes/abpet_query.php';
				require_once ABPET_DIR . 'includes/abpet_layout.php';
				if ( is_admin() ) {
					require_once ABPET_DIR . 'admin/abpet_admin.php';
					require_once ABPET_DIR . 'admin/abpet_dashboard.php';
					require_once ABPET_DIR . 'admin/abpet_post.php';
					require_once ABPET_DIR . 'admin/abpet_ticket.php';
					require_once ABPET_DIR . 'admin/abpet_orders.php';
					require_once ABPET_DIR . 'admin/abpet_dates.php';
					require_once ABPET_DIR . 'admin/abpet_additional.php';
					require_once ABPET_DIR . 'admin/abpet_form.php';
					require_once ABPET_DIR . 'admin/abpet_seat_plan.php';
					require_once ABPET_DIR . 'admin/abpet_resource.php';
					require_once ABPET_DIR . 'admin/abpet_configuration.php';
					require_once ABPET_DIR . 'admin/abpet_documentation.php';
					require_once ABPET_DIR . 'admin/abpet_category.php';
					require_once ABPET_DIR . 'admin/abpet_organizer.php';
					require_once ABPET_DIR . 'admin/abpet_location.php';
					require_once ABPET_DIR . 'admin/abpet_brand.php';
					require_once ABPET_DIR . 'admin/abpet_feature.php';
				}
				if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
					require_once ABPET_DIR . 'includes/abpet_hooks.php';
					require_once ABPET_DIR . 'includes/abpet_ajax.php';
					require_once ABPET_DIR . 'includes/abpet_frontend.php';
					require_once ABPET_DIR . 'includes/abpet_shortcodes.php';
					require_once ABPET_DIR . 'includes/abpet_woocommerce.php';
					require_once ABPET_DIR . 'includes/abpet_my_account.php';
					require_once ABPET_DIR . 'admin/abpet_hidden_post.php';
				}
			}
			public function register_cpt(): void {
				$cpt   = ABPET_Function::get_cpt();
				$label = ABPET_Function::label();
				register_post_type( $cpt, [
					'public'              => true,
					'labels'              => [
						'name'                  => esc_html( $label ),
						'singular_name'         => esc_html( $label ),
						'menu_name'             => esc_html( $label ),
						'name_admin_bar'        => esc_html( $label ),
						'archives'              => __( 'Post List', 'abp-event-ticket' ),
						'attributes'            => __( 'Post List', 'abp-event-ticket' ),
						'parent_item_colon'     => __( 'Post Item:', 'abp-event-ticket' ),
						'all_items'             => __( 'Post', 'abp-event-ticket' ),
						'add_new_item'          => __( 'Add Post', 'abp-event-ticket' ),
						'add_new'               => __( 'Add Post', 'abp-event-ticket' ),
						'new_item'              => __( 'Add Post', 'abp-event-ticket' ),
						'edit_item'             => __( 'Edit Post', 'abp-event-ticket' ),
						'update_item'           => __( 'Update Post', 'abp-event-ticket' ),
						'view_item'             => __( 'View Post', 'abp-event-ticket' ),
						'view_items'            => __( 'View Post', 'abp-event-ticket' ),
						'search_items'          => __( 'Search Post', 'abp-event-ticket' ),
						'not_found'             => __( 'Post Not Found', 'abp-event-ticket' ),
						'not_found_in_trash'    => __( 'Post Not found in Trash', 'abp-event-ticket' ),
						'featured_image'        => __( 'Post Image', 'abp-event-ticket' ),
						'set_featured_image'    => __( 'Post Image', 'abp-event-ticket' ),
						'remove_featured_image' => __( 'Remove Post Image', 'abp-event-ticket' ),
						'use_featured_image'    => __( 'Use image Post as featured image', 'abp-event-ticket' ),
						'insert_into_item'      => __( 'Insert  Post', 'abp-event-ticket' ),
						'uploaded_to_this_item' => __( 'Uploaded  Post', 'abp-event-ticket' ),
						'items_list'            => __( 'Post List', 'abp-event-ticket' ),
						'items_list_navigation' => __( 'Category list navigation', 'abp-event-ticket' ),
						'filter_items_list'     => __( 'Filter Post List', 'abp-event-ticket' )
					],
					'menu_icon'           => ABPET_Function::icon_wp(),
					'supports'            => [ 'title', 'editor', 'thumbnail' ],
					'rewrite'             => [ 'slug' => ABPET_Function::slug(), 'with_front' => true, 'pages' => true, 'feeds' => true, ],
					'show_in_rest'        => true,
					'rest_base'           => 'abpet_post',
					'capability_type'     => 'post',
					'publicly_queryable'  => true,  // you should be able to query it
					'show_ui'             => true,  // you should be able to edit it in wp-admin
					'show_in_menu'        => false,
					'exclude_from_search' => true,  // you should exclude it from search results
					'show_in_nav_menus'   => true,  // you should be able to add it to menus
					'has_archive'         => true,  // it should have archive page
				] );
				register_taxonomy( 'abpet_location', $cpt, [
					'hierarchical'      => true,
					"public"            => true,
					'labels'            => [
						'name'          => $label . ' ' . ABPET_Function::location_label(),
						'singular_name' => $label . ' ' . ABPET_Function::location_label(),
					],
					'show_ui'           => true,
					'show_admin_column' => false,
					'show_in_menu'      => false,
					'query_var'         => true,
					'rewrite'           => [ 'slug' => ABPET_Function::location_slug() ],
					'show_in_rest'      => true,
					'rest_base'         => 'abpet_location',
					'meta_box_cb'       => false,
				] );
				if ( ABPET_Function::on_off( 'category' ) ) {
					register_taxonomy( 'abpet_category', $cpt, [
						'hierarchical'      => true,
						"public"            => true,
						'labels'            => [
							'name'          => $label . ' ' . ABPET_Function::category_label(),
							'singular_name' => $label . ' ' . ABPET_Function::category_label(),
						],
						'show_ui'           => true,
						'show_admin_column' => false,
						'show_in_menu'      => false,
						'query_var'         => true,
						'rewrite'           => [ 'slug' => ABPET_Function::category_slug() ],
						'show_in_rest'      => true,
						'rest_base'         => 'abpet_category',
						'meta_box_cb'       => false,
					] );
				}
				if ( ABPET_Function::on_off( 'organizer' ) ) {
					register_taxonomy( 'abpet_organizer', $cpt, [
						'hierarchical'      => true,
						"public"            => true,
						'labels'            => [
							'name'          => $label . ' ' . ABPET_Function::organizer_label(),
							'singular_name' => $label . ' ' . ABPET_Function::organizer_label(),
						],
						'show_ui'           => true,
						'show_admin_column' => false,
						'show_in_menu'      => false,
						'query_var'         => true,
						'rewrite'           => [ 'slug' => ABPET_Function::organizer_slug() ],
						'show_in_rest'      => true,
						'rest_base'         => 'abpet_organizer',
						'meta_box_cb'       => false,
					] );
				}
				if ( ABPET_Function::on_off( 'brand' ) ) {
					register_taxonomy( 'abpet_brand', $cpt, [
						'hierarchical'      => true,
						"public"            => true,
						'labels'            => [
							'name'          => $label . ' ' . ABPET_Function::brand_label(),
							'singular_name' => $label . ' ' . ABPET_Function::brand_label(),
						],
						'show_ui'           => true,
						'show_admin_column' => false,
						'show_in_menu'      => false,
						'query_var'         => true,
						'rewrite'           => [ 'slug' => ABPET_Function::brand_slug() ],
						'show_in_rest'      => true,
						'rest_base'         => 'abpet_brand',
						'meta_box_cb'       => false,
					] );
				}
				flush_rewrite_rules();
			}
			public static function activation(): void {
				self::create_table();
				flush_rewrite_rules();
			}
			public static function deactivate(): void {
				flush_rewrite_rules();
			}
			public static function create_table(): void {
				global $wpdb;
				$order_table    = $wpdb->prefix . 'abpet_orders';
				$sp_table       = $wpdb->prefix . 'abpet_sp';
				$collate        = $wpdb->get_charset_collate();
				$schema_version = (int) get_option( 'abpet_orders_schema_version', 0 );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table existence probe on init/upgrade.
				$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $order_table ) ) === $order_table;
				if ( $table_exists && $schema_version < self::ORDER_SCHEMA_VERSION ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Schema migration; table name is $wpdb->prefix + fixed suffix.
					$wpdb->query( "DROP TABLE IF EXISTS `{$order_table}`" );
				}
				$abpet_orders = "CREATE TABLE $order_table (
					        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
					        order_id bigint(20) unsigned NOT NULL,
					        item_id bigint(20) unsigned NOT NULL,
					        post_id bigint(20) unsigned NOT NULL,
					        user_id bigint(20) unsigned NOT NULL,
					        event_date date DEFAULT NULL,
					        session_time time DEFAULT NULL,
					        seat_type varchar(20) NOT NULL DEFAULT 'ticket',
					        sp_id bigint(20) unsigned NOT NULL DEFAULT 0,
					        ticket_info text DEFAULT NULL,
					        ticket_id text DEFAULT NULL,
					        qty int(5) NOT NULL DEFAULT 1,
					        price decimal(10,2) DEFAULT NULL,
					        ex_info text DEFAULT NULL,
					        ex_id text DEFAULT NULL,
					        ex_price decimal(10,2) DEFAULT NULL,
					        total decimal(10,2) DEFAULT NULL,
					        pp_choice varchar(20) DEFAULT NULL,
					        pp_full varchar(100) DEFAULT NULL,
					        pp_deposit varchar(100) DEFAULT NULL,
					        pp_balance varchar(100) DEFAULT NULL,
					        pp_date datetime DEFAULT NULL,
					        pass_info text DEFAULT NULL,
					        checkin tinyint(1) NOT NULL DEFAULT 0,
					        order_status varchar(30) NOT NULL DEFAULT 'wc-pending',
					        payment_method varchar(100) DEFAULT NULL,
					        billing_name varchar(100) DEFAULT NULL,
					        billing_email varchar(100) DEFAULT NULL,
					        billing_phone varchar(50) DEFAULT NULL,
					        billing_address varchar(255) DEFAULT NULL,
					        payment_status varchar(30) DEFAULT NULL,					        
					        others text DEFAULT NULL,
					        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
					        updated_at datetime DEFAULT NULL,
					        PRIMARY KEY  (id),
					        KEY order_id  (order_id),
					        KEY user_id  (user_id),
					        KEY item_id  (item_id),
					        KEY post_id  (post_id),
					        KEY sp_id  (sp_id),
					        KEY order_status  (order_status),
					        KEY checkin  (checkin),
					        KEY event_date  (event_date),
					        KEY session_time  (session_time),
					        KEY avail  (post_id, event_date, session_time)
					    ) $collate;";
				// Seat Plan Table
				$sp = "CREATE TABLE $sp_table (
					        id mediumint unsigned NOT NULL AUTO_INCREMENT,
					        name varchar(100) DEFAULT NULL,
					        total_seats mediumint NOT NULL DEFAULT 0,
					        layout_data longtext DEFAULT NULL,
					        seat_info longtext DEFAULT NULL,
					        others longtext DEFAULT NULL,
					        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
					        updated_at datetime DEFAULT NULL,
					        PRIMARY KEY  (id)
					    ) $collate;";
				if ( ! function_exists( 'dbDelta' ) ) {
					require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				}
				dbDelta( $abpet_orders );
				dbDelta( $sp );
				update_option( 'abpet_orders_schema_version', self::ORDER_SCHEMA_VERSION );
			}
			public function maybe_upgrade_order_schema(): void {
				global $wpdb;
				$order_table = $wpdb->prefix . 'abpet_orders';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Table existence probe on init/upgrade.
				$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $order_table ) ) === $order_table;
				if ( ! $table_exists || (int) get_option( 'abpet_orders_schema_version', 0 ) < self::ORDER_SCHEMA_VERSION ) {
					self::create_table();
				}
			}
			public function plugin_settings_link( $links_array, $plugin_file_name ) {
				if ( strpos( $plugin_file_name, ABPET_BASE ) ) {
					array_unshift( $links_array, '<a class="abp" href="' . esc_url( ABPET_Function::build_url( 'configuration' ) ) . '">' . __( 'Configuration', 'abp-event-ticket' ) . '</a>' );
				}
				return $links_array;
			}
			public function flush_rewrite(): void {
				flush_rewrite_rules();
			}
			public function disable_gutenberg( $current_status, $post_type ) {
				if ( $post_type === ABPET_Function::get_cpt() ) {
					return false;
				}
				return $current_status;
			}
			public function activation_redirect(): void {
				$active_tab = '';
				$page       = '';
				if ( isset( $_GET['_abpet_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abpet_nonce'] ) ), 'abpet_url_action' ) ) {
					$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'dashboard';
					$page       = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
				}
				if ( $page === ABPET_Function::slug() && ABPET_WC < 2 && $active_tab != 'dashboard' ) {
					wp_safe_redirect( ABPET_Function::build_url( 'dashboard' ) );
					exit;
				}
			}
		}
		new ABPET_Dependencies();
	}