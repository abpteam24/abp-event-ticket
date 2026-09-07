<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'ABPET_Status' ) ) {
		class ABPET_Status {
			public function __construct() {
				add_action( 'abpet_load_status', array( $this, 'load_status' ) );
				add_action( 'wp_ajax_abpet_wc_config', array( $this, 'wc_config' ) );
				add_action( 'wp_ajax_abpet_create_page', array( $this, 'create_page' ) );
				add_action( 'wp_ajax_abpet_import_dummy', array( $this, 'import_dummy' ) );
				add_action( 'wp_ajax_abpet_remove_dummy', array( $this, 'remove_dummy' ) );
			}
			public function load_status(): void {
				?>
                <div class="_abp_panel_max_1200_mar_auto abp_status">
                    <div class="_panel_head">
                        <h3 class="_abp_gap_xs"><span>🛡️</span> <?php esc_html_e( 'Status  & Information', 'abp-event-ticket' ); ?></h3>
                    </div>
                    <div class="_panel_body_fd_column_gap_xs">
						<?php
							if ( ABPET_WC < 2 ) {
								ABPET_Layout::layout_warning_info_xs( 'must_wc' );
								if ( ABPET_WC == 1 ) { ?>
                                    <button class="_btn_navy_blue_xs" onclick="abpet_wc_config('wc_active')" type="button"><span class="fas fa-tasks"></span><?php esc_html_e( 'Active Now', 'abp-event-ticket' ); ?></button>
								<?php } else { ?>
                                    <button class="_btn_navy_blue_xs" onclick="abpet_wc_config('wc_install_active')" type="button"><span class="fas fa-file-download"></span><?php esc_html_e( 'Install & Active Now', 'abp-event-ticket' ); ?></button>
								<?php }
							}
							$this->version();
							$this->wordpress();
							$this->php();
							$this->wc();
							if ( ABPET_WC > 1 ) {
								do_action( 'abpet_add_tools' );
								$this->post_page();
							}
						?>
                    </div>
                </div>
				<?php
			}
			public function version(): void {
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abp"> <?php esc_html_e( 'Event Ticket Version', 'abp-event-ticket' ) ?> </h6>
                        <button class="_btn_light_success_xs" type="button"><span class="fas fa-check"></span><?php echo esc_html( ABPET_VERSION ); ?></button>
                    </div>
                </div>
				<?php
			}
			public function wordpress(): void {
				$version = get_bloginfo( 'version' );
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abp"> <?php esc_html_e( 'WordPress Version', 'abp-event-ticket' ); ?> </h6>
						<?php if ( $version > 5.5 ) { ?>
                            <button class="_btn_light_success_xs" type="button"><span class="fas fa-check"></span><?php echo esc_html( $version ); ?></button>
						<?php } else { ?>
                            <button class="_btn_light_warning_xs" type="button"><span class="fas fa-exclamation-triangle"></span><?php echo esc_html( $version ); ?></button>
						<?php } ?>
                    </div>
                </div>
				<?php
			}
			public function php(): void {
				$version = phpversion();
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abp"> <?php esc_html_e( 'Php Version', 'abp-event-ticket' ); ?> </h6>
						<?php if ( $version > 7.4 ) { ?>
                            <button class="_btn_light_success_xs" type="button"><span class="fas fa-check"></span><?php echo esc_html( $version ); ?></button>
						<?php } else { ?>
                            <button class="_btn_light_warning_xs" type="button"><span class="fas fa-exclamation-triangle"></span><?php echo esc_html( $version ); ?></button>
						<?php } ?>
                    </div>
                </div>
				<?php
			}
			public function wc(): void {
				$title = ABPET_WC == 2 ? __( 'WooCommerce Plugin', 'abp-event-ticket' ) : __( 'WooCommerce need to install and active', 'abp-event-ticket' );
				$title = ABPET_WC == 1 ? __( 'WooCommerce already installed but  not  activated', 'abp-event-ticket' ) : $title;
				$name  = get_option( 'woocommerce_email_from_name' );
				$email = get_option( 'woocommerce_email_from_address' );
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abp"> <?php echo esc_html( $title ); ?></h6>
						<?php if ( ABPET_WC == 2 ) { ?>
                            <button class="_btn_light_success_xs" type="button"><span class="fas fa-check"></span><?php esc_html_e( 'Activated', 'abp-event-ticket' ); ?></button>
						<?php } elseif ( ABPET_WC == 1 ) { ?>
                            <button class="_btn_warning_xs" onclick="abpet_wc_config('wc_active')" type="button"><span class="fas fa-tasks"></span><?php esc_html_e( 'Active Now', 'abp-event-ticket' ); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_xs" onclick="abpet_wc_config('wc_install_active')" type="button"><span class="fas fa-file-download"></span><?php esc_html_e( 'Install & Active Now', 'abp-event-ticket' ); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
					<?php if ( ABPET_WC == 2 && defined( 'WC_VERSION' ) ) { ?>
                        <div class="_fa_center_fj_between">
                            <h6 class="_abp"><?php esc_html_e( 'WooCommerce Version', 'abp-event-ticket' ); ?></h6>
							<?php if ( version_compare( WC_VERSION, '8.0', '>' ) ) { ?>
                                <button class="_btn_light_success_xs" type="button"><span class="fas fa-check"></span><?php echo esc_html( WC_VERSION ); ?></button>
							<?php } else { ?>
                                <button class="_btn_light_warning_xs" type="button"><span class="fas fa-exclamation-triangle"></span><?php echo esc_html( WC_VERSION ); ?></button>
							<?php } ?>
                        </div>
						<?php if ( ! empty( $name ) ) { ?>
                            <div class="_divider_xs"></div>
                            <div class="_fa_center_fj_between">
                                <h6 class="_abp"><?php esc_html_e( 'Name', 'abp-event-ticket' ); ?></h6>
                                <button class="_btn_light_success_xs" type="button"><?php echo esc_html( $name ); ?></button>
                            </div>
						<?php } ?>
						<?php if ( ! empty( $email ) ) { ?>
                            <div class="_divider_xs"></div>
                            <div class="_fa_center_fj_between">
                                <h6 class="_abp"><?php esc_html_e( 'Email Address', 'abp-event-ticket' ); ?></h6>
                                <button class="_btn_light_success_xs_text_inherit" type="button"><?php echo esc_html( $email ); ?></button>
                            </div>
						<?php } ?>
					<?php } else { ?>
                        <div class="_color_warning"><span class="_mar_r_xxs  fas fa-exclamation-triangle"></span><?php echo esc_html( ABPET_Static::array_info( 'must_wc' ) ); ?></div>
					<?php } ?>
                </div>
				<?php
			}
			public function post_page(): void {
				$label = ABPET_Function::label();
				$total = sizeof( ABPET_ids );
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abp"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Booking Page', 'abp-event-ticket' ); ?></h6>
						<?php if ( ABPET_Function::get_page_by_slug( 'tf_booking' ) ) { ?>
                            <button class="_btn_light_success_xs" type="button"><span class="fas fa-check"></span><?php esc_html_e( 'Activated', 'abp-event-ticket' ); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_xs " onclick="abpet_create_page('tf_booking')" type="button"><span class="fas fa-plus"></span><?php esc_html_e( 'Add Event Ticket Booking Page', 'abp-event-ticket' ); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fa_center_fj_between">
                        <h6 class="_abp"><?php echo esc_html( $label ) . ' ' . esc_html__( 'Post List Page', 'abp-event-ticket' ); ?></h6>
						<?php if ( ABPET_Function::get_page_by_slug( 'tf_post' ) ) { ?>
                            <button class="_btn_light_success_xs" type="button"><span class="fas fa-check"></span><?php esc_html_e( 'Activated', 'abp-event-ticket' ); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_xs " onclick="abpet_create_page('tf_post')" type="button"><span class="fas fa-plus"></span><?php esc_html_e( 'Add Event List Page', 'abp-event-ticket' ); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fa_center_fj_between">
                        <h6 class="_abp"><?php esc_html_e( 'Gallery Page', 'abp-event-ticket' ); ?></h6>
						<?php if ( ABPET_Function::get_page_by_slug( 'tf_gallery' ) ) { ?>
                            <button class="_btn_light_success_xs" type="button"><span class="fas fa-check"></span><?php esc_html_e( 'Activated', 'abp-event-ticket' ); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_xs" onclick="abpet_create_page('tf_gallery')" type="button"><span class="fas fa-plus"></span><?php esc_html_e( 'Add Gallery Page', 'abp-event-ticket' ); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fa_center_fj_between">
                        <h6 class="_abp"> <?php esc_html_e( 'Number of Post', 'abp-event-ticket' ); ?> </h6>
						<?php if ( $total > 0 ) { ?>
                            <button class="_btn_light_success_xs" type="button"><span class="fas fa-check"></span><?php echo esc_html( $total ); ?></button>
						<?php } else { ?>
                            <button class="_btn_light_warning_xs" type="button"><span class="fas fa-exclamation-triangle"></span><?php esc_html_e( 'Can Not Find Post', 'abp-event-ticket' ); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fa_center_fj_between">
                        <h6 class="_abp"> <?php esc_html_e( 'Dummy Data', 'abp-event-ticket' ); ?> </h6>
                        <?php $dummy_count = $this->dummy_post_count(); ?>
                        <?php if ( $dummy_count > 0 ) { ?>
                            <div class="_fa_center_fj_between _abp_gap_xs">
                                <button class="_btn_warning_xs" onclick="abpet_import_global('dummy')" type="button"><span class="fas fa-plus"></span><?php esc_html_e( 'Add More Dummy Data', 'abp-event-ticket' ); ?></button>
                                <button class="_btn_light_danger_xs" onclick="abpet_import_global('remove_dummy')" type="button"><span class="fas fa-trash"></span><?php
                                /* translators: %d: number of dummy events to remove. */
                                printf( esc_html__( 'Remove Dummy Data (%d)', 'abp-event-ticket' ), esc_html( $dummy_count ) );
                                ?></button>
                            </div>
                        <?php } else { ?>
                            <button class="_btn_warning_xs" onclick="abpet_import_global('dummy')" type="button"><span class="fas fa-plus"></span><?php esc_html_e( 'Import Dummy Data', 'abp-event-ticket' ); ?></button>
                        <?php } ?>
                    </div>
                </div>
				<?php
			}
			//=============================//
			public function wc_config(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_val  = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$page_type = $post_val( 'type' );
				if ( $page_type == 'wc_install_active' ) {
					include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
					include_once( ABSPATH . 'wp-admin/includes/file.php' );
					include_once( ABSPATH . 'wp-admin/includes/misc.php' );
					include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
					$plugin = 'woocommerce';
					$api    = plugins_api( 'plugin_information', array(
						'slug'   => $plugin,
						'fields' => array(
							'short_description' => false,
							'sections'          => false,
							'requires'          => false,
							'rating'            => false,
							'ratings'           => false,
							'downloaded'        => false,
							'last_updated'      => false,
							'added'             => false,
							'tags'              => false,
							'compatibility'     => false,
							'homepage'          => false,
							'donate_link'       => false,
						),
					) );
					if ( is_wp_error( $api ) ) {
						wp_send_json_error( [ 'html' => '', 'msg' => $api->get_error_message() ] );
					}
					$title              = 'title';
					$url                = 'url';
					$nonce              = 'nonce';
					$woocommerce_plugin = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'title', 'url', 'nonce', 'plugin', 'api' ) ) );
					$installed          = $woocommerce_plugin->install( $api->download_link );
					if ( is_wp_error( $installed ) ) {
						wp_send_json_error( [ 'msg' => $installed->get_error_message(), 'type' => 'warn' ] );
					}
					$activated = activate_plugin( 'woocommerce/woocommerce.php' );
					if ( is_wp_error( $activated ) ) {
						wp_send_json_error( [ 'msg' => $activated->get_error_message(), 'type' => 'warn' ] );
					}
					wp_send_json_success( [ 'msg' => esc_html__( 'WooCommerce installed and activated successfully!', 'abp-event-ticket' ), 'type' => 'success' ], 200 );
				}
				if ( $page_type == 'wc_active' ) {
					if ( defined( 'ABPET_WC' ) && ABPET_WC == 1 ) {
						$activated = activate_plugin( 'woocommerce/woocommerce.php' );
						if ( is_wp_error( $activated ) ) {
							wp_send_json_error( [ 'msg' => $activated->get_error_message(), 'type' => 'warn' ] );
						}
						wp_send_json_success( [ 'msg' => esc_html__( 'WooCommerce activated successfully!', 'abp-event-ticket' ), 'type' => 'success' ], 200 );
					}
				}
				wp_send_json_error( [ 'msg' => esc_html__( 'WooCommerce is either not installed or already active.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
			}
			public function create_page(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_val  = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$page_type = $post_val( 'type' );
				if ( ! empty( $page_type ) ) {
					if ( ! ABPET_Function::get_page_by_slug( $page_type ) ) {
						$label      = ABPET_Function::label();
						$short_code = '';
						if ( $page_type == 'tf_booking' ) {
							$label      = __( 'Booking', 'abp-event-ticket' );
							$short_code = '[abpet-booking]';
						}
						if ( $page_type == 'tf_post' ) {
							$short_code = '[abpet-post]';
						}
						if ( $page_type == 'tf_gallery' ) {
							$label      = __( 'Gallery', 'abp-event-ticket' );
							$short_code = '[abpet-gallery]';
						}
						$page    = array(
							'post_type'    => 'page',
							'post_name'    => $page_type,
							'post_title'   => $label,
							'post_content' => $short_code,
							'post_status'  => 'publish',
						);
						$post_id = wp_insert_post( $page );
						if ( is_wp_error( $post_id ) || 0 === $post_id ) {
							wp_send_json_error( [ 'type' => 'warn', 'msg' => esc_html__( 'Failed to create page.', 'abp-event-ticket' ) ] );
						}
						flush_rewrite_rules();
						/* translators: %s: Trnasport Label */
						$translated_format = esc_html__( '%s Page Created successfully.....', 'abp-event-ticket' );
						$msg               = sprintf( $translated_format, $label );
						wp_send_json_success( [ 'type' => 'success', 'msg' => $msg ] );
					}
					wp_send_json_error( [ 'type' => 'warn', 'msg' => esc_html__( 'Page already exists.', 'abp-event-ticket' ) ] );
				} else {
					wp_send_json_error( [ 'type' => 'warn', 'msg' => esc_html__( 'Something Wrong...!', 'abp-event-ticket' ) ] );
				}
			}
			public function import_dummy(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$dummy_infos = $this->dummy_data();
				$previous_registry = ABPET_Function::get_option( 'abpet_dummy_registry', [] );
				$registry = [
					'posts' => array_map( 'absint', $previous_registry['posts'] ?? [] ),
					'terms' => is_array( $previous_registry['terms'] ?? null ) ? $previous_registry['terms'] : [],
				];
				if ( isset( $dummy_infos['taxonomy'] ) ) {
					foreach ( $dummy_infos['taxonomy'] as $tax => $taxonomy_option ) {
						if ( taxonomy_exists( $tax ) ) {
							foreach ( $taxonomy_option as $taxonomy_data ) {
								$name = sanitize_text_field( $taxonomy_data['name'] ?? '' );
								if ( empty( $name ) ) {
									continue;
								}
								$existing = get_term_by( 'name', $name, $tax );
								$term_id  = $existing ? (int) $existing->term_id : 0;
								if ( ! $term_id ) {
									$term = wp_insert_term( $name, $tax );
									if ( ! is_wp_error( $term ) ) {
										$term_id = (int) $term['term_id'];
										$registry['terms'][] = [ 'taxonomy' => $tax, 'term_id' => $term_id ];
									}
								}
							}
						}
					}
					do_action( 'abpet_location_update' );
					do_action( 'abpet_category_update' );
					do_action( 'abpet_organizer_update' );
					do_action( 'abpet_brand_update' );
				}
				if ( isset( $dummy_infos['options'] ) ) {
					foreach ( $dummy_infos['options'] as $option => $dummy_option ) {
						$option_data = get_option( $option );
						if ( empty( $option_data ) ) {
							update_option( $option, $dummy_option );
						}
					}
				}
				if ( isset( $dummy_infos['custom_post'] ) ) {
					$dummy_posts = $this->dummy();
					foreach ( $dummy_posts as $dummy_data ) {
						$args = array();
						if ( isset( $dummy_data['name'] ) ) {
							$args['post_title'] = $dummy_data['name'];
						}
						$args['post_status'] = 'publish';
						$args['post_type']   = ABPET_Function::get_cpt();
						$post_id             = wp_insert_post( $args );
						if ( is_wp_error( $post_id ) || ! $post_id ) {
							continue;
						}
						$post_data           = $dummy_data['post_data'] ?? [];
						if ( ! empty( $post_data ) ) {
							foreach ( $post_data as $meta_key => $data ) {
								update_post_meta( $post_id, $meta_key, $data );
							}
						}
						$this->sync_event_taxonomies( $post_id, $post_data );
						$registry['posts'][] = (int) $post_id;
					}
				}
				update_option( 'abpet_dummy_registry', $registry, false );
				flush_rewrite_rules();
				wp_send_json_success( [
					'msg' => esc_html__( 'Dummy data imported successfully!', 'abp-event-ticket' )
				] );
			}
			public function remove_dummy(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_ids = get_posts( [
					'post_type'      => ABPET_Function::get_cpt(),
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Fixed plugin dummy-data flag lookup.
					'meta_key'       => 'dummy',
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Fixed plugin dummy-data flag lookup.
					'meta_value'     => 'on',
				] );
				foreach ( $post_ids as $post_id ) {
					wp_delete_post( (int) $post_id, true );
				}
				$registry = ABPET_Function::get_option( 'abpet_dummy_registry', [] );
				foreach ( ( $registry['terms'] ?? [] ) as $term_data ) {
					$taxonomy = sanitize_key( $term_data['taxonomy'] ?? '' );
					$term_id  = absint( $term_data['term_id'] ?? 0 );
					if ( $taxonomy && $term_id && taxonomy_exists( $taxonomy ) ) {
						$term = get_term( $term_id, $taxonomy );
						if ( $term && ! is_wp_error( $term ) && 0 === (int) $term->count ) {
							wp_delete_term( $term_id, $taxonomy );
						}
					}
				}
				delete_option( 'abpet_dummy_registry' );
				flush_rewrite_rules();
				wp_send_json_success( [ 'msg' => esc_html__( 'Dummy data removed successfully.', 'abp-event-ticket' ), 'type' => 'success' ] );
			}
			private function dummy_post_count(): int {
				return count( get_posts( [
					'post_type'      => ABPET_Function::get_cpt(),
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Fixed plugin dummy-data flag lookup.
					'meta_key'       => 'dummy',
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Fixed plugin dummy-data flag lookup.
					'meta_value'     => 'on',
				] ) );
			}
			private function sync_event_taxonomies( int $post_id, array $post_data ): void {
				foreach ( [
					'abpet_category'  => 'abpet_category',
					'abpet_location'  => 'abpet_location',
					'abpet_organizer' => 'abpet_organizer',
					'abpet_brand'     => 'abpet_brand',
				] as $meta_key => $taxonomy ) {
					if ( ! taxonomy_exists( $taxonomy ) ) {
						continue;
					}
					$value = $post_data[ $meta_key ] ?? '';
					$ids   = is_array( $value ) ? $value : explode( ',', (string) $value );
					$ids   = array_values( array_filter( array_map( 'absint', $ids ) ) );
					wp_set_object_terms( $post_id, $ids, $taxonomy, false );
				}
			}
			public function dummy_data(): array {
				return [
					'taxonomy'    => [
						'abpet_location'  => ABPET_Static::location(),
						'abpet_category'  => ABPET_Static::category(),
						'abpet_organizer' => ABPET_Static::organizer(),
						'abpet_brand'     => ABPET_Static::brand(),
					],
					'options'     => [
						'abpet_ticket'     => ABPET_Static::ticket(),
						'abpet_decor'      => ABPET_Static::decoration(),
						'abpet_additional' => ABPET_Static::additional(),
						'abpet_form'       => ABPET_Static::form(),
						'abpet_faq'        => ABPET_Static::faq(),
						'abpet_tc'         => ABPET_Static::tc(),
						'abpet_feature'    => ABPET_Static::feature(),
					],
					'custom_post' => []
				];
			}
			public function sp(): void {
				$sp_data = ABPET_Query::get_sp();
				if ( empty( $sp_data ) ) {
					global $wpdb;
					$table_name      = $wpdb->prefix . 'abpet_sp';
					$bus_plan_data_1 = [
						'name'        => uniqid( 'sp_' ),
						'total_seats' => 41,
						'others'      => '{"bg_image":"0","bg_color":"#fff","row":11,"column":5,"width":60,"height":60,"gap":5,"radius":5}',
						'layout_data' => '[{"index":"0","type":"other","id":"1","name":"Entrance","width_ratio":"3","fs":"15"},{"index":"1","type":"other","id":"1","name":""},{"index":"2","type":"other","id":"1","name":""},{"index":"3","type":"other","id":"2","name":"Driver","width_ratio":"2","fs":"14"},{"index":"4","type":"other","id":"1","name":""},{"index":"5","type":"seat","id":"3","name":"B-1"},{"index":"6","type":"seat","id":"3","name":"B-2"},{"index":"7","type":"other","id":"1","name":"Passenger Aisle","height_ratio":"9","rotate":"90","fs":"20"},{"index":"8","type":"seat","id":"3","name":"B-3"},{"index":"9","type":"seat","id":"3","name":"B-4"},{"index":"10","type":"seat","id":"5","name":"C-1"},{"index":"11","type":"seat","id":"5","name":"C-2"},{"index":"12","type":"other","id":"1","name":""},{"index":"13","type":"seat","id":"5","name":"C-3"},{"index":"14","type":"seat","id":"5","name":"C-4"},{"index":"15","type":"seat","id":"6","name":"F-1"},{"index":"16","type":"seat","id":"6","name":"F-2"},{"index":"17","type":"other","id":"1","name":""},{"index":"18","type":"seat","id":"6","name":"F-3"},{"index":"19","type":"seat","id":"6","name":"F-4"},{"index":"20","type":"seat","id":"7","name":"AD-1"},{"index":"21","type":"seat","id":"7","name":"AD-2"},{"index":"22","type":"other","id":"1","name":""},{"index":"23","type":"seat","id":"7","name":"AD-3"},{"index":"24","type":"seat","id":"7","name":"AD-4"},{"index":"25","type":"seat","id":"8","name":"CH-1"},{"index":"26","type":"seat","id":"8","name":"CH-2"},{"index":"27","type":"other","id":"1","name":""},{"index":"28","type":"seat","id":"8","name":"CH-3"},{"index":"29","type":"seat","id":"8","name":"CH-4"},{"index":"30","type":"seat","id":"2","name":"VIP-1"},{"index":"31","type":"seat","id":"2","name":"VIP-2"},{"index":"32","type":"other","id":"1","name":""},{"index":"33","type":"seat","id":"2","name":"VIP-3"},{"index":"34","type":"seat","id":"2","name":"VIP-4"},{"index":"35","type":"seat","id":"4","name":"S-1"},{"index":"36","type":"seat","id":"4","name":"S-2"},{"index":"37","type":"other","id":"1","name":""},{"index":"38","type":"seat","id":"4","name":"S-3"},{"index":"39","type":"seat","id":"4","name":"S-4"},{"index":"40","type":"seat","id":"4","name":"S-5"},{"index":"41","type":"seat","id":"4","name":"S-6"},{"index":"42","type":"other","id":"1","name":""},{"index":"43","type":"seat","id":"4","name":"S-7"},{"index":"44","type":"seat","id":"4","name":"S-8"},{"index":"45","type":"seat","id":"3","name":"B-5"},{"index":"46","type":"seat","id":"3","name":"B-6"},{"index":"47","type":"other","id":"1","name":""},{"index":"48","type":"seat","id":"3","name":"B-7"},{"index":"49","type":"seat","id":"3","name":"B-8"},{"index":"50","type":"seat","id":"9","name":"E-1"},{"index":"51","type":"seat","id":"9","name":"E-2"},{"index":"52","type":"seat","id":"9","name":"E-3"},{"index":"53","type":"seat","id":"9","name":"E-4"},{"index":"54","type":"seat","id":"9","name":"E-5"}]',
						'seat_info'   => '{"2":"4","3":"8","4":"8","5":"4","6":"4","7":"4","8":"4","9":"5"}'
					];
					$bus_plan_data_2 = [
						'name'        => uniqid( 'sp_' ),
						'total_seats' => 40,
						'others'      => '{"bg_image":"0","bg_color":"#fff","row":11,"column":5,"width":60,"height":60,"gap":5,"radius":5}',
						'layout_data' => '[{"index":"0","type":"other","id":"1","name":"Entance","width_ratio":"3","fs":"15"},{"index":"1","type":"other","id":"1","name":""},{"index":"2","type":"other","id":"1","name":""},{"index":"3","type":"other","id":"2","name":"","width_ratio":"2"},{"index":"4","type":"other","id":"1","name":""},{"index":"5","type":"seat","id":"3","name":"A-1"},{"index":"6","type":"seat","id":"3","name":"A-2"},{"index":"7","type":"other","id":"1","name":"Passenger Walkway","height_ratio":"10","rotate":"90","fs":"18"},{"index":"8","type":"seat","id":"3","name":"A-3"},{"index":"9","type":"seat","id":"3","name":"A-4"},{"index":"10","type":"seat","id":"3","name":"B-1"},{"index":"11","type":"seat","id":"3","name":"B-2"},{"index":"12","type":"other","id":"1","name":""},{"index":"13","type":"seat","id":"3","name":"B-3"},{"index":"14","type":"seat","id":"3","name":"B-4"},{"index":"15","type":"seat","id":"3","name":"C-1"},{"index":"16","type":"seat","id":"3","name":"C-2"},{"index":"17","type":"other","id":"1","name":""},{"index":"18","type":"seat","id":"3","name":"C-3"},{"index":"19","type":"seat","id":"3","name":"C-4"},{"index":"20","type":"seat","id":"3","name":"D-1"},{"index":"21","type":"seat","id":"3","name":"D-2"},{"index":"22","type":"other","id":"1","name":""},{"index":"23","type":"seat","id":"3","name":"D-3"},{"index":"24","type":"seat","id":"3","name":"D-4"},{"index":"25","type":"seat","id":"3","name":"E-1"},{"index":"26","type":"seat","id":"3","name":"E-2"},{"index":"27","type":"other","id":"1","name":""},{"index":"28","type":"seat","id":"3","name":"E-3"},{"index":"29","type":"seat","id":"3","name":"E-4"},{"index":"30","type":"seat","id":"3","name":"F-1"},{"index":"31","type":"seat","id":"3","name":"F-2"},{"index":"32","type":"other","id":"1","name":""},{"index":"33","type":"seat","id":"3","name":"F-3"},{"index":"34","type":"seat","id":"3","name":"F-4"},{"index":"35","type":"seat","id":"3","name":"G-1"},{"index":"36","type":"seat","id":"3","name":"G-2"},{"index":"37","type":"other","id":"1","name":""},{"index":"38","type":"seat","id":"3","name":"G-3"},{"index":"39","type":"seat","id":"3","name":"G-4"},{"index":"40","type":"seat","id":"3","name":"H-1"},{"index":"41","type":"seat","id":"3","name":"H-2"},{"index":"42","type":"other","id":"1","name":""},{"index":"43","type":"seat","id":"3","name":"H-3"},{"index":"44","type":"seat","id":"3","name":"H-4"},{"index":"45","type":"seat","id":"3","name":"I-1"},{"index":"46","type":"seat","id":"3","name":"I-2"},{"index":"47","type":"other","id":"1","name":""},{"index":"48","type":"seat","id":"3","name":"I-3"},{"index":"49","type":"seat","id":"3","name":"I-4"},{"index":"50","type":"seat","id":"3","name":"J-1"},{"index":"51","type":"seat","id":"3","name":"J-2"},{"index":"52","type":"other","id":"1","name":""},{"index":"53","type":"seat","id":"3","name":"J-3"},{"index":"54","type":"seat","id":"3","name":"J-4"}]',
						'seat_info'   => '{"3":"40"}'
					];
					$bus_plan_data_3 = [
						'name'        => uniqid( 'sp_' ),
						'total_seats' => 30,
						'others'      => '{"bg_image":"","bg_color":"#fff","row":11,"column":4,"width":60,"height":60,"gap":5,"radius":5}',
						'layout_data' => '[{"index":"0","type":"other","id":"1","name":"Entrance","width_ratio":"2","fs":"16"},{"index":"1","type":"other","id":"1","name":""},{"index":"2","type":"other","id":"2","name":"","width_ratio":"2"},{"index":"3","type":"other","id":"1","name":""},{"index":"4","type":"seat","id":"1","name":"A-1"},{"index":"5","type":"other","id":"1","name":"Passenger Access Path","height_ratio":"10","rotate":"90","fs":"16"},{"index":"6","type":"seat","id":"1","name":"A-2"},{"index":"7","type":"seat","id":"1","name":"A-3"},{"index":"8","type":"seat","id":"1","name":"B-1"},{"index":"9","type":"other","id":"1","name":""},{"index":"10","type":"seat","id":"1","name":"B-2"},{"index":"11","type":"seat","id":"1","name":"B-3"},{"index":"12","type":"seat","id":"1","name":"C-1"},{"index":"13","type":"other","id":"1","name":""},{"index":"14","type":"seat","id":"1","name":"C-2"},{"index":"15","type":"seat","id":"1","name":"C-3"},{"index":"16","type":"seat","id":"1","name":"D-1"},{"index":"17","type":"other","id":"1","name":""},{"index":"18","type":"seat","id":"1","name":"D-2"},{"index":"19","type":"seat","id":"1","name":"D-3"},{"index":"20","type":"seat","id":"1","name":"E-1"},{"index":"21","type":"other","id":"1","name":""},{"index":"22","type":"seat","id":"1","name":"E-2"},{"index":"23","type":"seat","id":"1","name":"E-3"},{"index":"24","type":"seat","id":"1","name":"F-1"},{"index":"25","type":"other","id":"1","name":""},{"index":"26","type":"seat","id":"1","name":"F-2"},{"index":"27","type":"seat","id":"1","name":"F-3"},{"index":"28","type":"seat","id":"1","name":"G-1"},{"index":"29","type":"other","id":"1","name":""},{"index":"30","type":"seat","id":"1","name":"G-2"},{"index":"31","type":"seat","id":"1","name":"G-3"},{"index":"32","type":"seat","id":"1","name":"H-1"},{"index":"33","type":"other","id":"1","name":""},{"index":"34","type":"seat","id":"1","name":"H-2"},{"index":"35","type":"seat","id":"1","name":"H-3"},{"index":"36","type":"seat","id":"1","name":"I-1"},{"index":"37","type":"other","id":"1","name":""},{"index":"38","type":"seat","id":"1","name":"I-2"},{"index":"39","type":"seat","id":"1","name":"I-3"},{"index":"40","type":"seat","id":"1","name":"J-1"},{"index":"41","type":"other","id":"1","name":""},{"index":"42","type":"seat","id":"1","name":"J-2"},{"index":"43","type":"seat","id":"1","name":"J-3"}]',
						'seat_info'   => '{"1":"30"}'
					];
					$bus_plan_data_4 = [
						'name'        => uniqid( 'sp_' ),
						'total_seats' => 15,
						'others'      => '{"bg_image":"","bg_color":"#fff","row":11,"column":4,"width":60,"height":60,"gap":5,"radius":5}',
						'layout_data' => '[{"index":"0","type":"other","id":"1","name":"Entance","width_ratio":"2","fs":"16"},{"index":"1","type":"other","id":"1","name":""},{"index":"2","type":"other","id":"2","name":"Driver","width_ratio":"2","fs":"16"},{"index":"3","type":"other","id":"1","name":""},{"index":"4","type":"seat","id":"4","name":"S-1","height_ratio":"2","fs":"14"},{"index":"5","type":"other","id":"1","name":"Passenger Way","height_ratio":"10","rotate":"90","fs":"18"},{"index":"6","type":"seat","id":"4","name":"S-2","height_ratio":"2","fs":"14"},{"index":"7","type":"seat","id":"4","name":"S-3","height_ratio":"2","fs":"14"},{"index":"8","type":"other","id":"1","name":""},{"index":"9","type":"other","id":"1","name":""},{"index":"10","type":"other","id":"1","name":""},{"index":"11","type":"other","id":"1","name":""},{"index":"12","type":"seat","id":"4","name":"S-4","height_ratio":"2","fs":"14"},{"index":"13","type":"other","id":"1","name":""},{"index":"14","type":"seat","id":"4","name":"S-5","height_ratio":"2","fs":"14"},{"index":"15","type":"seat","id":"4","name":"S-6","height_ratio":"2","fs":"14"},{"index":"16","type":"other","id":"1","name":""},{"index":"17","type":"other","id":"1","name":""},{"index":"18","type":"other","id":"1","name":""},{"index":"19","type":"other","id":"1","name":""},{"index":"20","type":"seat","id":"4","name":"S-7","height_ratio":"2","fs":"14"},{"index":"21","type":"other","id":"1","name":""},{"index":"22","type":"seat","id":"4","name":"S-8","height_ratio":"2","fs":"14"},{"index":"23","type":"seat","id":"4","name":"S-9","height_ratio":"2","fs":"14"},{"index":"24","type":"other","id":"1","name":""},{"index":"25","type":"other","id":"1","name":""},{"index":"26","type":"other","id":"1","name":""},{"index":"27","type":"other","id":"1","name":""},{"index":"28","type":"seat","id":"4","name":"S-10","height_ratio":"2","fs":"14"},{"index":"29","type":"other","id":"1","name":""},{"index":"30","type":"seat","id":"4","name":"S-11","height_ratio":"2","fs":"14"},{"index":"31","type":"seat","id":"4","name":"S-12","height_ratio":"2","fs":"14"},{"index":"32","type":"other","id":"1","name":""},{"index":"33","type":"other","id":"1","name":""},{"index":"34","type":"other","id":"1","name":""},{"index":"35","type":"other","id":"1","name":""},{"index":"36","type":"seat","id":"4","name":"S-13","height_ratio":"2","fs":"14"},{"index":"37","type":"other","id":"1","name":""},{"index":"38","type":"seat","id":"4","name":"S-14","height_ratio":"2","fs":"14"},{"index":"39","type":"seat","id":"4","name":"S-15","height_ratio":"2","fs":"14"},{"index":"40","type":"other","id":"1","name":""},{"index":"41","type":"other","id":"1","name":""},{"index":"42","type":"other","id":"1","name":""},{"index":"43","type":"other","id":"1","name":""}]',
						'seat_info'   => '{"4":"15"}'
					];
					$ticket_infos    = ABPET_Function::get_option( 'abpet_ticket_sp' );
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->insert( $table_name, $bus_plan_data_1 );
					$id_1                           = $wpdb->insert_id;
					$ticket_infos[ $id_1 ]['type']  = json_decode( $bus_plan_data_1['seat_info'], true );
					$ticket_infos[ $id_1 ]['total'] = 41;
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->insert( $table_name, $bus_plan_data_2 );
					$id_2                           = $wpdb->insert_id;
					$ticket_infos[ $id_2 ]['type']  = json_decode( $bus_plan_data_2['seat_info'], true );
					$ticket_infos[ $id_2 ]['total'] = 40;
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->insert( $table_name, $bus_plan_data_3 );
					$id_3                           = $wpdb->insert_id;
					$ticket_infos[ $id_3 ]['type']  = json_decode( $bus_plan_data_3['seat_info'], true );
					$ticket_infos[ $id_3 ]['total'] = 30;
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->insert( $table_name, $bus_plan_data_4 );
					$id_4                           = $wpdb->insert_id;
					$ticket_infos[ $id_4 ]['type']  = json_decode( $bus_plan_data_4['seat_info'], true );
					$ticket_infos[ $id_4 ]['total'] = 15;
					update_option( 'abpet_ticket_sp', $ticket_infos );
				}
			}
			public function dummy( $count = 5 ): array {
				$on_off         = [ 'on', 'off' ];
				$event_type         = [  'online', 'offline' ];
				$template       = [ "default", "light" ];
				$icon           = [ "🎟️", "🎫", "🎪", "🎭", "🎤", "🎬", "🏟️", "🎉", "📅", "💺", "fas fa-ticket", "fas fa-ticket-simple", "fas fa-calendar-days", "fas fa-masks-theater", "fas fa-microphone", "fas fa-music", "fas fa-trophy", "fas fa-champagne-glasses", "fas fa-users", "fas fa-star", ];
				$all_organizer  = ABPET_Function::get_option( 'abpet_organizer' );
				$organizer      = [ 'Global Events Group', 'EventPro Productions', 'Premier Events Network', 'Elite Event Management', 'NextGen Events', 'United Event Solutions', ];
				$all_brands     = ABPET_Function::get_option( 'abpet_brand' );
				$brand          = [ 'Live Nation', 'Eventbrite', 'AEG Presents', 'Ticketmaster', 'IMG Events', 'C3 Presents', 'Global Events', 'Premier Events', 'EventPro', 'Elite Entertainment', ];
				$all_categories = ABPET_Function::get_option( 'abpet_category' );
				$categories     = [ 'Concert', 'Conference', 'Workshop', 'Seminar', 'Festival', 'Sports', 'Theater', 'Exhibition', 'Party', 'Other', ];
				$features       = ABPET_Function::get_option( 'abpet_feature' );
				$names             = [ 'Summer Music Festival 2026', 'Global Business & Technology Conference', 'International Food & Culture Festival', 'Future Innovation & Startup Summit', 'Live Concert Night 2026', 'Creative Arts & Design Exhibition', 'World Sports & Fitness Expo', 'Professional Leadership Conference', 'Digital Marketing & Growth Summit', 'International Film & Entertainment Festival', ];
				$subtitles         = [
					'Experience an unforgettable celebration of music, entertainment, and live performances.',
					'Connect with industry leaders and explore the future of business and technology.',
					'Discover delicious flavors, traditions, and cultures from around the world.',
					'Meet innovators, entrepreneurs, and visionaries shaping the future.',
					'Enjoy an exciting evening of live music, entertainment, and unforgettable moments.',
					'Explore inspiring artwork, creative ideas, and modern design from talented artists.',
					'Discover the latest trends in sports, fitness, health, and active living.',
					'Learn from experienced professionals and develop the skills to lead with confidence.',
					'Explore powerful digital strategies, emerging trends, and proven growth techniques.',
					'Celebrate the best of cinema, entertainment, storytelling, and creative filmmaking.',
				];
				$post_descriptions = [
					'Join us for an exciting summer celebration featuring live music, talented performers, interactive activities, and a vibrant atmosphere. Gather your friends and family for a memorable day filled with entertainment and fun.',
					'Bring together professionals, entrepreneurs, and technology enthusiasts for an inspiring conference focused on business growth, innovation, emerging technologies, and industry trends. Connect, learn, and discover new opportunities.',
					'Experience a colorful celebration of global cuisine and culture featuring authentic food, live performances, cultural showcases, and family-friendly activities. Discover new traditions and enjoy flavors from around the world.',
					'Explore the ideas and technologies shaping tomorrow at this exciting innovation and startup summit. Meet ambitious founders, investors, industry experts, and creative thinkers while discovering new opportunities for growth and collaboration.',
					'Get ready for an unforgettable night of live music and entertainment featuring exciting performances, talented artists, and an energetic atmosphere. Book your tickets and enjoy a night to remember.',
					'Discover inspiring artwork, creative concepts, and innovative designs from emerging and established artists. This exhibition brings together creativity, imagination, and modern design in one inspiring experience.',
					'Explore the latest developments in sports, fitness, wellness, and active living. Meet industry professionals, discover new products, join exciting activities, and get inspired to live a healthier lifestyle.',
					'Gain valuable insights from experienced leaders and professionals at this leadership-focused conference. Learn practical strategies, exchange ideas, build meaningful connections, and develop the skills needed for professional success.',
					'Discover the latest digital marketing strategies, technologies, and growth opportunities. Learn from industry experts, explore emerging trends, and gain practical insights to help businesses grow in the digital world.',
					'Celebrate the art of filmmaking and entertainment with a diverse selection of films, creative showcases, industry discussions, and special presentations. Experience inspiring stories and discover new voices from the world of cinema.',
				];
				$all_data          = [];
				$ticket_infos   = $this->ticket_info( $count );
				$date_infos    = $this->date_info( $count );

				for ( $i = 0; $i < $count; $i ++ ) {
					$rand_key = isset( $names[$i] ) ? $i : array_rand( $names );
					$all_data[ $i ]['name']      = $names[ $rand_key ];
					$all_data[ $i ]['post_data'] = [
						'sale_continue'               => 'on',
						'abpet_template'              => $template[ wp_rand( 0, 1 ) ],
						'display_sku'                 => 'on',
						'post_sku'                    => wp_rand( 100, 999 ),
						'post_icon'                   => $icon[ $rand_key ],
						'event_type'                  => $event_type[ wp_rand( 0, 1 ) ],
						'sub_title'                   => $subtitles[ $rand_key ],
						'post_description'            => $post_descriptions[ $rand_key ],
						'display_organizer'           => $on_off[ wp_rand( 0, 1 ) ],
						'abpet_organizer'             => $this->get_id( $all_organizer, $organizer[ array_rand( $organizer ) ] ),
						'display_brand'               => $on_off[ wp_rand( 0, 1 ) ],
						'abpet_brand'                 => $this->get_id( $all_brands, $brand[ array_rand( $brand ) ] ),
						'display_capacity'            => $on_off[ wp_rand( 0, 1 ) ],
						'display_category'            => $on_off[ wp_rand( 0, 1 ) ],
						'abpet_category'              => $this->get_id( $all_categories, $categories[ array_rand( $categories ) ] ),
						'post_feature'                => implode( ',', array_rand( $features, 5 ) ),
						'abpet_slider'                => '10,20,30,40,50,100,60,70,80,90',
						'abpet_dates'                 => $date_infos[ $i ]??[],
						'display_additional_services' => 'on',
						'active_global_additional'    => 'on',
						'display_client_form'         => 'on',
						'active_global_form'          => 'on',
						'display_single_form'         => $on_off[ wp_rand( 0, 1 ) ],
						'display_faq'                 => 'on',
						'active_global_faq'           => 'on',
						'display_tc'                  => 'on',
						'active_global_tc'            => 'on',
						'dummy'                       => 'on',
						'seat_type'                   => $ticket_infos[ $i ]['seat_type']??'ticket',
						'display_ticket_type'         => 'on',
						'min_qty'                     => wp_rand( 1, 2 ),
						'max_qty'                     => wp_rand( 3, 10 ),
						'ticket_infos'                => $ticket_infos[ $i ]['ticket_infos']??[],
						'sp_infos'                    => $ticket_infos[ $i ]['sp_infos']??[],
						'all_ticket_type'             => $ticket_infos[ $i ]['all_ticket_type']??[],
					];
				}
				return $all_data;
			}
			public function ticket_info( $count ): array {
				ABPET_Static::sp();
				$ticket_options  = ABPET_Function::get_option( 'abpet_ticket' );
				$random_num      = sizeof( $ticket_options ) > 4 ? 3 : sizeof( $ticket_options );
				$all_ticket_type = array_rand( $ticket_options, $random_num );
				$all_sp_ticket   = ABPET_Function::get_option( 'abpet_ticket_sp' );
				$sp_id           = [];
				if ( ! empty( $all_sp_ticket ) ) {
					$sp_id = array_keys( $all_sp_ticket );
				}
				$all_data          = [];
				$all_data['sp_id'] = $sp_id[ array_rand( $sp_id ) ];
				if ( ! empty( $count ) && $count > 0 ) {
					for ( $key = 0; $key < $count; $key ++ ) {
						$seat_type = array( 'ticket', 'sp' )[ wp_rand( 0, 1 ) ];
						if ( $seat_type == 'sp' ) {
							$sp_select  = $sp_id[ array_rand( $sp_id ) ];
							$tickets    = [];
							$seat_infos = $all_sp_ticket[ $sp_select ] ?? [];
							if ( ! empty( $seat_infos ) ) {
								$seat_info = $seat_infos['type'] ?? [];
								if ( ! empty( $seat_info ) ) {
									$tickets = array_merge( $tickets, array_keys( $seat_info ) );
								}
							}
							$all_ticket_type                       = array_values( array_unique( $tickets ) );
							$all_data[ $key ]['sp_infos'][0]['id'] = $sp_select;
						}
						foreach ( $all_ticket_type as $type_id ) {
							$all_data[ $key ]['ticket_infos'][ $type_id ]['price']   = wp_rand( 30, 80 );
							$all_data[ $key ]['ticket_infos'][ $type_id ]['qty']     = wp_rand( 30, 60 );
							$all_data[ $key ]['ticket_infos'][ $type_id ]['reserve'] = wp_rand( 5, 10 );
							$all_data[ $key ]['ticket_infos'][ $type_id ]['min_qty'] = wp_rand( 1, 2 );
							$all_data[ $key ]['ticket_infos'][ $type_id ]['max_qty'] = wp_rand( 2, 5 );
						}
						$all_data[ $key ]['all_ticket_type'] = $all_ticket_type;
						$all_data[ $key ]['seat_type'] = $seat_type;
					}
				}
				return $all_data;
			}
			public function date_info( $count ): array {
				$date_infos = [];
				if ( ! empty( $count ) && $count > 0 ) {
					$times = [
						0 => [ 'label' => 'Morning', 'value' => '09:15' ],
						1 => [ 'label' => 'Late Morning', 'value' => '11:30' ],
						2 => [ 'label' => 'Afternoon', 'value' => '14:00' ],
						3 => [ 'label' => 'Evening', 'value' => '18:45' ],
						4 => [ 'label' => 'Night', 'value' => '21:10' ],
						5 => [ 'label' => 'Morning', 'value' => '08:15' ],
						6 => [ 'label' => 'Late Morning', 'value' => '10:30' ],
						7 => [ 'label' => 'Noon', 'value' => '12:00' ],
						8 => [ 'label' => 'Afternoon', 'value' => '15:45' ],
						9 => [ 'label' => 'Night', 'value' => '20:10' ],
					];
					for ( $key = 0; $key < $count; $key ++ ) {
						$rand_num = wp_rand( 1, 10 );
						$date_type                       = wp_rand( 'periodic_date', 'periodic_date' );
						$date_infos[ $key ]['date_type'] = $date_type;
						if ( $date_type == 'periodic_date' ) {
							$date_infos[ $key ]['periodic_start_date'] = gmdate( 'Y-m-d', strtotime( '+' . $rand_num . ' days', time() ) );
							$date_infos[ $key ]['periodic_after']      = wp_rand( 1, 4 );
						} else {
							for ( $i = 0; $i < $rand_num; $i ++ ) {
								$rand_num_                                  = wp_rand( 1, 60 );
								$date_infos[ $key ]['specific_dates'][ $i ] = gmdate( 'Y-m-d', strtotime( '+' . ( $rand_num + $rand_num_ ) . ' days', time() ) );
							}
						}
						$date_infos[ $key ]['time_infos']['time'] = array_intersect_key( $times, array_flip( array_rand( $times, 3 ) ) );
					}
				}
				return $date_infos;
			}
			public function get_id( $options = [], $name = '' ): int|string|null {
				if ( ! empty( $options ) ) {
					foreach ( $options as $key => $option ) {
						if ( isset( $option['name'] ) && $option['name'] === $name ) {
							return $key;
						}
					}
				}
				return null;
			}
		}
		new ABPET_Status();
	}