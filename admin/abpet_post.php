<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Post' ) ) {
		class ABPET_Post {
			public function __construct() {
				add_action( 'abpet_load_posts', array( $this, 'load_posts' ) );
				add_action( 'add_meta_boxes', [ $this, 'settings_meta' ] );
				add_action( 'save_post', array( $this, 'save_settings' ) );
				add_action( 'wp_ajax_abpet_post_permanent_remove', array( $this, 'post_permanent_remove' ) );
				add_action( 'wp_ajax_abpet_post_move_trash', array( $this, 'post_move_trash' ) );
				add_action( 'wp_ajax_abpet_post_restore', array( $this, 'post_restore' ) );
				add_action( 'wp_ajax_abpet_reload_post_list', array( $this, 'reload_post_list' ) );
			}
			public function load_posts( $abpet_info = [] ): void {
				$total_posts   = $abpet_info['total_post'] ?? 0;
				$total_publish = $abpet_info['total_publish'] ?? 0;
				$total_draft   = $abpet_info['total_draft'] ?? 0;
				$total_private = $abpet_info['total_private'] ?? 0;
				$total_trash   = $abpet_info['total_trash'] ?? 0;
				$status        = 'publish';
				if ( isset( $_GET['_abpet_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abpet_nonce'] ) ), 'abpet_url_action' ) ) {
					$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : 'publish';
				}
				$status                = $status ?? 'publish';
				$filter_args['status'] = $status;
				?>
                <div class="abpet_posts _section_card">
                    <div class="_fj_between_f_wrap">
                        <div class="_group_content">
                            <input type="hidden" name="select_hidden_post_status" value="<?php echo esc_attr( $status ); ?>"/>
                            <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $status == 'all' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPET_Function::build_url( 'posts', [ 'status' => 'all' ] ) ); ?>"><?php esc_html_e( 'All', 'abp-event-ticket' ); ?> ( <?php echo esc_html( $total_posts ); ?> )</button>
                            <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $status == 'publish' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPET_Function::build_url( 'posts', [ 'status' => 'publish' ] ) ); ?>"><?php esc_html_e( 'Published', 'abp-event-ticket' ); ?> ( <?php echo esc_html( $total_publish ); ?> )</button>
                            <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $status == 'private' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPET_Function::build_url( 'posts', [ 'status' => 'private' ] ) ); ?>"><?php esc_html_e( 'Private', 'abp-event-ticket' ); ?> ( <?php echo esc_html( $total_private ); ?> )</button>
                            <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $status == 'draft' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPET_Function::build_url( 'posts', [ 'status' => 'draft' ] ) ); ?>"><?php esc_html_e( 'Draft', 'abp-event-ticket' ); ?> ( <?php echo esc_html( $total_draft ); ?> )</button>
                            <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $status == 'trash' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPET_Function::build_url( 'posts', [ 'status' => 'trash' ] ) ); ?>"><?php esc_html_e( 'Trash', 'abp-event-ticket' ); ?> ( <?php echo esc_html( $total_trash ); ?> )</button>
                        </div>
                        <a class="_btn_navy_blue_xs" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . ABPET_Function::get_cpt() ) ); ?>">
							<?php ABPET_Static::icon_svg( 'plus' );
								esc_html_e( 'Add New Event', 'abp-event-ticket' ); ?>
                        </a>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class=" post_list">
						<?php $this->post_table( $filter_args ); ?>
                    </div>
                </div>
				<?php
			}
			public function settings_meta(): void {
				$label      = ABPET_Function::label();
				$brand_icon = ABPET_Function::icon();
				$label      = $label . ' ' . __( 'Configuration', 'abp-event-ticket' ) . get_the_title( get_the_id() );
				add_meta_box( 'abpet_configuration', '<span class="' . esc_attr( $brand_icon ?: '' ) . '"></span>' . esc_html( $label ), array( $this, 'settings' ), esc_attr( ABPET_Function::get_cpt() ), 'normal', 'high' );
			}
			//=============================//
			public function post_table( $filter_args ): void {
				//echo '<pre>';print_r($filter_args);echo '</pre>';
				$status = $filter_args['status'] ?? '';
				if ( empty( $status ) || $status == 'all' ) {
					$status = [ 'publish', 'draft', 'private', 'trash' ];
				}
				$page_number               = absint( $filter_args['page_number'] ?? 1 ) ?: 1;
				$limit                     = absint( ( $filter_args['page_item'] ?? 0 ) ?: ABPET_Function::get_option( 'abpet_per_page_item', 20 ) );
				$count                     = ( $page_number - 1 ) * $limit + 1;
				$cpt                       = ABPET_Function::get_cpt();
				$filters['status']         = $status;
				$filters['posts_per_page'] = $limit;
				$filters['paged']          = $page_number - 1;
				//echo '<pre>';print_r($filters);echo '</pre>';
				$post_ids = ABPET_Query::get_post_id( $filters );
				if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
					$total_post   = sizeof( ABPET_Query::get_post_id( [ 'status' => $status ] ) );
					$new_post_url = admin_url( 'post-new.php?post_type=' . $cpt );
					?>
                    <table class="_abp">
                        <thead>
                        <tr>
                            <th class="_w_50"><?php esc_html_e( 'SI', 'abp-event-ticket' ); ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Image', 'abp-event-ticket' ); ?></th>
                            <th><?php echo esc_html( ABPET_Function::label() ); ?></th>
                            <th><?php esc_html_e( 'Ticket Type & Qty', 'abp-event-ticket' ); ?></th>
                            <th><?php esc_html_e( 'Shortcode', 'abp-event-ticket' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'abp-event-ticket' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php
							foreach ( $post_ids as $post_id ) {
								$post_infos    = ABPET_Function::get_all_meta( $post_id );
								$title         = $post_infos['post_title'] ?? '';
								$seat_type     = $post_infos['seat_type'] ?? 'ticket';
								$seat_type     = ABPET_Function::on_off( 'sp' ) ? $seat_type : 'ticket';
								$edit_link     = get_edit_post_link( $post_id );
								$sale_continue = $post_infos['sale_continue'] ?? 'on';
								$post_status   = get_post_status( $post_id );
								$new_post_url  = add_query_arg( array( 'copy_post' => $post_id, '_abpet_nonce' => wp_create_nonce( 'abpet_copy_post_action' ), ), $new_post_url );
								?>
                                <tr>
                                    <th><?php echo esc_html( $count ); ?>.</th>
                                    <td><?php ABPET_Layout::image( $post_id ); ?></td>
                                    <td>
                                        <div class="_mar_b_xxs">
											<?php if ( $post_status == 'trash' ) { ?>
                                                <h6 class="_abp_color_warning"><?php ABPET_Layout::title( $post_infos ); ?></h6>
											<?php } else { ?>
                                                <a href="<?php echo esc_url( $edit_link ); ?>" class="_abp_fs_h6_color_theme"><?php ABPET_Layout::title( $post_infos ); ?></a>
											<?php } ?>
                                        </div>
                                        <div class="_gap_xxs">
                                            <span class="abp_tag"><?php echo esc_html( __( 'ID : ', 'abp-event-ticket' ) . ' ' . $post_id ); ?></span>
                                            <span class=" abp_tag <?php echo esc_attr( $sale_continue == 'on' ? 'publish' : 'trash' ); ?>">
                                                <?php echo esc_html( $sale_continue == 'on' ? __( 'Sale On', 'abp-event-ticket' ) : __( 'Sale Off', 'abp-event-ticket' ) ); ?>
                                            </span>
                                            <span class="abp_tag <?php echo esc_attr( $post_status ); ?>"><?php echo esc_html( $post_status ); ?></span>
                                        </div>
                                    </td>
                                    <th><?php echo esc_html( ABPET_Layout::ticket_type( $seat_type ) . ' - ' . ABPET_Function::get_total_qty( $post_id, $post_infos ) ); ?></th>
                                    <th><code> [abpet-post post_id="<?php echo esc_attr( $post_id ); ?>"]</code></th>
                                    <th>
                                        <div class="_group_content">
                                            <button type="button" class="_btn_light_navy_blue_xxs" data-href="<?php echo esc_url( $new_post_url ); ?>" data-blank="_blank" title="<?php echo esc_html__( 'Copy/Clone : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>"><?php ABPET_Static::icon_svg( 'clone_1' ); ?></button>
											<?php if ( $post_status == 'trash' ) { ?>
                                                <button type="button" class="_btn_light_success_xxs " onclick="abpet_post_action('restore','<?php echo esc_attr( $post_id ); ?>')" title="<?php echo esc_html__( 'Restore : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>">♻️</button>
                                                <button type="button" class="_btn_light_danger_xxs" onclick="abpet_post_action('permanent_remove','<?php echo esc_attr( $post_id ); ?>')" title="<?php echo esc_html__( 'Permanent Remove : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>"><?php ABPET_Static::icon_svg( 'close_2' ); ?></button>
											<?php } else { ?>
                                                <button type="button" class="_btn_light_yellow_xxs" data-href="<?php echo esc_url( $edit_link ); ?>" data-blank="_blank" title="<?php echo esc_html__( 'Edit : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>"><?php ABPET_Static::icon_svg( 'edit' ); ?></button>
                                                <button type="button" class="_btn_light_theme_xxs" data-href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" data-blank="_blank" title="<?php echo esc_html__( 'View : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>"><?php ABPET_Static::icon_svg( 'view_1' ); ?></button>
                                                <button type="button" class="_btn_light_danger_xxs" onclick="abpet_post_action('move_trash','<?php echo esc_attr( $post_id ); ?>')" title="<?php echo esc_html__( 'Move to Trash : ', 'abp-event-ticket' ) . ' ' . esc_html( $title ); ?>"><?php ABPET_Static::icon_svg( 'close_1' ); ?></button>
											<?php } ?>
                                        </div>
                                    </th>
                                </tr>
								<?php
								$count ++;
							}
						?>
                        </tbody>
                    </table>
					<?php
					do_action( 'abpet_pagination', [ 'page_item' => $limit, 'page_number' => $page_number, 'total' => $total_post, 'style' => 'ajax' ] );
				} else {
					ABPET_Layout::layout_warning_info( 'not_found' );
				}
			}
			public function settings(): void {
				$post_id      = get_the_id();
				$copy_post_id = isset( $_GET['copy_post'] ) ? absint( $_GET['copy_post'] ) : '';
				if ( ! empty( $copy_post_id ) && isset( $_GET['_abpet_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abpet_nonce'] ) ), 'abpet_copy_post_action' ) && current_user_can( 'edit_post', $copy_post_id ) ) {
					?>
                    <input type="hidden" name="abpet_copy_post" value="<?php echo esc_attr( $copy_post_id ); ?>"/>
					<?php
					$post_infos['copy_post_id'] = $copy_post_id;
					$new_post_id                = $copy_post_id;
				} else {
					$new_post_id = $post_id;
				}
				$post_infos = ABPET_Function::get_all_meta( $new_post_id );
				wp_nonce_field( 'abpet_post_nonce', 'abpet_post_nonce' );
				?>
                <div class="abpet_area abpet_admin abp_post_config">
                    <input type="hidden" name="abpet_post_id" value="<?php echo esc_attr( $post_id ); ?>"/>
                    <div class="_abp_panel">
                        <div class="abp_tabs tab_top">
                            <div class="_panel_head">
                                <ul class="_abp tab_lists">
                                    <li data-tabs-target="#abpet_general"><span class="fas fa-rainbow"></span><?php esc_html_e( 'General', 'abp-event-ticket' ); ?></li>
                                    <li data-tabs-target="#abpet_ticket"><span class="_mar_r_xxs">🎫 </span><?php esc_html_e( 'Ticket & Price', 'abp-event-ticket' ); ?></li>
                                    <li data-tabs-target="#abpet_dates"><span class="_mar_r_xxs">🗓️</span><?php esc_html_e( 'Date', 'abp-event-ticket' ); ?></li>
									<?php if ( ABPET_Function::on_off( 'additional_info' ) ) { ?>
                                        <li data-tabs-target="#abpet_additional_service"><span class="_mar_r_xxs">💰</span><?php esc_html_e( 'Additional services', 'abp-event-ticket' ); ?></li>
									<?php } ?>
									<?php if ( ABPET_Function::on_off( 'client_info' ) ) { ?>
                                        <li data-tabs-target="#abpet_client_form"><span class="_mar_r_xxs">📋</span><?php esc_html_e( 'Client Form', 'abp-event-ticket' ); ?></li>
									<?php } ?>
									<?php do_action( 'abpet_post_tab_menu', $post_infos ); ?>
                                    <li data-tabs-target="#abpet_resource"><span class="_mar_r_xxs">📚</span><?php esc_html_e( 'Resources', 'abp-event-ticket' ); ?></li>
                                </ul>
                            </div>
                            <div class="tab_content _panel_body">
								<?php
									$this->general_configuration( $post_infos );
									do_action( 'abpet_post_content', $post_infos );
								?>
                            </div>
                        </div>
                    </div>
					<?php ABPET_Layout::load_admin_globally(); ?>
                </div>
				<?php
			}
			public function general_configuration( $post_infos ): void {
				$abpet_template = $post_infos['abpet_template'] ?? 'default';
				$event_type = $post_infos['event_type'] ?? 'offline';
				?>
                <div class="tab_item" data-tabs="#abpet_general">
                    <h4 class="_abp_color_theme"><?php esc_html_e( 'General Configuration', 'abp-event-ticket' ); ?></h4>
					<?php ABPET_Layout::info_text( 'general_config' ); ?>
                    <div class="_divider_xs"></div>
                    <div class="group_setting">
                        <div class="setting_item">
                            <label>
								<?php ABPET_Layout::switch_checkbox( 'sale_continue', ( $post_infos['sale_continue'] ?? 'on' ) ); ?>
								<?php esc_html_e( 'Sale continue?', 'abp-event-ticket' ); ?>
                            </label>
                            <div class="_divider_xxs"></div>
							<?php ABPET_Layout::info_text( 'sale_continue' ); ?>
                        </div>
                        <div class="setting_item">
                            <label class="_f_equal_f_wrap">
                                <span class="_abp_label"><?php esc_html_e( 'Template', 'abp-event-ticket' ); ?></span>
                                <select class="_form_control " name="abpet_template" required>
                                    <option disabled selected><?php esc_html_e( 'Please Select', 'abp-event-ticket' ); ?></option>
                                    <option value="default" <?php echo esc_attr( $abpet_template == 'default' ? 'selected' : '' ); ?>><?php esc_html_e( 'Default Template', 'abp-event-ticket' ); ?></option>
                                    <option value="light" <?php echo esc_attr( $abpet_template == 'light' ? 'selected' : '' ); ?>><?php esc_html_e( 'Light Template', 'abp-event-ticket' ); ?></option>
                                </select>
                            </label>
                            <div class="_divider_xxs"></div>
							<?php ABPET_Layout::info_text( 'abpet_template' ); ?>
                        </div>
						<?php if ( ABPET_Function::on_off( 'sku' ) ) { ?>
                            <div class="setting_item">
                                <div class="_fj_between">
                                    <label>
										<?php ABPET_Layout::switch_checkbox( 'display_sku', ( $post_infos['display_sku'] ?? 'off' ) ); ?>
										<?php esc_html_e( 'ID/SKU', 'abp-event-ticket' ); ?>
                                    </label>
                                    <label>
                                        <input class="_form_control" name="post_sku" value="<?php echo esc_attr( $post_infos['post_sku'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Event ID', 'abp-event-ticket' ); ?>"/>
                                    </label>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'post_sku' ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPET_Function::on_off( 'post_icon' ) ) { ?>
                            <div class="setting_item">
                                <divl class="_fj_between">
                                    <span class="_abp_label"><?php esc_html_e( 'Event Icon', 'abp-event-ticket' ); ?></span>
									<?php do_action( 'abpet_add_icon', 'post_icon', ( $post_infos['post_icon'] ?? '' ) ); ?>
                                </divl>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'post_icon' ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPET_Function::on_off( 'sub_title' ) ) { ?>
                            <div class="setting_item">
                                <div class="_f_equal_f_wrap">
                                    <span class="_abp_label"><?php esc_html_e( 'Sub Title', 'abp-event-ticket' ); ?></span>
                                    <label>
                                        <textarea class="_form_control" name="sub_title" placeholder="<?php esc_attr_e( 'Event Sub Title', 'abp-event-ticket' ); ?>"><?php echo esc_html( $post_infos['sub_title'] ?? '' ); ?></textarea>
                                    </label>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'sub_title' ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPET_Function::on_off( 'post_des' ) ) { ?>
                            <div class="setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_abp_label"><?php esc_html_e( 'Short Description', 'abp-event-ticket' ); ?></span>
                                    <textarea class="_form_control" name="post_description" placeholder="<?php esc_attr_e( 'EX: Description', 'abp-event-ticket' ); ?>"><?php echo esc_html( $post_infos['post_description'] ?? '' ); ?></textarea>
                                </label>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'post_description' ); ?>
                            </div>
						<?php } ?>
                        <div class="setting_item">
                            <div class=" _fj_between">
                                <span class="_abp_label"><?php esc_html_e( 'Event Type', 'abp-event-ticket' ); ?><sup class="_color_required">*</sup></span>
                                <div class="custom_radio _group_content">
                                    <input type="hidden" class="_form_control" name="event_type" value="<?php echo esc_attr( $event_type ); ?>"/>
                                    <div class="radio_item">
                                        <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $event_type == 'offline' ? 'abp_active' : '' ); ?>"  data-radio="offline" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                            <span data-icon class="<?php echo esc_attr( $event_type == 'offline' ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php esc_html_e( 'Offline', 'abp-event-ticket' ); ?>
                                        </button>
                                    </div>
                                    <div class="radio_item">
                                        <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $event_type == 'online' ? 'abp_active' : '' ); ?>"  data-radio="online" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                            <span data-icon class=" <?php echo esc_attr( $event_type == 'online' ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php esc_html_e( 'Online', 'abp-event-ticket' ); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
		                    <?php ABPET_Layout::info_text( 'event_type' ); ?>
                        </div>
						<?php if ( ABPET_Function::on_off( 'display_capacity' ) ) { ?>
                            <div class="setting_item">
                                <label>
									<?php ABPET_Layout::switch_checkbox( 'display_capacity', ( $post_infos['display_capacity'] ?? 'off' ) ); ?>
									<?php esc_html_e( 'Display Capacity', 'abp-event-ticket' ); ?>
                                </label>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'display_capacity' ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPET_Function::on_off( 'location' ) ) { ?>
                            <div class="setting_item abpet_location">
                                <div class="_fj_between_fa_center">
                                    <label>
										<?php ABPET_Layout::switch_checkbox( 'display_location', ( $post_infos['display_location'] ?? 'on' ) ); ?>
										<?php echo esc_html( ABPET_Function::location_label() ); ?>
                                    </label>
                                    <div class="_group_content">
										<?php ABPET_Layout::selection_area();
											ABPET_Layout::button_global_popup( 'tax_location', __( 'Add New', 'abp-event-ticket' ) . ' ' . ABPET_Function::location_label() ); ?>
                                    </div>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'display_location' );
									ABPET_Layout::selected_area( 'abpet_location', ( $post_infos['abpet_location'] ?? '' ) ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPET_Function::on_off( 'category' ) ) { ?>
                            <div class="setting_item abpet_category">
                                <div class="_fj_between_fa_center">
                                    <label>
										<?php ABPET_Layout::switch_checkbox( 'display_category', ( $post_infos['display_category'] ?? 'on' ) ); ?>
										<?php echo esc_html( ABPET_Function::category_label() ); ?>
                                    </label>
                                    <div class="_group_content">
										<?php ABPET_Layout::selection_area();
											ABPET_Layout::button_global_popup( 'tax_category', __( 'Add New', 'abp-event-ticket' ) . ' ' . ABPET_Function::category_label() ); ?>
                                    </div>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'display_category' );
									ABPET_Layout::selected_area( 'abpet_category', ( $post_infos['abpet_category'] ?? '' ) ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPET_Function::on_off( 'organizer' ) ) { ?>
                            <div class="setting_item abpet_organizer">
                                <div class="_fj_between_fa_center">
                                    <label>
										<?php ABPET_Layout::switch_checkbox( 'display_organizer', ( $post_infos['display_organizer'] ?? 'off' ) ); ?>
										<?php echo esc_html( ABPET_Function::organizer_label() ); ?>
                                    </label>
                                    <div class="_group_content">
										<?php ABPET_Layout::selection_area();
											ABPET_Layout::button_global_popup( 'tax_organizer', __( 'Add New', 'abp-event-ticket' ) . ' ' . ABPET_Function::organizer_label() ); ?>
                                    </div>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'display_organizer' );
									ABPET_Layout::selected_area( 'abpet_organizer', ( $post_infos['abpet_organizer'] ?? '' ) ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPET_Function::on_off( 'brand' ) ) { ?>
                            <div class="setting_item abpet_brand">
                                <div class="_fj_between_fa_center">
                                    <label>
										<?php ABPET_Layout::switch_checkbox( 'display_brand', ( $post_infos['display_brand'] ?? 'off' ) ); ?>
										<?php echo esc_html( ABPET_Function::brand_label() ); ?>
                                    </label>
                                    <div class="_group_content">
										<?php ABPET_Layout::selection_area();
											ABPET_Layout::button_global_popup( 'tax_brand', __( 'Add New', 'abp-event-ticket' ) . ' ' . ABPET_Function::brand_label() ); ?>
                                    </div>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'display_brand' );
									ABPET_Layout::selected_area( 'abpet_brand', ( $post_infos['abpet_brand'] ?? '' ) ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPET_Function::on_off( 'related' ) ) { ?>
                            <div class="setting_item related_item">
                                <div class="_fj_between_fa_center">
                                    <span class="_abp_label"><?php esc_html_e( 'Related Event', 'abp-event-ticket' ); ?></span>
									<?php ABPET_Layout::selection_area(); ?>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'related_item' );
									ABPET_Layout::selected_area( 'related_item', ( $post_infos['related_item'] ?? '' ) ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPET_Function::on_off( 'feature' ) ) { ?>
                            <div class="setting_item post_feature">
                                <div class="_fj_between_fa_center">
                                    <span class="_abp_label"><?php esc_html_e( 'Feature', 'abp-event-ticket' ); ?></span>
                                    <div class="_group_content">
										<?php ABPET_Layout::selection_area();
											ABPET_Layout::button_global_popup( 'option_feature', __( 'Add New', 'abp-event-ticket' ) . ' ' . ABPET_Function::feature_label() ); ?>
                                    </div>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'post_feature' );
									ABPET_Layout::selected_area( 'post_feature', ( $post_infos['post_feature'] ?? '' ) ); ?>
                            </div>
						<?php } ?>
                        <div class="setting_item full_width">
                            <span class="_abp_label"><?php esc_html_e( 'Gallery', 'abp-event-ticket' ); ?></span>
                            <div class="_divider_xxs"></div>
							<?php ABPET_Layout::info_text( 'display_slider' ); ?>
                            <div class="_divider_xxs"></div>
							<?php do_action( 'abpet_add_image_multiple', 'abpet_slider', ( $post_infos['abpet_slider'] ?? '' ) ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
			//====================================//
			public function save_settings( $post_id ): void {
				if ( ! isset( $_POST['abpet_post_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abpet_post_nonce'] ) ), 'abpet_post_nonce' ) ) {
					return;
				}
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
					return;
				}
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
				if ( get_post_type( $post_id ) == ABPET_Function::get_cpt() ) {
					$post_int            = fn( $key, $default = 0 ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
					$post_val            = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
					$post_textarea       = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : $default;
					$post_html           = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? wp_kses_post( wp_unslash( $_POST[ $key ] ) ) : $default;
					$post_int_array      = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'absint', wp_unslash( $_POST[ $key ] ) ) : [];
					$post_array          = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
					$post_textarea_array = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST[ $key ] ) ) : [];
					$post_deep           = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? map_deep( wp_unslash( $_POST[ $key ] ), 'sanitize_text_field' ) : [];
					$format_date                       = fn( $date ) => $date ? gmdate( 'Y-m-d', strtotime( $date ) ) : '';
					//$post_html_array     = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'wp_kses_post', wp_unslash( $_POST[ $key ] ) ) : [];
					/***********************************/
					$seat_type           = $post_val( 'seat_type' );
					$display_ticket_type = $post_val( 'display_ticket_type' );
					$ticket_infos        = [];
					$sp_infos            = [];
					$all_ticket_types    = [];
					if ( $display_ticket_type == 'off' ) {
						$all_ticket_types[] = 'price';
					}
					$ticket_ids         = $post_array( 'ticket_name' );
					$price_data         = $post_array( 'ticket_price' );
					if ( ! empty( $ticket_ids ) ) {
						foreach ( $ticket_ids as $key => $id ) {
							if ( ! empty( $id ) ) {
								if ( $display_ticket_type == 'on' ) {
									$all_ticket_types[] = $id;
								}
								$ticket_infos[ $id ]['price'] = $price_data[ $key ] ?? 0;
							}
						}
					}
					if ( $seat_type == 'ticket' ) {
						$ticket_qty         = $post_array( 'ticket_qty' );
						$reserve_qty        = $post_array( 'reserve_qty' );
						$ticket_min_qty     = $post_array( 'ticket_min_qty' );
						$ticket_max_qty     = $post_array( 'ticket_max_qty' );
						$ticket_description = $post_textarea_array( 'ticket_description' );
						if ( ! empty( $ticket_ids ) ) {
							foreach ( $ticket_ids as $key => $id ) {
								if ( ! empty( $id ) ) {
									if ( $display_ticket_type == 'on' ) {
										$all_ticket_types[] = $id;
									}
									$ticket_infos[ $id ] = [
										'qty'         => $ticket_qty[ $key ] ?? 10,
										'price'       => $price_data[ $key ] ?? 0,
										'reserve'     => $reserve_qty[ $key ] ?? 0,
										'min_qty'     => $ticket_min_qty[ $key ] ?? 1,
										'max_qty'     => $ticket_max_qty[ $key ] ?? '',
										'description' => $ticket_description[ $key ] ?? '',
									];
								}
							}
						}
					} else {
						$sp_ids    = $post_int_array( 'sp_id' );
						$sp_names  = $post_array( 'sp_name' );
						$all_sp_id = [];
						if ( ! empty( $sp_ids ) ) {
							foreach ( $sp_ids as $key => $id ) {
								if ( ! empty( $id ) ) {
									$all_sp_id[] = $id;
									$sp_infos[]  = [
										'id'   => $id,
										'name' => $sp_names[ $key ] ?? '',
									];
								}
							}
						}
						if ( $display_ticket_type == 'on' ) {
							$all_ticket_types = ABPET_Function::get_sp_ticket( $all_sp_id );
						}
					}
					/***********************************/

					$date_infos['date_type']           = $post_val( 'date_type', 'periodic_date' );
					$date_infos['periodic_start_date'] = $format_date( $post_val( 'periodic_start_date' ) );
					$date_infos['periodic_end_date']   = $format_date( $post_val( 'periodic_end_date' ) );
					$date_infos['periodic_after']      = $post_val( 'periodic_after', '1' );
					$date_rule                         = $post_val( 'date_rule' );
					$date_infos['date_rule']           = $post_val( 'date_rule' );
					$date_rule_array                   = $date_rule ? explode( ',', $date_rule ) : [];
					if ( in_array( 'weekend', $date_rule_array ) ) {
						$date_infos['weekend'] = $post_val( 'weekend' );
					}
					if ( in_array( 'specific_off_dates', $date_rule_array ) ) {
						$specific_off_dates               = array_filter( $post_array( 'specific_off_dates' ) );
						$date_infos['specific_off_dates'] = array_unique( array_map( fn( $d ) => gmdate( 'Y-m-d', strtotime( $d ) ), $specific_off_dates ) );
					}
					if ( in_array( 'off_date_range', $date_rule_array ) ) {
						$off_schedules = [];
						$from_dates    = $post_array( 'abpet_off_from' );
						$to_dates      = $post_array( 'abpet_off_to' );
						foreach ( $from_dates as $key => $from_date ) {
							if ( $from_date && ! empty( $to_dates[ $key ] ) ) {
								$off_schedules[] = [ 'from' => $from_date, 'to' => $to_dates[ $key ] ];
							}
						}
						$date_infos['off_date_range'] = $off_schedules;
					}
					if ( in_array( 'special_on_dates', $date_rule_array ) ) {
						$special_on_dates = $post_array( 'special_on_dates' );
						$on_start         = $post_array( 'special_on_time_start' );
						$on_end           = $post_array( 'special_on_time_end' );
						$specific_on      = [];
						foreach ( $special_on_dates as $key => $date ) {
							if ( $date ) {
								$specific_on[ $key ] = [ 'date' => $format_date( $date ), 'start' => $on_start[ $key ] ?? '', 'end' => $on_end[ $key ] ?? '' ];
							}
						}
						$date_infos['special_on_dates'] = $specific_on;
					}
					$off_schedules = [];
					$from_dates    = $post_array( 'abpet_off_from' );
					$to_dates      = $post_array( 'abpet_off_to' );
					foreach ( $from_dates as $key => $from_date ) {
						if ( $from_date && ! empty( $to_dates[ $key ] ) ) {
							$off_schedules[] = [ 'from' => $from_date, 'to' => $to_dates[ $key ] ];
						}
					}
					$date_infos['off_date_range'] = $off_schedules;
					/****************/
					$specific_dates = $post_array( 'specific_dates' );
					$specific_dates = ! empty( $specific_dates ) ? array_values( array_unique( array_filter( $specific_dates ) ) ) : [];
					sort( $specific_dates );
					$date_infos['specific_dates'] = $specific_dates;
					$operation_time  = [];
					$operation_times = $post_array( 'operation_time' );
					if ( ! empty( $operation_times ) ) {
						$operation_time = array_values( array_unique( array_filter( $operation_times ) ) );
						sort( $operation_time );
					}
					$time_info['time'] = ! empty( $operation_time ) ? $operation_time : [ '00:00' ];
					$opt_time          = $post_val( 'operation_time_optional' );
					$opt_time          = ! empty( $opt_time ) ? explode( ',', $opt_time ) : [];
					if ( in_array( 'day_wise_time', $opt_time ) ) {
						foreach ( ABPET_Layout::week_day() as $key => $day ) {
							$times = $post_array( $key . '_time' );
							$times = array_filter( $times );
							if ( ! empty( $times ) ) {
								sort( $times );
								$time_info['day_time'][ $key ] = array_values( array_unique( $times ) );
							}
						}
					}
					if ( in_array( 'date_wise_time', $opt_time ) ) {
						$date_time_ids = $post_array( 'date_wise_time_id' );
						$all_dates     = $post_deep( 'date_wise_date' );
						$all_times     = $post_deep( 'date_wise_time' );
						if ( ! empty( $date_time_ids ) ) {
							foreach ( $date_time_ids as $time_id ) {
								$date_wise_dates = $all_dates[ $time_id ] ?? [];
								$date_wise_time  = $all_times[ $time_id ] ?? [];
								if ( ! empty( $date_wise_dates ) && ! empty( $date_wise_time ) ) {
									$clean_dates = array_filter( $date_wise_dates );
									$clean_dates = reset( $clean_dates );
									$clean_times = array_filter( $date_wise_time );
									if ( ! empty( $clean_dates ) && ! empty( $clean_times ) ) {
										sort( $clean_times );
										$time_info['date_times'][ $time_id ]['date'] = $clean_dates;
										$time_info['date_times'][ $time_id ]['time'] = array_values( array_unique( $clean_times ) );
									}
								}
							}
						}
					};
					/***********************************/
					$display_additional_services = $post_val( 'display_additional_services', 'off' );
					$active_global_additional    = $display_additional_services == 'on' ? $post_val( 'active_global_additional', 'on' ) : 'off';
					$additional_services         = ( $active_global_additional == 'on' || $display_additional_services == 'off' ) ? [] : apply_filters( 'abpet_get_additional_array', [] );
					$display_client_form         = $post_val( 'display_client_form', 'off' );
					$active_global_form          = $display_client_form == 'on' ? $post_val( 'active_global_form', 'on' ) : 'off';
					$abpet_form                  = ( $active_global_form == 'on' || $display_client_form == 'off' ) ? [] : apply_filters( 'abpet_get_form_array', [] );
					$display_faq                 = $post_val( 'display_faq', 'off' );
					$active_global_faq           = $display_faq == 'on' ? $post_val( 'active_global_faq', 'on' ) : 'off';
					$abpet_faq                   = ( $active_global_faq == 'on' || $display_faq == 'off' ) ? [] : apply_filters( 'abpet_get_faq_array', [] );
					$display_tc                  = $post_val( 'display_tc', 'off' );
					$active_global_tc            = $display_tc == 'on' ? $post_val( 'active_global_tc', 'on' ) : 'off';
					$abpet_tc                    = ( $active_global_tc == 'on' || $display_tc == 'off' ) ? [] : $post_html( 'tc_content' );
					$meta_info                   = [
						'sale_continue'               => $post_val( 'sale_continue', 'on' ),
						'abpet_template'              => $post_val( 'abpet_template', 'default' ),
						'display_sku'                 => $post_val( 'display_sku', 'off' ),
						'post_sku'                    => $post_val( 'post_sku' ),
						'post_icon'                   => $post_val( 'post_icon' ),
						'sub_title'                   => $post_textarea( 'sub_title' ),
						'post_description'            => $post_textarea( 'post_description' ),
						'display_organizer'           => $post_val( 'display_organizer', 'off' ),
						'event_type'             => $post_val( 'event_type' ),
						'abpet_organizer'             => $post_val( 'abpet_organizer' ),
						'display_brand'               => $post_val( 'display_brand', 'off' ),
						'abpet_brand'                 => $post_val( 'abpet_brand' ),
						'display_capacity'            => $post_val( 'display_capacity', 'on' ),
						'display_category'            => $post_val( 'display_category', 'on' ),
						'abpet_category'              => $post_val( 'abpet_category' ),
						'display_location'            => $post_val( 'display_location', 'on' ),
						'abpet_location'              => $post_val( 'abpet_location' ),
						'related_item'                => $post_val( 'related_item' ),
						'post_feature'                => $post_val( 'post_feature' ),
						'abpet_slider'                => $post_val( 'abpet_slider' ),
						//================//
						'seat_type'                   => $seat_type,
						'display_ticket_type'         => $display_ticket_type,
						'min_qty'                     => $post_int( 'min_qty' ),
						'max_qty'                     => $post_int( 'max_qty' ),
						'ticket_infos'                => $ticket_infos,
						'sp_infos'                    => $sp_infos,
						'all_ticket_type'             => $all_ticket_types,
						//================//
						'abpet_dates'                 => $date_infos,
						'time_infos'                  => $time_info,
						'display_additional_services' => $display_additional_services,
						'active_global_additional'    => $active_global_additional,
						'additional_services'         => $additional_services,
						'display_client_form'         => $display_client_form,
						'active_global_form'          => $active_global_form,
						'display_single_form'         => $post_val( 'display_single_form', 'on' ),
						'abpet_form'                  => $abpet_form,
						'display_faq'                 => $display_faq,
						'active_global_faq'           => $active_global_faq,
						'abpet_faq'                   => $abpet_faq,
						'display_tc'                  => $display_tc,
						'active_global_tc'            => $active_global_tc,
						'abpet_tc'                    => $abpet_tc,
					];
					//=============tax================//
					if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) {
						$meta_info['_tax_status'] = $post_val( '_tax_status', 'none' );
						$meta_info['_tax_class']  = $post_val( '_tax_class' );
					}
					//=============================//
					$meta_info = apply_filters( 'abpet_meta_info_update', $meta_info, $post_id );
					if ( sizeof( $meta_info ) > 0 ) {
						foreach ( $meta_info as $key => $value ) {
							update_post_meta( $post_id, sanitize_key( $key ), $value );
						}
					}
				}
			}
			public function post_permanent_remove(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
				if ( $post_id <= 0 ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid ID ..... !! ', 'abp-event-ticket' ), 'type' => 'warn' ], 400 );
				}
				$title      = get_the_title( $post_id );
				$link_wc_id = absint( ABPET_Function::get_post_info( $post_id, 'link_wc_id' ) );
				if ( $link_wc_id > 0 ) {
					wp_delete_post( $link_wc_id, true );
				}
				wp_delete_post( $post_id, true );
				wp_send_json_success( [ 'html' => '', 'msg' => $title . ' : ' . __( 'Permanently removed. ..... !! ', 'abp-event-ticket' ), 'type' => 'error' ] );
			}
			public function post_move_trash(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
				if ( $post_id > 0 ) {
					$title      = get_the_title( $post_id );
					$link_wc_id = absint( ABPET_Function::get_post_info( $post_id, 'link_wc_id' ) );
					if ( $link_wc_id > 0 ) {
						wp_trash_post( $link_wc_id );
					}
					wp_trash_post( $post_id );
					wp_send_json_success( [ 'html' => '', 'msg' => $title . ' : ' . __( 'Moved to trash successfully...... !! ', 'abp-event-ticket' ), 'type' => 'warn' ] );
				}
				wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid  ID ..... !! ', 'abp-event-ticket' ), 'type' => 'warn' ], 400 );
			}
			public function post_restore(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
				if ( $post_id > 0 ) {
					$link_wc_id = absint( ABPET_Function::get_post_info( $post_id, 'link_wc_id' ) );
					if ( $link_wc_id > 0 ) {
						wp_untrash_post( $link_wc_id );
					}
					wp_untrash_post( $post_id );
					$updated_post = [
						'ID'          => $post_id,
						'post_status' => 'publish',
					];
					wp_update_post( $updated_post );
					$title = get_the_title( $post_id );
					wp_send_json_success( [ 'html' => '', 'msg' => $title . ' : ' . __( 'Restored successfully...... !! ', 'abp-event-ticket' ), 'type' => 'success' ] );
				}
				wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid  ID ..... !! ', 'abp-event-ticket' ), 'type' => 'warn' ], 400 );
			}
			public function reload_post_list(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_array  = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
				$filter_args = $post_array( 'filter_args' );
				ob_start();
				$this->post_table( $filter_args );
				$table_html = ob_get_clean();
				wp_send_json_success( [
					'html' => $table_html,
					'type' => 'success',
					'msg'  => __( 'Post List Loaded successfully...... !! ', 'abp-event-ticket' )
				] );
			}
		}
		new ABPET_Post();
	}