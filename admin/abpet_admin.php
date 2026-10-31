<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_ADMIN' ) ) {
		class ABPET_ADMIN {
			public function __construct() {
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
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
				$allowed_tabs = [ 'dashboard', 'posts', 'orders', 'sp', 'configuration', 'documentation', 'admin_order' ];
				$active_tab   = 'dashboard';
				if ( isset( $_GET['_abpet_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abpet_nonce'] ) ), 'abpet_url_action' ) ) {
					$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'posts';
				}
				if ( ! in_array( $active_tab, $allowed_tabs, true ) ) {
					$active_tab = 'dashboard';
				}
				?>
                <div class="abpet_area  abpet_admin">
                    <div class="admin_head ">
                        <div class="head_brand">
                            <div class="brand_icon _all_center"><?php ABPET_Layout::image_icon( $icon ); ?></div>
                            <div class="_fd_column">
                                <h4 class="abp"><?php echo esc_html( $label ); ?></h4>
                                <span class="brand_version"><?php echo esc_html( ABPET_VERSION ); ?></span>
                            </div>
                        </div>
                        <div class="_group_content">
                            <a href="<?php echo esc_url( add_query_arg( 'tab', 'dashboard' ) ); ?>" class="_btn_xs <?php echo esc_attr( $active_tab == 'dashboard' ? 'abp_active' : '' ); ?>"><span>📊</span><?php esc_html_e( 'Dashboard', 'abp-event-ticket' ); ?></a>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'posts' ) ); ?>" class="_btn_xs post_tab <?php echo esc_attr( $active_tab == 'posts' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Layout::image_icon( $icon );
									echo esc_html( $label ) . ' ' . esc_html__( 'Lists', 'abp-event-ticket' ); ?>
                                <sup class="_color_theme">( <?php echo esc_html( $total_post ); ?> )</sup>
                            </a>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'orders' ) ); ?>" class="_btn_xs <?php echo esc_attr( $active_tab == 'orders' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Static::icon_svg( 'order' );
									esc_html_e( 'Orders', 'abp-event-ticket' ); ?>
                                <sup class="_color_theme">( <?php echo esc_html( $total_order ); ?> )</sup>
                            </a>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'sp' ) ); ?>" class="_btn_xs  <?php echo esc_attr( $active_tab == 'sp' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Static::icon_svg( 'seat' );
									esc_html_e( 'Ticket/Seat Plan', 'abp-event-ticket' ); ?>
                            </a>
							<?php do_action( 'abpet_add_admin_menu_tab_middle', $active_tab ); ?>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'configuration' ) ); ?>" class="_btn_xs <?php echo esc_attr( $active_tab == 'configuration' ? 'abp_active' : '' ); ?>">
								<?php ABPET_Static::icon_svg( 'setting' );
									esc_html_e( 'Global Configuration', 'abp-event-ticket' ); ?>
                            </a>
                            <a href="<?php echo esc_url( ABPET_Function::build_url( 'documentation' ) ); ?>" class="_btn_xs <?php echo esc_attr( $active_tab == 'documentation' ? 'abp_active' : '' ); ?>">
                                <span>📘</span><?php esc_html_e( 'Documentation', 'abp-event-ticket' ); ?>
                            </a>
							<?php do_action( 'abpet_add_admin_menu_tab', $active_tab ); ?>
                        </div>
						<?php if ( ABPET_WC == 2 ) { ?>
                            <div class="_group_content">
                                <button type="button" class="_btn_xs" data-href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . ABPET_Function::get_cpt() ) ); ?>" data-blank="_blank">
									<?php ABPET_Static::icon_svg( 'plus' );
										echo esc_html( $label ); ?>
                                </button>
								<?php ABPET_Layout::button_global_popup( 'tax_location', ABPET_Function::location_label(), '_btn_xs' );
									if ( ABPET_Function::on_off( 'category' ) ) {
										ABPET_Layout::button_global_popup( 'tax_category', ABPET_Function::category_label(), '_btn_xs' );
									} ?>
                            </div>
						<?php } ?>
                    </div>
                    <div class="dashboard_content">
						<?php do_action('abpet_notice');
                            do_action( 'abpet_load_' . $active_tab, $abpet_info ); ?>
                    </div>
					<?php ABPET_Layout::load_admin_globally(); ?>
                </div>
				<?php
			}
		}
		new ABPET_ADMIN();
	}