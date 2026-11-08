<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Post_Configuration' ) ) {
		class ABPET_Post_Configuration {
			public function __construct() {
				add_action( 'add_meta_boxes', [ $this, 'settings_meta' ] );
				add_action( 'save_post', array( $this, 'save_settings' ) );
			}
			public function settings_meta(): void {
				$label      = ABPET_Function::label();
				$brand_icon = ABPET_Function::icon();
				$label      = $label . ' ' . __( 'Configuration', 'abp-event-ticket' ) . get_the_title( get_the_id() );
				add_meta_box( 'abpet_configuration', '<span class="' . esc_attr( $brand_icon ?: '' ) . '"></span>' . esc_html( $label ), array( $this, 'settings' ), esc_attr( ABPET_Function::get_cpt() ), 'normal', 'high' );
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
                <div class="abpet_area abpet_admin">
                    <input type="hidden" name="abpet_post_id" value="<?php echo esc_attr( $post_id ); ?>"/>
                    <div class="abp_tabs tab_left">
                        <ul class="abp tab_lists">
                            <li data-tabs-target="#abpet_general"><span class="fas fa-rainbow"></span><?php esc_html_e( 'General', 'abp-event-ticket' ); ?></li>
                            <li data-tabs-target="#abpet_ticket">🎫 <?php esc_html_e( 'Ticket & Price', 'abp-event-ticket' ); ?></li>
                            <li data-tabs-target="#abpet_dates">🗓️<?php esc_html_e( 'Date', 'abp-event-ticket' ); ?></li>
							<?php if ( ABPET_Function::on_off( 'additional_info' ) ) { ?>
                                <li data-tabs-target="#abpet_additional_service">💰<?php esc_html_e( 'Additional services', 'abp-event-ticket' ); ?></li>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'client_info' ) ) { ?>
                                <li data-tabs-target="#abpet_client_form">📋<?php esc_html_e( 'Attendee Form', 'abp-event-ticket' ); ?></li>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'speaker' ) ) { ?>
                                <li data-tabs-target="#abpet_speaker">🎤<?php echo esc_html( ABPET_Function::speaker_label() ); ?></li>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'related' ) ) { ?>
                                <li data-tabs-target="#abpet_related">🔁<?php esc_html_e( 'Related Event', 'abp-event-ticket' ); ?></li>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'timeline' ) ) { ?>
                                <li data-tabs-target="#abpet_timeline">⏱️<?php esc_html_e( 'Timeline', 'abp-event-ticket' ); ?></li>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'feature' ) ) { ?>
                                <li data-tabs-target="#abpet_feature">🔗<?php echo esc_html( ABPET_Function::feature_label() ); ?></li>
							<?php } ?>
							<?php do_action( 'abpet_post_tab_menu', $post_infos ); ?>
                            <li data-tabs-target="#abpet_gallery">🖼️<?php esc_html_e( 'Gallery', 'abp-event-ticket' ); ?></li>
                            <li data-tabs-target="#abpet_tax">🧾<?php esc_html_e( 'Tax', 'abp-event-ticket' ); ?></li>
							<?php if ( ABPET_Function::on_off( 'faq' ) ) { ?>
                                <li data-tabs-target="#abpet_faq">❓<?php esc_html_e( 'FAQs', 'abp-event-ticket' ); ?></li>
							<?php } ?>
							<?php if ( ABPET_Function::on_off( 'tc' ) ) { ?>
                                <li data-tabs-target="#abpet_tc">🤝<?php esc_html_e( 'Term & Conditions', 'abp-event-ticket' ); ?></li>
							<?php } ?>
                        </ul>
                        <div class="tab_content ">
							<?php
								$this->general_configuration( $post_infos );
								$this->related_configuration( $post_infos );
								$this->gallery_configuration( $post_infos );
								$this->tax_configuration( $post_infos );
								$this->timeline_configuration( $post_infos );
								do_action( 'abpet_post_content', $post_infos );
							?>
                        </div>
                    </div>
					<?php ABPET_Layout::load_admin_globally(); ?>
                </div>
				<?php
			}
			public function general_configuration( $post_infos ): void {
				$abpet_template = $post_infos['abpet_template'] ?? 'default';
				$event_type     = $post_infos['event_type'] ?? 'offline';
				?>
                <div class="tab_item" data-tabs="#abpet_general">
                    <h4 class="abp_color_theme"><?php esc_html_e( 'General Configuration', 'abp-event-ticket' ); ?></h4>
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
                                <span class="abp_label"><?php esc_html_e( 'Template', 'abp-event-ticket' ); ?></span>
                                <select class="_form_control " name="abpet_template" required>
                                    <option disabled selected><?php esc_html_e( 'Please Select', 'abp-event-ticket' ); ?></option>
                                    <option value="default" <?php echo esc_attr( $abpet_template == 'default' ? 'selected' : '' ); ?>><?php esc_html_e( 'Default Template', 'abp-event-ticket' ); ?></option>
                                    <option value="light" <?php echo esc_attr( $abpet_template == 'light' ? 'selected' : '' ); ?>><?php esc_html_e( 'Light Template', 'abp-event-ticket' ); ?></option>
                                    <option value="modern" <?php echo esc_attr( $abpet_template == 'modern' ? 'selected' : '' ); ?>><?php esc_html_e( 'Modern Template', 'abp-event-ticket' ); ?></option>
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
                                    <span class="abp_label"><?php esc_html_e( 'Event Icon', 'abp-event-ticket' ); ?></span>
									<?php do_action( 'abpet_add_icon', 'post_icon', ( $post_infos['post_icon'] ?? '' ) ); ?>
                                </divl>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'post_icon' ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPET_Function::on_off( 'sub_title' ) ) { ?>
                            <div class="setting_item">
                                <div class="_f_equal_f_wrap">
                                    <span class="abp_label"><?php esc_html_e( 'Sub Title', 'abp-event-ticket' ); ?></span>
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
                                    <span class="abp_label"><?php esc_html_e( 'Short Description', 'abp-event-ticket' ); ?></span>
                                    <textarea class="_form_control" name="post_description" placeholder="<?php esc_attr_e( 'EX: Description', 'abp-event-ticket' ); ?>"><?php echo esc_html( $post_infos['post_description'] ?? '' ); ?></textarea>
                                </label>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'post_description' ); ?>
                            </div>
						<?php } ?>
                        <div class="setting_item">
                            <div class=" _fj_between">
                                <span class="abp_label"><?php esc_html_e( 'Event Type', 'abp-event-ticket' ); ?><sup class="_color_required">*</sup></span>
                                <div class="custom_radio _group_content">
                                    <input type="hidden" class="_form_control" name="event_type" value="<?php echo esc_attr( $event_type ); ?>"/>
                                    <div class="radio_item">
                                        <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $event_type == 'offline' ? 'abp_active' : '' ); ?>" data-radio="offline" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                            <span data-icon class="<?php echo esc_attr( $event_type == 'offline' ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php esc_html_e( 'Offline', 'abp-event-ticket' ); ?>
                                        </button>
                                    </div>
                                    <div class="radio_item">
                                        <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $event_type == 'online' ? 'abp_active' : '' ); ?>" data-radio="online" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
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
                    </div>
                </div>
				<?php
			}
			public function related_configuration( $post_infos = [] ): void {
				if ( ABPET_Function::on_off( 'related' ) ) {
					?>
                    <div class="tab_item related_item" data-tabs="#abpet_related">
                        <h4 class="abp_color_theme_gap_xxs">🔁<?php esc_html_e( 'Related Event Configuration', 'abp-event-ticket' ); ?></h4>
                        <div class="_divider_xxs"></div>
                        <div class="group_setting">
                            <div class="setting_item">
                                <div class="_fj_between_fa_center">
                                    <span class="abp_label"><?php esc_html_e( 'Related Event', 'abp-event-ticket' ); ?></span>
									<?php ABPET_Layout::selection_area(); ?>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::info_text( 'related_item' );
									ABPET_Layout::selected_area( 'related_item', ( $post_infos['related_item'] ?? '' ) ); ?>
                            </div>
                        </div>
                    </div>
					<?php
				}
			}
			public function gallery_configuration( $post_infos = [] ): void {
				?>
                <div class="tab_item post_gallery" data-tabs="#abpet_gallery">
                    <h4 class="abp_color_theme_gap_xxs">🖼️<?php esc_html_e( 'Gallery Configuration', 'abp-event-ticket' ); ?></h4>
                    <div class="_divider_xxs"></div>
                    <div class="group_setting">
                        <div class="setting_item full_width">
                            <span class="abp_label"><?php esc_html_e( 'Gallery', 'abp-event-ticket' ); ?></span>
                            <div class="_divider_xxs"></div>
							<?php ABPET_Layout::info_text( 'display_slider' ); ?>
                            <div class="_divider_xxs"></div>
							<?php do_action( 'abpet_add_image_multiple', 'abpet_slider', ( $post_infos['abpet_slider'] ?? '' ) ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function tax_configuration( $post_infos = [] ): void {
				$tax_status  = $post_infos['_tax_status'] ?? '';
				$tax_classes = WC_Tax::get_tax_rate_classes();
				$tax_class   = $post_infos['_tax_class'] ?? '';
				?>
                <div class="tab_item" data-tabs="#abpet_tax">
                    <h4 class="abp_color_theme_gap_xxs">🧾<?php esc_html_e( 'Tax Configuration', 'abp-event-ticket' ); ?></h4>
                    <div class="_divider_xxs"></div>
					<?php if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) { ?>
                        <div class="group_setting">
                            <div class="setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span><?php esc_html_e( 'Tax Status', 'abp-event-ticket' ); ?></span>
                                    <select class="_form_control" name="_tax_status">
                                        <option disabled selected><?php esc_html_e( 'Please Select', 'abp-event-ticket' ); ?></option>
                                        <option value="taxable" <?php echo esc_attr( $tax_status == 'taxable' ? 'selected' : '' ); ?>><?php esc_html_e( 'Taxable', 'abp-event-ticket' ); ?></option>
                                        <option value="shipping" <?php echo esc_attr( $tax_status == 'shipping' ? 'selected' : '' ); ?>><?php esc_html_e( 'Shipping only', 'abp-event-ticket' ); ?></option>
                                        <option value="none" <?php echo esc_attr( $tax_status == 'none' ? 'selected' : '' ); ?>><?php esc_html_e( 'None', 'abp-event-ticket' ); ?></option>
                                    </select>
                                </label>
                            </div>
                            <div class="setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span><?php esc_html_e( 'Tax Class', 'abp-event-ticket' ); ?></span>
                                    <select class="_form_control" name="_tax_class">
                                        <option disabled selected><?php esc_html_e( 'Please Select', 'abp-event-ticket' ); ?></option>
                                        <option value="standard" <?php echo esc_attr( $tax_class == 'standard' ? 'selected' : '' ); ?>><?php esc_html_e( 'Standard', 'abp-event-ticket' ); ?></option>
										<?php if ( sizeof( $tax_classes ) > 0 ) { ?>
											<?php foreach ( $tax_classes as $class ) { ?>
                                                <option value="<?php echo esc_attr( $class->slug ); ?>" <?php echo esc_attr( $tax_class == $class->slug ? 'selected' : '' ); ?>> <?php echo esc_html( $class->name ); ?> </option>
											<?php } ?>
										<?php } ?>
                                    </select>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPET_Layout::info_text( '_tax_class' ); ?>
                            </div>
                        </div>
					<?php } else {
						ABPET_Layout::layout_warning_info( 'enable_tax_msg' );
					} ?>
                </div>
				<?php
			}
			public function timeline_configuration( $post_infos = [] ): void {
				if ( ABPET_Function::on_off( 'timeline' ) ) {
					$post_id  = absint( $post_infos['post_id'] ?? 0 );
					$timeline = get_post_meta( $post_id, 'abpet_timeline', true );
					$timeline = is_array( $timeline ) ? $timeline : [];
					?>
                    <div class="tab_item timeline_configuration" data-tabs="#abpet_timeline">
                        <h4 class="abp_color_theme_gap_xxs">⏱️<?php esc_html_e( 'Timeline Configuration', 'abp-event-ticket' ); ?></h4>
                        <div class="_divider_xxs"></div>
						<?php ABPET_Layout::info_text( 'display_timeline' ); ?>
                        <div class="timeline_content _mar_t_xs">
                            <div class="configuration_content">
                                <div class="insertable_area sortable_area">
									<?php
										if ( ! empty( $timeline ) && sizeof( $timeline ) > 0 ) {
											foreach ( $timeline as $item ) {
												$this->timeline_item( $item );
											}
										}
									?>
                                </div>
                                <div class="_divider_xxs"></div>
								<?php ABPET_Layout::button_add( __( 'Add New Timeline Item', 'abp-event-ticket' ) ); ?>
                                <div class="abp_hidden">
                                    <div class="hidden_content">
										<?php $this->timeline_item(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php
				}
			}
			public function timeline_item( $item = [] ): void {
				$time        = $item['time'] ?? '';
				$title       = $item['title'] ?? '';
				$title       = $title ?: __( 'NEW', 'abp-event-ticket' );
				$description = $item['des'] ?? '';
				$description = $description ? html_entity_decode( $description ) : '';
				$editor_id   = uniqid( 'abpet_timeline_editor_' );
				?>
                <div class="delete_area timeline_item  <?php echo esc_attr( empty( $item ) ? 'active' : '' ); ?>">
                    <div class="timeline_head">
                        <h6 class="abp edit_hook" data-paste="#timeline_title"><?php echo esc_html( $title ); ?></h6>
						<?php ABPET_Layout::button_delete_sort_edit(); ?>
                    </div>
                    <div class="edit_area timeline_body">
						<?php ABPET_Layout::info_text( 'timeline_item' ); ?>
                        <label class="_f_equal_f_wrap">
                            <span class="abp_label"><?php esc_html_e( 'Time', 'abp-event-ticket' ); ?></span>
                            <input type="text" class="_form_control" name="timeline_time[]" placeholder="<?php esc_attr_e( 'EX: 10:00 AM', 'abp-event-ticket' ); ?>" value="<?php echo esc_attr( $time ); ?>"/>
                        </label>
                        <div class="_divider_xs"></div>
                        <label class="_f_equal_f_wrap">
                            <span class="abp_label"><?php esc_html_e( 'Title', 'abp-event-ticket' ); ?><sup class="_color_required">*</sup></span>
                            <input type="text" class="_form_control" name="timeline_title[]" data-pass="#timeline_title" placeholder="<?php esc_attr_e( 'EX: Registration Opens', 'abp-event-ticket' ); ?>" value="<?php echo esc_attr( $title ); ?>"/>
                        </label>
                        <div class="_fd_column_mar_t_xs">
                            <span class="abp_label"><?php esc_html_e( 'Description', 'abp-event-ticket' ); ?></span>
							<?php
								wp_editor(
									$description,
									$editor_id,
									array(
										'textarea_name' => 'timeline_description[]',
										'textarea_rows' => 5,
										'media_buttons' => true,
										'teeny'         => false,
										'quicktags'     => true
									)
								);
							?>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function save_settings( $post_id ): void {
				static $validating_status = false;
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
					$format_date         = fn( $date ) => $date ? gmdate( 'Y-m-d', strtotime( $date ) ) : '';
					$date_infos          = [];
					$time_info           = [];
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
					$ticket_ids = $post_array( 'ticket_name' );
					$price_data = $post_array( 'ticket_price' );
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
					$operation_time               = [];
					$operation_times              = $post_array( 'operation_time' );
					if ( ! empty( $operation_times ) ) {
						$operation_time = array_values( array_unique( array_filter( $operation_times ) ) );
						sort( $operation_time );
					}
					$time_info['time'] = ! empty( $operation_time ) ? $operation_time : [ '00:00' ];
					$opt_time          = $post_val( 'operation_time_optional' );
					$opt_time          = ! empty( $opt_time ) ? explode( ',', $opt_time ) : [];
					if ( in_array( 'day_wise_time', $opt_time ) ) {
						$day_keys = $post_val( 'operation_time_day_name' );
						$day_keys = ! empty( $day_keys ) ? explode( ',', $day_keys ) : [];
						foreach ( ABPET_Layout::week_day() as $key => $day ) {
							$times = $post_array( $key . '_time' );
							$times = array_filter( $times );
							if ( ! empty( $times ) && in_array( $key, $day_keys ) ) {
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
					if ( $date_infos['date_type'] === 'specific_date' && empty( $date_infos['specific_dates'] ) ) {
						$date_infos['date_type'] = 'periodic_date';
					}
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
						'event_type'                  => $post_val( 'event_type' ),
						'abpet_organizer'             => $post_val( 'abpet_organizer' ),
						'display_brand'               => $post_val( 'display_brand', 'off' ),
						'abpet_brand'                 => $post_val( 'abpet_brand' ),
						'display_capacity'            => $post_val( 'display_capacity', 'on' ),
						'display_category'            => $post_val( 'display_category', 'on' ),
						'abpet_category'              => $post_val( 'abpet_category' ),
						'display_location'            => $post_val( 'display_location', 'on' ),
						'abpet_location'              => $post_val( 'abpet_location' ),
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
					$validation_errors           = $this->validate_event_configuration( $post_id, $seat_type, $ticket_infos, $sp_infos, $date_infos, $time_info );
					if ( get_post_status( $post_id ) === 'publish' && ! empty( $validation_errors ) && ! $validating_status ) {
						$validating_status = true;
						wp_update_post( [ 'ID' => $post_id, 'post_status' => 'draft' ] );
						$validating_status = false;
						set_transient( 'abpet_event_configuration_errors_' . get_current_user_id(), $validation_errors, MINUTE_IN_SECONDS );
					}
					//=============tax================//
					if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) {
						$meta_info['_tax_status'] = $post_val( '_tax_status', 'none' );
						$meta_info['_tax_class']  = $post_val( '_tax_class' );
					}
					//=============================//
					if ( ABPET_Function::on_off( 'related' ) ) {
						$meta_info['related_item'] = $post_val( 'related_item' );
					}
					if ( ABPET_Function::on_off( 'feature' ) ) {
						$meta_info['post_feature'] = $post_val( 'post_feature' );
					}
					if ( ABPET_Function::on_off( 'timeline' ) ) {
						$times  = $post_array( 'timeline_time' );
						$titles = $post_array( 'timeline_title' );
						$descriptions = ( isset( $_POST['timeline_description'] ) && is_array( $_POST['timeline_description'] ) ) ? array_map( 'wp_kses_post', wp_unslash( $_POST['timeline_description'] ) ) : [];
						$timeline     = [];
						if ( ! empty( $titles ) ) {
							foreach ( $titles as $key => $title ) {
								$time_line_time = $times[ $key ] ?? '';
								if ( $title && $time_line_time ) {
									$timeline[ $key ] = [
										'time'  => $time_line_time,
										'title' => $title,
										'des'   => $descriptions[ $key ] ?? '',
									];
								}
							}
						}
						$meta_info['abpet_timeline'] = array_values( $timeline );
					}
					if ( ! ABPET_Function::on_off( 'speaker' ) ) {
						$meta_info['speaker'] = $post_val( 'speaker' );
					}
					//=============================//
					$meta_info = apply_filters( 'abpet_meta_info_update', $meta_info, $post_id );
					if ( sizeof( $meta_info ) > 0 ) {
						foreach ( $meta_info as $key => $value ) {
							update_post_meta( $post_id, sanitize_key( $key ), $value );
						}
					}
					foreach (
						[
							'abpet_category'  => 'abpet_category',
							'abpet_location'  => 'abpet_location',
							'abpet_organizer' => 'abpet_organizer',
							'abpet_brand'     => 'abpet_brand',
							'speaker'         => 'abpet_speaker',
						] as $meta_key => $taxonomy
					) {
						if ( taxonomy_exists( $taxonomy ) ) {
							$value = $meta_info[ $meta_key ] ?? '';
							$ids   = is_array( $value ) ? $value : explode( ',', (string) $value );
							$ids   = array_values( array_filter( array_map( 'absint', $ids ) ) );
							wp_set_object_terms( $post_id, $ids, $taxonomy, false );
						}
					}
				}
			}
			public function validate_event_configuration( $post_id, $seat_type, $ticket_infos, $sp_infos, $date_infos, $time_info ): array {
				$errors = [];
				if ( ! get_the_title( $post_id ) ) {
					$errors[] = __( 'Add an event title before publishing.', 'abp-event-ticket' );
				}
				$date_type = $date_infos['date_type'] ?? 'periodic_date';
				$dates     = [];
				if ( $date_type === 'specific_date' ) {
					$dates = $date_infos['specific_dates'] ?? [];
				} elseif ( ! empty( $date_infos['periodic_start_date'] ) ) {
					$dates[] = $date_infos['periodic_start_date'];
					if ( ! empty( $date_infos['periodic_end_date'] ) ) {
						$dates[] = $date_infos['periodic_end_date'];
					}
				} else {
					$dates[] = current_time( 'Y-m-d' );
				}
				$dates = array_filter( $dates, static function ( $date ) {
					return $date && strtotime( $date ) !== false;
				} );
				if ( empty( $dates ) ) {
					$errors[] = __( 'Configure at least one valid event date.', 'abp-event-ticket' );
				}
				$times = array_filter( $time_info['time'] ?? [], static function ( $time ) {
					return (bool) preg_match( '/^(?:[01]\d|2[0-3]):[0-5]\d$/', (string) $time );
				} );
				foreach ( $time_info['day_time'] ?? [] as $day_times ) {
					$times = array_merge( $times, array_filter( (array) $day_times ) );
				}
				foreach ( $time_info['date_times'] ?? [] as $date_time ) {
					$times = array_merge( $times, array_filter( (array) ( $date_time['time'] ?? [] ) ) );
				}
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce and capability are verified in the caller save_settings() before validation runs.
				if ( empty( $times ) || ( count( $times ) === 1 && reset( $times ) === '00:00' && empty( $_POST['operation_time'] ) ) ) {
					$errors[] = __( 'Configure at least one valid event session time.', 'abp-event-ticket' );
				}
				if ( $seat_type === 'ticket' ) {
					$valid_tickets = array_filter( $ticket_infos, static function ( $ticket ) {
						return isset( $ticket['price'], $ticket['qty'] ) && (float) $ticket['price'] >= 0 && (int) $ticket['qty'] > 0;
					} );
					if ( empty( $valid_tickets ) ) {
						$errors[] = __( 'Add at least one ticket type with a valid price and quantity.', 'abp-event-ticket' );
					}
				} elseif ( empty( $sp_infos ) ) {
					$errors[] = __( 'Assign at least one seat plan to this event.', 'abp-event-ticket' );
				}
				if ( empty( ABPET_Function::get_post_info( $post_id, 'link_wc_id' ) ) ) {
					$errors[] = __( 'The WooCommerce product could not be linked to this event.', 'abp-event-ticket' );
				}
				return array_unique( $errors );
			}
		}
		new ABPET_Post_Configuration();
	}