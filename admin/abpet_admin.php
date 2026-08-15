<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_ADMIN' ) ) {
		class ABPET_ADMIN {
			public function __construct() {
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
				add_action( 'abpet_load_global', array( $this, 'load_global' ) );
			}
			public function admin_menu(): void {
				$label = ABPET_Function::label();
				$slug  = ABPET_Function::slug();
				$icon  = ABPET_Function::icon_wp();
				add_menu_page( $label, $label, 'manage_options', $slug, array( $this, 'load_main_page' ), $icon, 50 );
			}
			public function load_main_page(): void {
				remove_all_actions( 'user_admin_notices' );
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' );
				remove_all_actions( 'network_admin_notices' );
				add_filter( 'wp_dependency_installer_errors', '__return_false' );
				$abpet_info   = ABPET_Query::get_info();
				$label        = ABPET_Function::label();
				$icon         = ABPET_Function::icon();
				$total_post   = $abpet_info['total_post'] ?? 0;
				$total_order  = $abpet_info['total_order'] ?? 0;
				$allowed_tabs = [ 'dashboard', 'posts', 'orders', 'sp', 'global', 'configuration', 'status', 'documentation', 'admin_order' ];
				$active_tab   = 'posts';
				if ( isset( $_GET['_abpet_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abpet_nonce'] ) ), 'abpet_url_action' ) ) {
					$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'posts';
				}
				if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
					$active_tab = 'posts';
				}
				if ( ABPET_WC < 2 ) {
					$active_tab = 'status';
				}
				?>
                <div class="abpet_area  abpet_admin">
                    <div class="admin_head ">
                        <div class="head_brand">
                            <div class="brand_icon _all_center"><?php ABPET_Layout::image_icon( $icon ); ?></div>
                            <div class="_fd_column">
                                <h4 class="_abp"><?php echo esc_html( $label ); ?></h4>
                                <span class="brand_version"><?php echo esc_html( ABPET_VERSION ); ?></span>
                            </div>
                        </div>
                        <div class="_group_content">
                            <!--                            <a href="--><?php //echo esc_url( add_query_arg( 'tab', 'dashboard' ) ); ?><!--" class="_btn_light_info --><?php //echo esc_attr( $active_tab == 'dashboard' ? 'abp_active' : '' ); ?><!--"><span class="_mar_r_xs">📊</span>--><?php //esc_html_e( 'Dashboard', 'abp-event-ticket' ); ?><!--</a>-->
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'posts' ) ); ?>" class="_btn_white_xs post_tab <?php echo esc_attr( $active_tab == 'posts' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Layout::image_icon( $icon );
									echo esc_html( $label ) . ' ' . esc_html__( 'Lists', 'abp-event-ticket' ); ?>
                                <sup class="_color_theme">( <?php echo esc_html( $total_post ); ?> )</sup>
                            </a>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'orders' ) ); ?>" class="_btn_white_xs <?php echo esc_attr( $active_tab == 'orders' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Static::icon_svg( 'order' );
									esc_html_e( 'Orders', 'abp-event-ticket' ); ?>
                                <sup class="_color_theme">( <?php echo esc_html( $total_order ); ?> )</sup>
                            </a>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'sp' ) ); ?>" class="_btn_white_xs  <?php echo esc_attr( $active_tab == 'sp' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Static::icon_svg( 'seat' );
									esc_html_e( 'Ticket/Seat Plan', 'abp-event-ticket' ); ?>
                            </a>
							<?php do_action( 'abpet_add_admin_menu_tab_middle', $active_tab ); ?>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'global' ) ); ?>" class="_btn_white_xs <?php echo esc_attr( $active_tab == 'global' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Static::icon_svg( 'globe' );
									esc_html_e( 'Global Data', 'abp-event-ticket' ); ?>
                            </a>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'configuration' ) ); ?>" class="_btn_white_xs <?php echo esc_attr( $active_tab == 'configuration' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Static::icon_svg( 'setting' );
									esc_html_e( 'Configuration', 'abp-event-ticket' ); ?>
                            </a>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'status' ) ); ?>" class="_btn_white_xs <?php echo esc_attr( $active_tab == 'status' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Static::icon_svg( 'status' );
									esc_html_e( 'Status', 'abp-event-ticket' ); ?>
                            </a>
							<?php do_action( 'abpet_add_admin_menu_tab', $active_tab ); ?>
                        </div>
						<?php if ( ABPET_WC == 2 ) { ?>
                            <div class="_group_content">
                                <button type="button" class="_btn_white_xs" data-href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . ABPET_Function::get_cpt() ) ); ?>" data-blank="_blank">
									<?php ABPET_Static::icon_svg( 'plus' );
										echo esc_html( $label ); ?>
                                </button>
								<?php ABPET_Layout::button_global_popup( 'tax_location', ABPET_Function::location_label(), '_btn_white_xs' );
									if ( ABPET_Function::on_off( 'category' ) ) {
										ABPET_Layout::button_global_popup( 'tax_category', ABPET_Function::category_label(), '_btn_white_xs' );
									} ?>
                            </div>
						<?php } ?>
                    </div>
                    <div class="dashboard_content">
						<?php do_action( 'abpet_load_' . $active_tab, $abpet_info ); ?>
                    </div>
					<?php ABPET_Layout::load_admin_globally(); ?>
                </div>
				<?php
			}
			public function load_global( $abpet_info ): void {
				$allowed_tabs = [ 'dates', 'additional', 'client_form', 'resource', 'category', 'organizer', 'location', 'feature', 'brand', 'discount' ];
				$active_tab   = 'dates';
				if ( isset( $_GET['_abpet_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abpet_nonce'] ) ), 'abpet_url_action' ) ) {
					$active_tab = isset( $_GET['global'] ) ? sanitize_text_field( wp_unslash( $_GET['global'] ) ) : 'dates';
				}
				if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
					$active_tab = 'dates';
				}
				?>
                <div class="_max_1200_mar_auto">
                    <div class="_section_card_w_full">
                        <div class="_group_content_w_full_f_equal_f_wrap">
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'global', [ 'global' => 'dates' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'dates' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Static::icon_svg( 'date_1' ); ?><?php esc_html_e( 'Dates', 'abp-event-ticket' ); ?>
                            </a>
							<?php if ( ABPET_Function::on_off( 'additional_info' ) ) { ?>
                                <a href="<?php echo esc_url( ABPET_Function::build_url( 'global', [ 'global' => 'additional' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'additional' ? 'abp_active' : '' ); ?>">💰<?php esc_html_e( 'Additional services', 'abp-event-ticket' ); ?></a>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'client_info' ) ) { ?>
                                <a href="<?php echo esc_url( ABPET_Function::build_url( 'global', [ 'global' => 'client_form' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'client_form' ? 'abp_active' : '' ); ?>">📋<?php esc_html_e( 'Client Form', 'abp-event-ticket' ); ?></a>
							<?php } ?>
							<?php do_action( 'abpet_add_admin_global_tab', $active_tab ); ?>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'global', [ 'global' => 'location' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'location' ? 'abp_active' : '' ); ?>">
                                <span class="fas fa-route"></span><?php echo esc_html( ABPET_Function::location_label() ); ?>
                            </a>
							<?php if ( ABPET_Function::on_off( 'category' ) ) { ?>
                                <a href="<?php echo esc_url( ABPET_Function::build_url( 'global', [ 'global' => 'category' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'category' ? 'abp_active' : '' ); ?>">
									<?php ABPET_Static::icon_svg( 'category_1' ); ?><?php echo esc_html( ABPET_Function::category_label() ); ?>
                                </a>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'organizer' ) ) { ?>
                                <a href="<?php echo esc_url( ABPET_Function::build_url( 'global', [ 'global' => 'organizer' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'organizer' ? 'abp_active' : '' ); ?>">🏢<?php echo esc_html( ABPET_Function::organizer_label() ); ?></a>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'brand' ) ) { ?>
                                <a href="<?php echo esc_url( ABPET_Function::build_url( 'global', [ 'global' => 'brand' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'brand' ? 'abp_active' : '' ); ?>">🏷️<?php echo esc_html( ABPET_Function::brand_label() ); ?></a>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'feature' ) ) { ?>
                                <a href="<?php echo esc_url( ABPET_Function::build_url( 'global', [ 'global' => 'feature' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'feature' ? 'abp_active' : '' ); ?>">🔗<?php echo esc_html( ABPET_Function::feature_label() ); ?></a>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'tc' ) || ABPET_Function::on_off( 'faq' ) ) { ?>
                                <a href="<?php echo esc_url( ABPET_Function::build_url( 'global', [ 'global' => 'resource' ] ) ); ?>" class="_btn_light_green_pale_xs  <?php echo esc_attr( $active_tab == 'resource' ? 'abp_active' : '' ); ?>">📚<?php esc_html_e( 'Resources', 'abp-event-ticket' ); ?></a>
							<?php } ?>
                        </div>
                        <div class="_divider_xs"></div>
						<?php do_action( 'abpet_global_' . $active_tab, $abpet_info ); ?>
                    </div>
                </div>
				<?php
			}
		}
		new ABPET_ADMIN();
	}