<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Dates' ) ) {
		class ABPET_Dates {
			public function __construct() {
				add_action( 'abpet_global_dates', array( $this, 'global_dates' ) );
				add_action( 'abpet_post_content', array( $this, 'post_content_dates' ) );
				add_action( 'wp_ajax_abpet_save_global_dates', array( $this, 'save_global_dates' ) );
			}
			public function global_dates(): void {
				?>
                <div class="global_dates">
					<?php $this->dates_config(); ?>
                </div>
				<?php
			}
			public function dates_config(): void {
				$date_infos = ABPET_Function::get_option( 'abpet_date_config' );
				//echo '<pre>';print_r($date_infos);echo '</pre>';
				$format_array = ABPET_Layout::array_date_format();
				$date_format  = $date_infos['date_format'] ?? 'D d M , yy';
				$time_format  = $date_infos['time_format'] ?? ABPET_Time_Format;
				?>
                <div class="abp_form">
                    <h4 class="_abp_gap_xs"><?php ABPET_Static::icon_svg( 'date_2' ); ?><?php esc_html_e( 'Global Dates Configuration', 'abp-event-ticket' ); ?></h4>
					<?php ABPET_Layout::info_text( 'abpet_dates' ); ?>
                    <div class="group_setting _mar_t_xs">
                        <div class="setting_item">
                            <label class="_f_wrap_fj_between_fa_center">
                                <span class="_abp_label"><?php esc_html_e( 'Date Format', 'abp-event-ticket' ); ?></span>
								<?php if ( sizeof( $format_array ) > 0 ) { ?>
                                    <select class="_form_control " name="date_format" required>
										<?php foreach ( $format_array as $key => $format ) { ?>
                                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $date_format, $key ) ?>><?php echo esc_html( $format ); ?></option>
										<?php } ?>
                                    </select>
								<?php } ?>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'date_format' ); ?>
                        </div>
                        <div class="setting_item">
                            <label class="_f_equal_f_wrap">
                                <span class="_abp_label"><?php esc_html_e( 'Time Format', 'abp-event-ticket' ); ?></span>
                                <input type="text" class="_form_control" name="time_format" placeholder="<?php echo esc_attr( ABPET_Time_Format ); ?>" value="<?php echo esc_attr( $time_format ); ?>" required/>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'time_format' ); ?>
                        </div>
                        <div class="setting_item">
                            <label class="_f_equal_f_wrap">
                                <span class="_mar_r_xs"><?php esc_html_e( 'Buffer time in MIN (Optional)', 'abp-event-ticket' ); ?></span>
                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="sale_close_before" placeholder="Ex: 15" value="<?php echo esc_attr( $date_infos['sale_close_before'] ?? 0 ); ?>"/>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'sale_close_before' ); ?>
                        </div>
                        <div class="setting_item">
                            <label class="_f_equal_f_wrap">
                                <span class="_abp_label"><?php esc_html_e( 'Number of advance booking date', 'abp-event-ticket' ); ?></span>
                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="advance_date_number" placeholder="Ex: 28" value="<?php echo esc_attr( $date_infos['advance_date_number'] ?? 28 ); ?>"/>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'advance_date_number' ); ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPET_Layout::button_global_save( 'global_dates', __( 'Save Global Date Configuration', 'abp-event-ticket' ) ); ?>
                </div>
				<?php
			}
			public function post_content_dates( $post_infos ): void {
				$date_infos      = $post_infos['abpet_dates'] ?? [];
				$time_infos      = $post_infos['time_infos'] ?? [];
				$operation_times = $time_infos['time'] ?? [];
				$day_times       = $time_infos['day_time'] ?? [];
				$date_times      = $time_infos['date_times'] ?? [];
				$date_type       = ( $date_infos['date_type'] ?? null ) ?: 'periodic_date';
				$specific_dates  = $date_infos['specific_dates'] ?? [];
				$opt_time = !empty($day_times) ? 'day_wise_time' : '';
				$opt_time = !empty($date_times) ? $opt_time .',date_wise_time' : $opt_time;
				// echo '<pre>';print_r($time_infos);echo '</pre>';
				//echo '<pre>';print_r($time_infos);echo '</pre>';
				?>
                <div class="tab_item date_configuration" data-tabs="#abpet_dates">
                    <h4 class="_abp_color_theme_gap_xxs">🗓️<?php esc_html_e( 'Date Configuration', 'abp-event-ticket' ); ?></h4>
                    <div class="_divider_xxs"></div>
                    <div class="_mar_t_xs group_setting">
                        <div class="setting_item">
                            <div class=" _fj_between">
                                <span class="_abp_label"><?php esc_html_e( 'Date Type', 'abp-event-ticket' ); ?><sup class="_color_required">*</sup></span>
                                <div class="custom_radio _group_content">
                                    <input type="hidden" class="_form_control" name="date_type" value="<?php echo esc_attr( $date_type ); ?>"/>
                                    <div class="radio_item">
                                        <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $date_type == 'specific_date' ? 'abp_active' : '' ); ?>" data-close-target="#specific_date" data-radio="specific_date" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                            <span data-icon class="<?php echo esc_attr( $date_type == 'specific_date' ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php esc_html_e( 'Specific Dates', 'abp-event-ticket' ); ?>
                                        </button>
                                    </div>
                                    <div class="radio_item">
                                        <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $date_type == 'periodic_date' ? 'abp_active' : '' ); ?>" data-close-target="#periodic_date" data-radio="periodic_date" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                            <span data-icon class=" <?php echo esc_attr( $date_type == 'periodic_date' ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php esc_html_e( 'Periodic Dates', 'abp-event-ticket' ); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'date_type' ); ?>
                        </div>
                        <div class="setting_item <?php echo esc_attr( $date_type == 'periodic_date' ? 'abp_active' : '' ); ?>" data-close="#periodic_date">
                            <label class="_f_wrap_fj_between_fa_center">
                                <span class="_mar_r_xs"><?php esc_html_e( 'Periodic after', 'abp-event-ticket' ); ?></span>
                                <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="periodic_after" placeholder="Ex: 5" value="<?php echo esc_attr( $date_infos['periodic_after'] ?? 1 ); ?>"/>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'periodic_after' ); ?>
                        </div>
                        <div class="setting_item <?php echo esc_attr( $date_type == 'periodic_date' ? 'abp_active' : '' ); ?>" data-close="#periodic_date">
                            <div class="_f_wrap_fj_between_fa_center">
                                <span class="_abp_label"><?php esc_html_e( 'Launching Date (Optional)', 'abp-event-ticket' ); ?></span>
								<?php ABPET_Layout::input_date( 'periodic_start_date', ( $date_infos['periodic_start_date'] ?? '' ) ); ?>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'periodic_start_date' ); ?>
                        </div>
                        <div class="setting_item <?php echo esc_attr( $date_type == 'periodic_date' ? 'abp_active' : '' ); ?>" data-close="#periodic_date">
                            <div class="_f_wrap_fj_between_fa_center">
                                <span class="_abp_label"><?php esc_html_e( 'Terminate Date (Optional)', 'abp-event-ticket' ); ?></span>
								<?php ABPET_Layout::input_date( 'periodic_end_date', ( $date_infos['periodic_end_date'] ?? '' ) ); ?>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'periodic_end_date' ); ?>
                        </div>
                        <div class="setting_item full_width configuration_content <?php echo esc_attr( $date_type == 'specific_date' ? 'abp_active' : '' ); ?>" data-close="#specific_date">
                            <div class="_f_wrap_fj_between_fa_center">
                                <span class="_abp_label"><?php esc_html_e( 'Specific Dates', 'abp-event-ticket' ); ?></span>
								<?php ABPET_Layout::button_add( __( 'Add Specific Date', 'abp-event-ticket' ) ); ?>
                            </div>
                            <div class="_divider_xs"></div>
                            <div class="insertable_area sortable_area _f_wrap_gap_xs">
								<?php
									if ( sizeof( $specific_dates ) ) {
										foreach ( $specific_dates as $specific_date ) {
											if ( ! empty( $specific_date ) ) {
												$this->date_item( 'specific_dates[]', $specific_date );
											}
										}
									}
								?>
                            </div>
                            <div class="abp_hidden">
                                <div class="hidden_content">
									<?php $this->date_item( 'specific_dates[]' ); ?>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'specific_dates' ); ?>
                        </div>
                    </div>
					<?php $this->special_on_off_dates( $date_infos ); ?>
                    <div class="_divider_xs"></div>
                    <h4 class="_abp_color_theme_gap_xxs">⏰<?php esc_html_e( 'Time Configuration', 'abp-event-ticket' ); ?></h4>
                    <div class="_divider_xxs"></div>
                    <div class="group_setting">
                        <div class="setting_item full_width">
                            <div class=" configuration_content">
                                <div class="_f_wrap_fj_between_fa_center_mar_b_xxs">
                                    <span class="_abp_label"><?php esc_html_e( 'Operation Time', 'abp-event-ticket' ); ?></span>
                                    <div class="_group_content custom_checkbox">
                                        <input type="hidden" name="operation_time_optional" value="<?php echo esc_attr($opt_time); ?>"/>
                                        <div class="checkbox_item">
                                            <button type="button" class="_btn_light_info_xs <?php echo esc_attr(!empty($day_times) ? 'abp_active' : ''); ?>" data-collapse-target="#day_wise_time" data-checked="day_wise_time" data-open-icon="fa-check-square" data-close-icon="fa-square">
                                                <span data-icon class="_mar_r_xs far <?php echo esc_attr(!empty($day_times) ? 'fa-check-square' : 'fa-square'); ?>"></span><?php esc_html_e('Day Wise Time', 'abp-event-ticket'); ?>
                                            </button>
                                        </div>
                                        <div class="checkbox_item">
                                            <button type="button" class="_btn_light_info_xs <?php echo esc_attr(!empty($date_times) ? 'abp_active' : ''); ?>" data-collapse-target="#date_wise_time" data-checked="date_wise_time" data-open-icon="fa-check-square" data-close-icon="fa-square">
                                                <span data-icon class="_mar_r_xs far <?php echo esc_attr(!empty($date_times) ? 'fa-check-square' : 'fa-square'); ?>"></span><?php esc_html_e('Date Wise Time', 'abp-event-ticket'); ?>
                                            </button>
                                        </div>
                                    </div>
	                                <?php ABPET_Layout::button_add(__('Add Operation Time', 'abp-event-ticket'));?>
                                </div>
								<?php ABPET_Layout::info_text( 'operation_time' ); ?>
                                <div class="_divider_xs"></div>
                                <div class="_f_wrap_f_equal_f_gap_xxs">
                                    <div class="insertable_area sortable_area _f_wrap_gap_xs">
										<?php
											$time_exit = 0;
											if ( ! empty( $operation_times ) ) {
												foreach ( $operation_times as $times ) {
													if ( ! empty( $times ) ) {
														$this->time_item( 'operation_time[]', $times);
														$time_exit ++;
													}
												}
											}
											if ( $time_exit == 0 ) {
												$this->time_item( 'operation_time[]', '' );
											}
										?>
                                    </div>
                                    <div class="abp_hidden">
                                        <div class="hidden_content">
											<?php $this->time_item( 'operation_time[]' ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<?php $this->day_wise_time( $day_times ); ?>
							<?php $this->date_wise_time( $date_times ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function save_global_dates(): void {
				if ( ! check_ajax_referer( 'abpet_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-event-ticket' ), 'type' => 'warn' ], 403 );
				}
				$post_val                           = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
				$date_config['date_format']         = $post_val( 'date_format' );
				$date_config['time_format']         = $post_val( 'time_format' );
				$date_config['advance_date_number'] = $post_val( 'advance_date_number', '28' );
				$date_config['sale_close_before']   = $post_val( 'sale_close_before' );
				update_option( 'abpet_date_config', $date_config );
				ob_start();
				$this->dates_config();
				$html = ob_get_clean();
				wp_send_json_success( [
					'html' => $html,
					'msg'  => __( 'Global Date Configuration Saved Successfully ..... !!', 'abp-event-ticket' ),
					'type' => 'success'
				] );
			}
			public function special_on_off_dates( $date_infos = [] ): void {
				$date_rule       = $date_infos['date_rule'] ?? '';
				$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
				$date_type       = ( $date_infos['date_type'] ?? null ) ?: 'periodic_date';
				$weekend         = $date_infos['weekend'] ?? '';
				$weekend_array   = $weekend ? explode( ',', $weekend ) : [];
				$days            = ABPET_Layout::week_day();
				$date_rules      = ABPET_Layout::date_option_rules();
				?>
                <div class="<?php echo esc_attr( $date_type == 'periodic_date' ? 'abp_active' : '' ); ?>" data-close="#periodic_date">
                    <div class="group_setting _mar_t_xs">
                        <div class="setting_item full_width">
                            <div class="_fj_between _mar_t_xs">
                                <span class="_abp_label"><?php esc_html_e( 'Special On/Off Date(optional)', 'abp-event-ticket' ); ?></span>
                                <div class="custom_checkbox _group_content">
                                    <input type="hidden" name="date_rule" value="<?php echo esc_attr( $date_rule ); ?>"/>
									<?php foreach ( $date_rules as $key => $rule ) { ?>
                                        <div class="checkbox_item _min_100">
                                            <button type="button" class="_btn_light_info_xs <?php echo esc_attr( in_array( $key, $date_rule_array, true ) ? 'abp_active' : '' ); ?>" data-collapse-target="#<?php echo esc_attr( $key ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="fa-check-square" data-close-icon="fa-square">
                                                <span data-icon class="far <?php echo esc_attr( in_array( $key, $date_rule_array, true ) ? 'far fa-check-square' : 'fa-square' ); ?>"></span><?php echo esc_html( $rule ); ?>
                                            </button>
                                        </div>
									<?php } ?>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'date_rule' ); ?>
                        </div>
                        <div class="setting_item full_width <?php echo esc_attr( in_array( 'weekend', $date_rule_array, true ) ? 'abp_active' : '' ); ?> " data-collapse="#weekend">
                            <div class="_f_wrap_fj_between_fa_center">
                                <span class="_abp_label"><?php esc_html_e( 'Weekend(optional)', 'abp-event-ticket' ); ?></span>
                                <div class="custom_checkbox _group_content">
                                    <input type="hidden" name="weekend" value="<?php echo esc_attr( $weekend ); ?>"/>
									<?php foreach ( $days as $key => $day ) { ?>
                                        <div class="checkbox_item _min_100">
                                            <button type="button" class="_btn_light_info_xs <?php echo esc_attr( in_array( $key, $weekend_array ) ? 'abp_active' : '' ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                                <span data-icon class="<?php echo esc_attr( in_array( $key, $weekend_array ) ? 'far fa-check-square' : 'far fa-square' ); ?>"></span><?php echo esc_html( $day ); ?>
                                            </button>
                                        </div>
									<?php } ?>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'weekend' ); ?>
                        </div>
                        <div class="setting_item configuration_content <?php echo esc_attr( in_array( 'specific_off_dates', $date_rule_array, true ) ? 'abp_active' : '' ); ?>" data-collapse="#specific_off_dates">
                            <div class="_fj_between_fa_center">
                                <span class="_abp_label"><?php esc_html_e( 'Specific Off Dates(optional)', 'abp-event-ticket' ); ?></span>
								<?php ABPET_Layout::button_add( __( 'Add Specific Off Date', 'abp-event-ticket' ) ); ?>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'specific_off_dates' ); ?>
                            <div class="insertable_area sortable_area _f_wrap_gap_xs_mar_t_xs">
								<?php $specific_off_dates = $date_infos['specific_off_dates'] ?? [];
									if ( sizeof( $specific_off_dates ) ) {
										foreach ( $specific_off_dates as $specific_date ) {
											if ( $specific_date ) {
												$this->date_item( 'specific_off_dates[]', $specific_date );
											}
										}
									}
								?>
                            </div>
                            <div class="abp_hidden">
                                <div class="hidden_content">
									<?php $this->date_item( 'specific_off_dates[]' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="setting_item configuration_content  <?php echo esc_attr( in_array( 'special_on_dates', $date_rule_array, true ) ? 'abp_active' : '' ); ?>" data-collapse="#special_on_dates">
                            <div class="_fj_between_fa_center">
                                <span class="_abp_label"><?php esc_html_e( 'Special On Dates (optional)', 'abp-event-ticket' ); ?></span>
								<?php ABPET_Layout::button_add( __( 'Add Special On Dates', 'abp-event-ticket' ) ); ?>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'special_on_dates' ); ?>
                            <div class="insertable_area sortable_area _f_wrap_gap_xs_mar_t_xs">
								<?php $special_dates = $date_infos['special_on_dates'] ?? [];
									if ( sizeof( $special_dates ) ) {
										foreach ( $special_dates as $specific_date ) {
											if ( ! empty( $specific_date ) ) {
												$this->date_item( 'special_on_dates[]', $specific_date );
											}
										}
									}
								?>
                            </div>
                            <div class="abp_hidden">
                                <div class="hidden_content">
									<?php $this->date_item( 'special_on_dates[]' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="setting_item configuration_content <?php echo esc_attr( in_array( 'off_date_range', $date_rule_array, true ) ? 'abp_active' : '' ); ?>" data-collapse="#off_date_range">
                            <div class="_fj_between_fa_center">
                                <span class="_abp_label"><?php esc_html_e( 'Off Date Range(optional)', 'abp-event-ticket' ); ?></span>
								<?php ABPET_Layout::button_add( __( 'Add Off Date Range', 'abp-event-ticket' ) ); ?>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPET_Layout::info_text( 'off_date_range' ); ?>
                            <div class="insertable_area sortable_area _f_wrap_gap_xs_mar_t_xs">
								<?php $off_date_range = $date_infos['off_date_range'] ?? [];
									if ( sizeof( $off_date_range ) ) {
										foreach ( $off_date_range as $specific_date ) {
											if ( sizeof( $specific_date ) > 0 && $specific_date['from'] && $specific_date['to'] ) {
												$this->off_day_range( $specific_date['from'], $specific_date['to'] );
											}
										}
									}
								?>
                            </div>
                            <div class="abp_hidden">
                                <div class="hidden_content">
									<?php $this->off_day_range(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function day_wise_time( $day_times = [], $prefix = '' ): void {
				$days = ABPET_Layout::week_day();
				?>
                <div class="full_width  <?php echo esc_attr( ! empty( $day_times ) ? 'abp_active' : '' ); ?>" data-collapse="#<?php echo esc_attr( $prefix ); ?>day_wise_time">
                    <div class="_divider_xxs"></div>
                    <div class="_fj_between _fa_center">
                        <span class="_abp_label"><?php esc_html_e( 'Day Wise Operation Time (Optional) ', 'abp-event-ticket' ); ?></span>
                        <div class="_group_content custom_checkbox">
							<?php foreach ( $days as $key => $day ) { ?>
                                <div class="checkbox_item">
                                    <button type="button" class="_btn_light_info_xs <?php echo esc_attr( array_key_exists( $key, $day_times ) ? 'abp_active' : '' ); ?>" data-collapse-target="#<?php echo esc_attr( $prefix . $key ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="fa-check-square" data-close-icon="fa-square">
                                        <span data-icon class="far <?php echo esc_attr( array_key_exists( $key, $day_times ) ? 'far fa-check-square' : 'fa-square' ); ?>"></span><?php echo esc_html( $day ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php ABPET_Layout::info_text( 'day_wise_time' ); ?>
					<?php foreach ( $days as $key => $day ) {
						$operation_times = $day_times[ $key ] ?? [];
						?>
                        <div class="configuration_content <?php echo esc_attr( array_key_exists( $key, $day_times ) ? 'abp_active' : '' ); ?>" data-collapse="#<?php echo esc_attr( $prefix . $key ); ?>">
                            <div class="_divider_xs"></div>
                            <div class="insertable_area sortable_area _f_wrap_gap_xs">
								<?php ABPET_Layout::button_add( __( 'Operation Time : ', 'abp-event-ticket' ) . $day ); ?>
								<?php
									if ( ! empty( $operation_times ) ) {
										foreach ( $operation_times as $times ) {
											if ( ! empty( $times ) ) {
												$this->time_item( $prefix . $key . '_time[]', $times );
											}
										}
									}
								?>
                            </div>
                            <div class="abp_hidden">
                                <div class="hidden_content">
									<?php $this->time_item( $prefix . $key . '_time[]' ); ?>
                                </div>
                            </div>
                        </div>
					<?php } ?>
                </div>
				<?php
			}
			public function date_wise_time( $date_times = [], $prefix = '' ): void {
				?>
                <div class="full_width configuration_content   <?php echo esc_attr( ! empty( $date_times ) ? 'abp_active' : '' ); ?>" data-collapse="#<?php echo esc_attr( $prefix ); ?>date_wise_time">
                    <div class="_divider_xxs"></div>
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_abp_label"><?php esc_html_e( 'Date Wise Operation Time (Optional) ', 'abp-event-ticket' ); ?></span>
						<?php ABPET_Layout::button_add( __( 'Add New Date Wise Operation Time', 'abp-event-ticket' ) ); ?>
                    </div>
					<?php ABPET_Layout::info_text( 'date_wise_time' ); ?>
                    <div class="insertable_area sortable_area">
						<?php if ( ! empty( $date_times ) ) {
							foreach ( $date_times as $key => $date_time ) {
								$this->date_wise_time_item( $date_time, $key, $prefix );
							}
						} ?>
                    </div>
                    <div class="abp_hidden" data-hidden_id>
                        <div class="hidden_content">
							<?php $this->date_wise_time_item( [], uniqid( 'abp_' ), $prefix ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
			//=============================//
			public function off_day_range( $from_date = '', $to_date = '' ): void {
				?>
                <div class="delete_area _group_content">
					<?php
						ABPET_Layout::button_sort();
						ABPET_Layout::input_date( 'abpet_off_from[]', $from_date );
						ABPET_Layout::input_date( 'abpet_off_to[]', $to_date );
						ABPET_Layout::button_delete();
					?>
                </div>
				<?php
			}
			public function date_item( $name, $date = '' ): void {
				?>
                <div class="delete_area _group_content">
					<?php
						ABPET_Layout::button_sort();
						ABPET_Layout::input_date( $name, $date );
						ABPET_Layout::button_delete();
					?>
                </div>
				<?php
			}
			public function time_item( $name, $time = '', $required = '' ): void {
				?>
                <div class="delete_area _group_content">
					<?php
						ABPET_Layout::button_sort();
						ABPET_Layout::input_time( $name, $time, '', $required );
						ABPET_Layout::button_delete();
					?>
                </div>
				<?php
			}
			public function date_wise_time_item( $date_time = [], $key = '', $prefix = '' ): void {
				$times = $date_time['time'] ?? [];
				?>
                <div class="configuration_content delete_area">
                    <input type="hidden" name="<?php echo esc_attr( $prefix ); ?>date_wise_time_id[]" class="hidden_id" value="<?php echo esc_attr( $key ); ?>">
                    <div class="_divider_xs"></div>
                    <div class="_fa_start_gap_xs">
                        <div class="_group_content">
							<?php
								ABPET_Layout::button_sort();
								ABPET_Layout::input_date( $prefix . 'date_wise_date[' . $key . '][]', ( $date_time['date'] ?? '' ) );
								ABPET_Layout::button_delete();
							?>
                        </div>
						<?php ABPET_Layout::button_add( __( 'Add Operation Time ', 'abp-event-ticket' ) ); ?>
                        <div class="insertable_area sortable_area _f_wrap_fa_gap_xs">
							<?php if ( ! empty( $times ) ) {
								foreach ( $times as $time ) {
									if ( ! empty( $time ) ) {
										$this->time_item( $prefix . 'date_wise_time[' . $key . '][]', $time );
									}
								}
							} ?>
                        </div>
                    </div>
                    <div class="abp_hidden">
                        <div class="hidden_content">
							<?php $this->time_item( $prefix . 'date_wise_time[' . $key . '][]' ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
		}
		new ABPET_Dates();
	}