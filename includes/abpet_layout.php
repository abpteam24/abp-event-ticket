<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Layout' ) ) {
		class ABPET_Layout {
			public function __construct() {
				add_action( 'abpet_load_date_picker', [ $this, 'load_date_picker' ], 10, 2 );
				//==============================//
				add_action( 'abpet_add_icon', array( $this, 'load_icon' ), 10, 2 );
				add_action( 'abpet_add_image_multiple', array( $this, 'add_image_multi' ), 10, 2 );
				add_action( 'abpet_add_image_icon', array( $this, 'selection_icon_image' ), 10, 3 );
				add_action( 'abpet_image_selection', array( $this, 'image_selection' ), 10, 3 );
			}
			public function load_date_picker( $selector, $dates ): void {
				if ( empty( $dates ) || ! is_array( $dates ) ) {
					return;
				}
				$picker_data   = self::create_datepicker_array( $dates );
				$json_selector = wp_json_encode( sanitize_text_field( $selector ) );
				$json_data     = wp_json_encode( $picker_data );
				$inline_js     = "window.abpet_picker_data = window.abpet_picker_data || {}; window.abpet_picker_data[{$json_selector}] = {$json_data};";
				wp_add_inline_script( 'jquery-ui-datepicker', $inline_js );
			}
			public static function create_datepicker_array( $dates ): array {
				$start_date  = current( $dates );
				$start_year  = (int) gmdate( 'Y', strtotime( $start_date ) );
				$start_month = (int) ( gmdate( 'n', strtotime( $start_date ) ) - 1 );
				$start_day   = (int) gmdate( 'j', strtotime( $start_date ) );
				$end_date    = end( $dates );
				$end_year    = (int) gmdate( 'Y', strtotime( $end_date ) );
				$end_month   = (int) ( gmdate( 'n', strtotime( $end_date ) ) - 1 );
				$end_day     = (int) gmdate( 'j', strtotime( $end_date ) );
				$all_dates   = [];
				foreach ( $dates as $date ) {
					$all_dates[] = gmdate( 'j-n-Y', strtotime( $date ) );
				}
				return [
					'minYear'     => $start_year,
					'minMonth'    => $start_month,
					'minDay'      => $start_day,
					'maxYear'     => $end_year,
					'maxMonth'    => $end_month,
					'maxDay'      => $end_day,
					'activeDates' => $all_dates,
					'txtAvail'    => esc_js( __( 'Available', 'abp-event-ticket' ) ),
					'txtUnavail'  => esc_js( __( 'Unavailable', 'abp-event-ticket' ) )
				];
			}
			//==============================//
			public static function load_admin_globally(): void {
				?>
                <div class="abp_popup " data-popup="#abpet_global_popup">
                    <div class="popup_area">
                        <span class="close_icon" onclick="abpet_popup_close_global()"><i class="fas fa-times"></i></span>
                        <div class="popup_body"></div>
                    </div>
                </div>
                <div class="popup_icon abp_popup" data-popup="#abpet_popup_icon">
                    <div class="popup_area">
                        <div class="popup_head _all_center">
                            <div class="abp_dropdown _max_400">
                                <label class="abp_all_center">
                                    <input type="hidden" class="abp_icon_search_hidden" name="abp_icon_search" value=""/>
                                    <input type="text" class="_form_control_text_center validation_name abpet_allow abp_icon_search" name="" placeholder="<?php esc_attr_e( 'Search  icon', 'abp-event-ticket' ); ?>" value=""/>
                                </label>
                                <div class="dropdown_list"></div>
                            </div>
                            <span class="popup_close"><i class="fas fa-times"></i></span>
                        </div>
                        <div class="popup_body">
                            <h4 class="abp_text_center item_icon_title"></h4>
                            <div class="item_icon_area"></div>
                        </div>
                    </div>
                </div>
				<?php
			}
			//==============================//
			public static function button_add( $button_text, $class = '', $button_class = '' ): void {
				$class        = $class ?: 'add_new_hook';
				$button_class = $button_class ?: '_btn_light_active_xs';
				$button_text  = $button_text ?: __( 'Add New', 'abp-event-ticket' );
				?>
                <button class="<?php echo esc_attr( $button_class . ' ' . $class ); ?>" type="button">
					<?php ABPET_Static::icon_svg( 'plus' );
						echo esc_html( $button_text ); ?>
                </button>
				<?php
			}
			public static function button_delete_sort_edit(): void {
				?>
                <div class="_all_center">
                    <div class="_group_content">
						<?php
							self::button_edit();
							self::button_sort();
							self::button_delete();
						?>
                    </div>
                </div>
				<?php
			}
			public static function button_delete_sort(): void {
				?>
                <div class="_all_center">
                    <div class="_group_content">
						<?php
							self::button_sort();
							self::button_delete();
						?>
                    </div>
                </div>
				<?php
			}
			public static function button_edit( $class_edit = 'edit_hook' ): void {
				?>
                <button class="_btn_light_navy_blue_xs <?php echo esc_attr( $class_edit ); ?>" type="button" title="<?php esc_attr_e( 'Edit This Item', 'abp-event-ticket' ); ?>">
					<?php ABPET_Static::icon_svg( 'edit' ); ?>
                </button>
				<?php
			}
			public static function button_delete( $class = 'delete_hook' ): void {
				?>
                <button class="_btn_light_danger_xxs <?php echo esc_attr( $class ); ?>" type="button" title="<?php esc_attr_e( 'Delete This Item', 'abp-event-ticket' ); ?>"><?php ABPET_Static::icon_svg( 'close_1' ); ?></button>
				<?php
			}
			public static function button_sort(): void {
				?>
                <div class="_btn_light_info_xxs sortable_handle" type="button" title="<?php esc_attr_e( 'Move This Item', 'abp-event-ticket' ); ?>">
					<?php ABPET_Static::icon_svg( 'drag' ); ?>
                </div>
				<?php
			}
			public static function button_global_save( $action, $text = '', $class = '' ): void {
				if ( ! empty( $action ) ) {
					$class = $class ?: '_btn_theme_xs';
					$text  = $text ?: __( 'save', 'abp-event-ticket' );
					?>
                    <button class="<?php echo esc_attr( $class ); ?>" type="button" onclick="abpet_save_global('<?php echo esc_attr( $action ); ?>',this)">
						<?php ABPET_Static::icon_svg( 'save' );
							echo esc_html( $text ); ?>
                    </button>
					<?php
				}
			}
			public static function button_global_popup( $action, $text = '', $class = '' ): void {
				if ( ! empty( $action ) ) {
					$class = $class ?: '_btn_light_active_xs';
					$text  = $text ?: __( 'Add New', 'abp-event-ticket' );
					?>
                    <button type="button" class="<?php echo esc_attr( $class ) ?>" onclick="abpet_popup_open_global('<?php echo esc_attr( $action ); ?>')">
						<?php ABPET_Static::icon_svg( 'plus' );
							echo esc_html( $text ); ?>
                    </button>
					<?php
				}
			}
			//=============================//
			public static function info_text( $key = '', $data = '' ): void {
				$data = empty( $data ) ? ABPET_Static::array_info( $key ) : $data;
				if ( $data ) {
					?>
                    <div class="info_text load_more">
                        ℹ️ &nbsp;<?php echo wp_kses_post( $data ); ?>
                        <span class="load_more_action" data-less="<?php esc_html_e( '.... Less ', 'abp-event-ticket' ); ?>" data-more="<?php esc_html_e( '.... More', 'abp-event-ticket' ); ?>"><?php esc_html_e( '.... More', 'abp-event-ticket' ); ?></span>
                    </div>
					<?php
				}
			}
			public static function load_more( $data = '' ): void {
				if ( $data ) {
					?>
                    <div class="load_more">
						<?php echo wp_kses_post( $data ); ?>
                        <span class="load_more_action" data-less="<?php esc_html_e( '.... Less ', 'abp-event-ticket' ); ?>" data-more="<?php esc_html_e( '.... More', 'abp-event-ticket' ); ?>"><?php esc_html_e( '.... More', 'abp-event-ticket' ); ?></span>
                    </div>
					<?php
				}
			}
			public static function layout_warning_info( $key ): void {
				$data = ABPET_Static::array_info( $key );
				if ( $data ) {
					echo '<div class="_section_bg_warning_mar_zero"><h4 class="abp_text_center_color_white">' . esc_html( $data ) . '</h4></div>';
				}
			}
			public static function layout_warning_info_xs( $key, $data = '' ): void {
				$data = empty( $data ) ? ABPET_Static::array_info( $key ) : $data;
				if ( $data ) {
					echo '<div class="abp_text_center_color_white_bg_warning_padding_xxs_fs_label">' . esc_html( $data ) . '</div>';
				}
			}
			public static function layout_info_xs( $key, $data = '' ): void {
				$data = empty( $data ) ? ABPET_Static::array_info( $key ) : $data;
				if ( $data ) {
					echo '<div class="abp_bg_info_padding_xxs _all_center _color_5">' . esc_html( $data ) . '</div>';
				}
			}
			public static function on(): bool|string {
				ob_start();
				?>
                <strong class="abp_color_theme"> <?php esc_html_e( 'ON', 'abp-event-ticket' ); ?></strong>
				<?php
				return ob_get_clean();
			}
			public static function off(): bool|string {
				ob_start();
				?>
                <strong class="abp_color_theme"> <?php esc_html_e( 'OFF', 'abp-event-ticket' ); ?></strong>
				<?php
				return ob_get_clean();
			}
			//==============Input field===============//
			public static function quantity_input( $input_info = [] ): void {
				$name        = $input_info['name'] ?? '';
				$price       = floatval( $input_info['price'] ?? 0 );
				$min_qty     = absint( $input_info['min_qty'] ?? 1 );
				$max_qty     = absint( $input_info['max_qty'] ?? 1 );
				$class       = $input_info['class'] ?? '';
				$collapse_id = $input_info['collapse_id'] ?? '';
				if ( $name && $max_qty >= $min_qty ) {
					if ( ! empty( $collapse_id ) ) {
						?> <div data-collapse="<?php echo esc_attr( $collapse_id ); ?>"><?php
					}
					?>
                    <div class="_group_content qty_input">
                        <div class="qty_decrease _ag_content"><?php ABPET_Static::icon_svg( 'minus_1' ); ?></div>
                        <label>
                            <input type="text" class="_form_control  validation_number <?php echo esc_attr( $class ); ?>"
                                   name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $min_qty ); ?>"
                                   data-price="<?php echo esc_attr( $price ); ?>" data-min="<?php echo esc_attr( $min_qty ); ?>" data-max="<?php echo esc_attr( $max_qty ); ?>"
                            />
                        </label>
                        <div class="qty_increase _ag_content"><?php ABPET_Static::icon_svg( 'plus' ); ?></div>
                    </div>
					<?php
					if ( ! empty( $collapse_id ) ) {
						?></div><?php
					}
				}
			}
			public static function switch_checkbox( $name, $value = '' ): void {
				$value = in_array( $value, [ 'on', 'off', '' ], true ) ? $value : '';
				?>
                <div class="<?php echo esc_attr( $value === 'on' ? 'abp_active' : '' ); ?>" data-switch data-collapse-target="#<?php echo esc_attr( $name ); ?>">
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
                </div>
				<?php
			}
			public static function input_title( $label = '', $required = '' ): void {
				if ( $label ) { ?>
                    <span class="_mar_b_xxs">
							<?php echo esc_html( $label ); ?>
						<?php if ( $required ) { ?>
                            <sup class="_color_required">*</sup>
						<?php } ?>
						</span>
					<?php
				}
			}
			public static function input_date( $name, $date = '', $label = '', $required = '' ): void {
				$now          = ABPET_Function::date_format( current_time( 'Y-m-d' ) );
				$hidden_date  = $date ? gmdate( 'Y-m-d', strtotime( $date ) ) : '';
				$visible_date = $date ? ABPET_Function::date_format( $date, 'date' ) : '';
				?>
                <label class="_input_item">
					<?php self::input_title( $label, $required ); ?>
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $hidden_date ); ?>" <?php echo esc_attr( $required ); ?>/>
                    <input type="text" name="" class="_form_control abp_datepicker" value="<?php echo esc_attr( $visible_date ); ?>" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                    <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-event-ticket' ); ?>"></span>
                </label>
				<?php
			}
			public static function input_time( $name, $time = '', $label = '', $required = '' ): void {
				?>
                <label class="_input_item">
					<?php self::input_title( $label, $required ); ?>
                    <input type="time" class="_form_control" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $time ); ?>" <?php echo esc_attr( $required ); ?>/>
                    <span class="fas fa-times time_close_icon" title="<?php esc_attr_e( 'Clear Time', 'abp-event-ticket' ); ?>"></span>
                </label>
				<?php
			}
			public static function textarea( $name, $value = '', $label = '', $required = '' ): void {
				?>
                <label class="abpet_textarea _input_item">
					<?php self::input_title( $label, $required ); ?>
                    <textarea name="<?php echo esc_attr( $name ); ?>" rows="3" class="_form_control" placeholder="<?php echo esc_attr( $label ); ?>" title="<?php echo esc_attr( $label ); ?>"  <?php echo esc_attr( $required ); ?>><?php echo esc_textarea( $value ); ?></textarea>
                </label>
				<?php
			}
			public static function select( $name, $value = '', $label = '', $required = '', $options = [] ): void {
				if ( is_array( $options ) && sizeof( $options ) > 0 ) {
					?>
                    <label class="_input_item">
						<?php self::input_title( $label, $required ); ?>
                        <select name="<?php echo esc_attr( $name ); ?>" class="_form_control" title="<?php echo esc_attr( $label ); ?>" <?php echo esc_attr( $required ); ?>>
                            <option value="" disabled selected><?php echo esc_html__( 'Please Select', 'abp-event-ticket' ) . ' ' . esc_html( $label ); ?></option>
							<?php foreach ( $options as $option ) { ?>
                                <option value="<?php echo esc_attr( $option ); ?>" <?php echo esc_attr( $option == $value ? 'selected' : '' ); ?>><?php echo esc_html( $option ); ?></option>
							<?php } ?>
                        </select>
                    </label>
					<?php
				}
			}
			public static function checkbox( $name, $value = '', $label = '', $required = '', $options = [] ): void {
				if ( is_array( $options ) && sizeof( $options ) > 0 ) {
					?>
                    <div class=" _input_item">
                        <span class="_fs_label"> <?php self::input_title( $label, $required ); ?></span>
                        <div class="custom_checkbox">
                            <input type="hidden" class="_form_control" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
							<?php foreach ( $options as $option ) { ?>
                                <div class="checkbox_item">
                                    <button type="button" class="_btn_white_xs <?php echo esc_attr( $option == $value ? 'abp_active' : '' ); ?>" data-checked="<?php echo esc_attr( $option ); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr( $option == $value ? 'far fa-check-square' : 'far fa-square' ); ?>"></span><?php echo esc_html( $option ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}
			}
			public static function radio( $name, $value = '', $label = '', $required = '', $options = [] ): void {
				if ( is_array( $options ) && sizeof( $options ) > 0 ) {
					?>
                    <div class=" _input_item">
                        <span class="_fs_label"> <?php self::input_title( $label, $required ); ?></span>
                        <div class="custom_radio">
                            <input type="hidden" class="_form_control" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
							<?php foreach ( $options as $option ) { ?>
                                <div class="radio_item">
                                    <button type="button" class="_btn_white_xs <?php echo esc_attr( $option == $value ? 'abp_active' : '' ); ?>" data-radio="<?php echo esc_attr( $option ); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr( $option == $value ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php echo esc_html( $option ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}
			}
			//=============Add  Image / Icon================//
			public static function image( $post_id = '', $image_id = '', $url = '', $class = '' ): void {
				$image_url = ( $post_id > 0 || $image_id ) ? ABPET_Function::get_image_url( $post_id, $image_id ) : $url;
				$post_url  = $post_id > 0 ? get_the_permalink( $post_id ) : '';
				$image_url = $image_url ?: ABPET_BLANK_IMG_URL;
				if ( $image_url ) {
					?>
                    <div class="abp_image <?php echo esc_attr( $class ); ?>" data-image-href="<?php echo esc_url( $image_url ); ?>" <?php if ( ! empty( $post_url ) ) { ?> data-href="<?php echo esc_url( $post_url ); ?>" <?php } ?> >
                        <img class="_img_control" src="#" alt="<?php echo esc_attr( max( $post_id, $image_id ) ); ?>">
                    </div>
					<?php
				}
			}
			public static function image_icon( $icon_image, $class = '' ): void {
				if ( ! empty( $icon_image ) ) {
					$icon = $image = $emoji = '';
					if ( is_numeric( $icon_image ) ) {
						$image = $icon_image;
					} elseif ( preg_match( '/\s/', $icon_image ) ) {
						$icon = $icon_image;
					} else {
						$emoji = $icon_image;
					}
					if ( $image ) {
						ABPET_Layout::image( '', $image );
					} else { ?>
                        <span class="<?php echo esc_attr( $icon . ' ' . $class ); ?>"><?php echo esc_html( $emoji ); ?></span>
					<?php }
				}
			}
			public function load_icon( $name, $value = '' ): void {
				$button_active_class = $value ? '_d_none' : '';
				$icon                = $emoji = '';
				if ( preg_match( '/\s/', $value ) ) {
					$icon = $value;
				} else {
					$emoji = $value;
				}
				$icon_class = ( $icon || $emoji ) ? '' : '_d_none';
				?>
                <div class="icon_image_selection_area">
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
                    <div class="icon_item  <?php echo esc_attr( $icon_class ); ?>">
                        <div class="_all_center"><span class="<?php echo esc_attr( $icon ); ?>" data-add-icon><?php echo esc_html( $emoji ); ?></span></div>
                        <span class="fas fa-times icon_close icon_delete" title="<?php esc_html_e( 'Remove Icon', 'abp-event-ticket' ); ?>"></span>
                    </div>
                    <div class="image_icon_select_area <?php echo esc_attr( $button_active_class ); ?>">
                        <button class="_btn_info_xs icon_add" type="button" data-target-popup="#abpet_popup_icon"><span class="fas fa-icons _fs_h6"></span></button>
                    </div>
                </div>
				<?php
			}
			public function image_selection( $name, $image_id = '', $target = '' ): void {
				?>
                <div class="image_selection">
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $image_id ); ?>" data-target="<?php echo esc_attr( $target ); ?>"/>
                    <div class="image_item <?php echo esc_attr( empty( $image_id ) ? '_d_none' : '' ); ?>" data-image-id="<?php echo esc_attr( $image_id ); ?>'">
                        <span class="image_remove" onclick="abpet_image_remove(this)">❌</span>
                        <img class="_img_control" src="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'medium' ) ); ?>" alt="<?php echo esc_attr( $image_id ); ?>"/>
                    </div>
                    <button type="button" class="_btn_light_active_xs <?php echo esc_attr( $image_id ? '_d_none' : '' ); ?>" onclick="abpet_image_selection(this)">
                        <span class="fas fa-image _mar_r_xs"></span><?php esc_html_e( 'Select Image', 'abp-event-ticket' ); ?>
                    </button>
                </div>
				<?php
			}
			public function add_image_multi( $name, $images ): void {
				$images = is_array( $images ) ? ABPET_Function::array_to_string( $images ) : $images;
				?>
                <div class="multiple_image_area">
                    <input type="hidden" class="multiple_image_ids" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $images ); ?>"/>
                    <div class="multiple_image">
						<?php
							$all_images = explode( ',', $images );
							if ( $images && sizeof( $all_images ) > 0 ) {
								foreach ( $all_images as $image ) {
									$img_url = ABPET_Function::get_image_url( '', $image, 'medium' ) ?: ABPET_BLANK_IMG_URL;
									?>
                                    <div class="multiple_image_item" data-image-id="<?php echo esc_attr( $image ); ?>">
                                        <span class="fas fa-times _circle_icon_xs remove_image_multi"></span>
                                        <img class="_img_control" src="<?php echo esc_attr( $img_url ); ?>" alt="<?php echo esc_attr( $image ); ?>"/>
                                    </div>
									<?php
								}
							}
						?>
                    </div>
					<?php ABPET_Layout::button_add( __( 'Add  Image', 'abp-event-ticket' ), 'add_image_multi _mar_t_xs' ); ?>
                </div>
				<?php
			}
			public function selection_icon_image( $name, $value = '' ): void {
				$icon = $image = $emoji = '';
				if ( is_numeric( $value ) ) {
					$image = $value;
				} elseif ( preg_match( '/\s/', $value ) ) {
					$icon = $value;
				} else {
					$emoji = $value;
				}
				$icon_class          = ( $icon || $emoji ) ? '' : '_d_none';
				$image_class         = $image ? '' : '_d_none';
				$button_active_class = ( $icon || $image || $emoji ) ? '_d_none' : '';
				?>
                <div class="icon_image_selection_area _fd_column">
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
                    <div class="icon_item <?php echo esc_attr( $icon_class ); ?>">
                        <div class="_all_center"><span class="<?php echo esc_attr( $icon ); ?>" data-add-icon><?php echo esc_html( $emoji ); ?></span></div>
                        <span class="fas fa-times icon_close icon_delete" title="<?php esc_html_e( 'Remove Icon', 'abp-event-ticket' ); ?>"></span>
                    </div>
                    <div class="image_item <?php echo esc_attr( $image_class ); ?>">
                        <img class="_img_control" src="<?php echo esc_url( ABPET_Function::get_image_url( '', $image, 'medium' ) ); ?>" alt="image">
                        <span class="fas fa-times icon_close image_delete" title="<?php esc_html_e( 'Remove Image', 'abp-event-ticket' ); ?>"></span>
                    </div>
                    <div class="image_icon_select_area <?php echo esc_attr( $button_active_class ); ?>">
                        <div class="_group_content_f_equal_w_full">
                            <button class="_btn_light_info_xs image_select" type="button"><span class="fas fa-image _fs_h6"></span></button>
                            <button class="_btn_light_info_xs icon_add" type="button" data-target-popup="#abpet_popup_icon"><span class="fas fa-icons _fs_h6"></span></button>
                        </div>
                    </div>
                </div>
				<?php
			}
			//=============static array================//
			public static function ticket_type( $key = '' ) {
				$types = [
					'sp'     => __( 'Seat Plan', 'abp-event-ticket' ),
					'ticket' => __( 'Ticket', 'abp-event-ticket' ),
				];
				return ! empty( $key ) ? ( $types[ $key ] ?? '' ) : $types;
			}
			public static function status_text( $status ): string {
				if ( ! is_string( $status ) && ! is_int( $status ) ) {
					return '';
				}
				$status_array = function_exists( 'wc_get_order_statuses' ) ? wc_get_order_statuses() : [];
				return is_array( $status_array ) ? ( $status_array[ $status ] ?? '' ) : '';
			}
			public static function week_day(): array {
				return [
					'monday'    => __( 'Monday', 'abp-event-ticket' ),
					'tuesday'   => __( 'Tuesday', 'abp-event-ticket' ),
					'wednesday' => __( 'Wednesday', 'abp-event-ticket' ),
					'thursday'  => __( 'Thursday', 'abp-event-ticket' ),
					'friday'    => __( 'Friday', 'abp-event-ticket' ),
					'saturday'  => __( 'Saturday', 'abp-event-ticket' ),
					'sunday'    => __( 'Sunday', 'abp-event-ticket' ),
				];
			}
			public static function date_option_rules(): array {
				$rules = [
					'weekend'            => __( 'Weekend', 'abp-event-ticket' ),
					'specific_off_dates' => __( 'Specific Off Dates', 'abp-event-ticket' ),
					'special_on_dates'   => __( 'Special On Dates', 'abp-event-ticket' ),
					'off_date_range'     => __( 'Off Dates Range', 'abp-event-ticket' ),
				];
				return apply_filters( 'abpet_filter_date_rule', $rules );
			}
			public static function array_date_format(): array {
				$current_date = current_time( 'Y-m-d' );
				return [
					'yy-mm-dd'   => $current_date,
					'yy/mm/dd'   => date_i18n( 'Y/m/d', strtotime( $current_date ) ),
					'yy-dd-mm'   => date_i18n( 'Y-d-m', strtotime( $current_date ) ),
					'yy/dd/mm'   => date_i18n( 'Y/d/m', strtotime( $current_date ) ),
					'dd-mm-yy'   => date_i18n( 'd-m-Y', strtotime( $current_date ) ),
					'dd/mm/yy'   => date_i18n( 'd/m/Y', strtotime( $current_date ) ),
					'mm-dd-yy'   => date_i18n( 'm-d-Y', strtotime( $current_date ) ),
					'mm/dd/yy'   => date_i18n( 'm/d/Y', strtotime( $current_date ) ),
					'd M , yy'   => date_i18n( 'j M , Y', strtotime( $current_date ) ),
					'D d M , yy' => date_i18n( 'D j M , Y', strtotime( $current_date ) ),
					'M d , yy'   => date_i18n( 'M  j, Y', strtotime( $current_date ) ),
					'D M d , yy' => date_i18n( 'D M  j, Y', strtotime( $current_date ) ),
				];
			}
			//=============================//
			public static function selection_area(): void {
				?>
                <div class="selection_area">
                    <label>
                        <input class="_form_control item_search" type="text" placeholder="<?php esc_attr_e( 'Search ....', 'abp-event-ticket' ); ?>"/>
                    </label>
                    <div class="selection_list"></div>
                </div>
				<?php
			}
			public static function selected_area( $name, $value = '' ): void {
				?>
                <div class="selected_area">
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
                    <div class="selected_list"></div>
                </div>
				<?php
			}
			//=============================//
			public static function title( $post_infos = [] ): void {
				$post_id = absint( $post_infos['post_id'] ?? 0 );
				if ( ! empty( $post_id ) && $post_id > 0 ) {
					$display_sku = $post_infos['display_sku'] ?? ABPET_Function::get_post_info( $post_id, 'display_sku', 'off' );
					$post_sku    = $post_infos['post_sku'] ?? ABPET_Function::get_post_info( $post_id, 'post_sku' );
					if ( ABPET_Function::on_off( 'post_icon' ) ) {
						ABPET_Layout::image_icon( ( $post_infos['post_icon'] ?? ABPET_Function::get_post_info( $post_id, 'post_icon' ) ) );
					}
					echo esc_html( get_the_title( $post_id ) ); ?>
					<?php if ( ! empty( $post_sku ) && $display_sku == 'on' && ABPET_Function::on_off( 'sku' ) ) { ?>
                        <small class="abp_color_gray">&nbsp;(<?php echo esc_html( $post_sku ); ?>)</small>
					<?php }
				}
			}
			public static function sub_title( $post_infos = [], $class = 'sub_title' ): void {
				$post_id = absint( $post_infos['post_id'] ?? 0 );
				if ( ABPET_Function::on_off( 'sub_title' ) && $post_id > 0 ) {
					$value = $post_infos['sub_title'] ?? ABPET_Function::get_post_info( $post_id, 'sub_title' );
					if ( ! empty( $value ) ) { ?>
                        <p class="abp <?php echo esc_attr( $class ); ?>">
							<?php echo esc_html( $value ); ?>
                        </p>
						<?php
					}
				}
			}
			public static function capacity( $post_infos = [], $class = 'publish' ): void {
				$post_id = absint( $post_infos['post_id'] ?? 0 );
				if ( ABPET_Function::on_off( 'display_capacity' ) && $post_id > 0 ) {
					$display  = $post_infos['display_capacity'] ?? ABPET_Function::get_post_info( $post_id, 'display_capacity', 'on' );
					$capacity = $post_infos['capacity'] ?? ABPET_Function::get_total_qty( $post_id, $post_infos );
					if ( ! empty( $capacity ) && $display === 'on' ) { ?>
                        <div class="abp_tag <?php echo esc_attr( $class ); ?>">
							<?php ABPET_Static::icon_svg( 'user_group_2' );
								echo esc_html( $capacity . ' ' . __( 'Attendees   ', 'abp-event-ticket' ) ); ?>
                        </div>
						<?php
					}
				}
			}
			public static function category( $post_infos = [], $class = '' ): void {
				$post_id = absint( $post_infos['post_id'] ?? 0 );
				if ( ABPET_Function::on_off( 'category' ) && $post_id > 0 ) {
					$display = $post_infos['display_category'] ?? ABPET_Function::get_post_info( $post_id, 'display_category', 'on' );
					$value   = $post_infos['abpet_category'] ?? ABPET_Function::get_post_info( $post_id, 'abpet_category' );
					if ( ! empty( $value ) && $display === 'on' ) {
						$values = explode( ',', $value );
						if ( ! empty( $values ) ) {
							foreach ( $values as $value ) {
								$value = ABPET_Function::category_value( $value ); ?>
                                <div class="abp_tag <?php echo esc_attr( $class ); ?>" title="<?php echo esc_attr( ABPET_Function::category_label() . ' : ' . $value ); ?>">
									<?php ABPET_Static::icon_svg( 'category_1' );
										echo esc_html( $value ); ?>
                                </div>
								<?php
							}
						}
					}
				}
			}
			public static function brand( $post_infos = [], $class = '' ): void {
				$post_id = absint( $post_infos['post_id'] ?? 0 );
				if ( ABPET_Function::on_off( 'brand' ) && $post_id > 0 ) {
					$display = $post_infos['display_brand'] ?? ABPET_Function::get_post_info( $post_id, 'display_brand', 'off' );
					$value   = $post_infos['abpet_brand'] ?? ABPET_Function::get_post_info( $post_id, 'abpet_brand' );
					if ( ! empty( $value ) && $display === 'on' ) {
						$values = explode( ',', $value );
						if ( ! empty( $values ) ) {
							foreach ( $values as $value ) {
								$value = ABPET_Function::brand_value( $value ); ?>
                                <span class="abp_tag <?php echo esc_attr( $class ); ?>" title="<?php echo esc_attr( ABPET_Function::brand_label() . ' : ' . $value ); ?>">
                                    <?php ABPET_Static::icon_svg( 'brand_2' );
	                                    echo esc_html( $value ); ?>
                                </span>
								<?php
							}
						}
					}
				}
			}
			public static function organizer( $post_infos = [], $class = '' ): void {
				$post_id = absint( $post_infos['post_id'] ?? 0 );
				if ( ABPET_Function::on_off( 'organizer' ) && $post_id > 0 ) {
					$display = $post_infos['display_organizer'] ?? ABPET_Function::get_post_info( $post_id, 'display_organizer', 'off' );
					$value   = $post_infos['abpet_organizer'] ?? ABPET_Function::get_post_info( $post_id, 'abpet_organizer' );
					if ( ! empty( $value ) && $display === 'on' ) {
						$values = explode( ',', $value );
						if ( ! empty( $values ) ) {
							foreach ( $values as $value ) {
								$value = ABPET_Function::organizer_value( $value );
								?>
                                <div class="abp_tag <?php echo esc_attr( $class ); ?>" title="<?php echo esc_attr( ABPET_Function::organizer_label() . ' : ' . $value ); ?>">
									<?php ABPET_Static::icon_svg( 'organizer_2' );
										echo esc_html( $value ); ?>
                                </div>
								<?php
							}
						}
								}
							}
						}
						public static function location( $post_infos = [], $class = '' ): void {
							$post_id = absint( $post_infos['post_id'] ?? 0 );
							if ( ! ABPET_Function::on_off( 'location' ) || $post_id <= 0 ) {
								return;
							}
							$display = $post_infos['display_location'] ?? ABPET_Function::get_post_info( $post_id, 'display_location', 'on' );
							$value   = $post_infos['abpet_location'] ?? ABPET_Function::get_post_info( $post_id, 'abpet_location' );
							if ( $display === 'on' && ! empty( $value ) ) {
								$values = array_filter( array_map( 'trim', explode( ',', $value ) ) );
								foreach ( $values as $value ) {
									$value = ABPET_Function::location_value( $value );
									echo '<div class="abp_tag ' . esc_attr( $class ) . '" title="' . esc_attr( ABPET_Function::location_label() . ' : ' . $value ) . '">';
									ABPET_Static::icon_svg( 'location_1' );
									echo esc_html( $value ) . '</div>';
								}
							}
						}
			public static function description( $post_infos = [], $class = '' ): void {
				$post_id = absint( $post_infos['post_id'] ?? 0 );
				if ( ABPET_Function::on_off( 'post_des' ) && $post_id > 0 ) {
					$value = $post_infos['post_description'] ?? ABPET_Function::get_post_info( $post_id, 'post_description' );
					if ( ! empty( $value ) ) { ?>
                        <div class="<?php echo esc_attr( $class ); ?>">
							<?php self::load_more( $value ); ?>
                        </div>
						<?php
					}
				}
			}
			public static function start_date( $all_dates, $date = '' ): void {
				//echo '<pre>';print_r($all_dates);					echo '</pre>';
				if ( sizeof( $all_dates ) > 0 ) {
					$now          = ABPET_Function::date_format( current_time( 'Y-m-d' ) );
					$date         = $date ?: current( $all_dates );
					$hidden_date  = ! empty( $date ) ? gmdate( 'Y-m-d', strtotime( $date ) ) : '';
					$visible_date = ! empty( $date ) ? ABPET_Function::date_format( $date, 'date' ) : '';
					if ( sizeof( $all_dates ) > 1 ) {
						?>
                        <label>
                            <span class="_gap_xxs"><?php ABPET_Static::icon_svg( 'date_1' ); ?><?php esc_html_e( 'Select Event Date', 'abp-event-ticket' ); ?><sup class="_color_required">*</sup></span>
                            <input type="hidden" name="start_date" value="<?php echo esc_attr( $hidden_date ); ?>" required/>
                            <input id="start_date" type="text" value="<?php echo esc_attr( $visible_date ); ?>" class="_form_control" placeholder="<?php echo esc_attr( $now ); ?>" data-alert="<?php esc_attr_e( 'Please Select Event Date', 'abp-event-ticket' ); ?>" readonly required/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-event-ticket' ); ?>"></span>
                        </label>
						<?php
						do_action( 'abpet_load_date_picker', '#start_date', $all_dates );
					} else {
						?>
                        <label>
                            <span class="_gap_xxs"><?php ABPET_Static::icon_svg( 'date_1' ); ?><?php esc_html_e( 'Event Date', 'abp-event-ticket' ); ?></span>
                            <input type="hidden" name="start_date" value="<?php echo esc_attr( $hidden_date ); ?>" required/>
                            <span class="_color_theme_fs_h5"><?php echo esc_html( $visible_date ); ?></span>
                        </label>
						<?php
					}
				} else {
					ABPET_Layout::layout_warning_info_xs( 'not_date' );
				}
			}
			public static function start_time( $form_data=[]): void {
				$all_times  = $form_data['all_times'] ?? [];
				$start_date = $form_data['start_date'] ?? '';
				$start_time = $form_data['start_time'] ?? '';
				if ( ! empty( $all_times ) && sizeof( $all_times ) > 0 ) { ?>
                    <label class="_text_nowrap">
                        <span class="_gap_xxs">⏰ <?php esc_html_e( 'Time :', 'abp-event-ticket' ); ?></span>
						<?php if ( sizeof( $all_times ) > 1 ) { ?>
                            <select class="_form_control" name="session_time">
								<?php foreach ( $all_times as $time ) { ?>
                                    <option value="<?php echo esc_attr( $time ); ?>" <?php selected( $time, $start_time ) ?>><?php echo esc_html( ABPET_Function::date_format( $start_date . ' ' . $time ) ); ?></option>
								<?php } ?>
                            </select>
						<?php } else { ?>
							<?php echo esc_html( ABPET_Function::date_format( $start_date . ' ' . $start_time ) ); ?>
                            <input type="hidden" name="session_time" value="<?php echo esc_attr( $start_time ); ?>">
						<?php } ?>
                    </label>
				<?php }
			}

			public static function item_feature( $features = '' ): void {
				if ( ABPET_Function::on_off( 'feature' ) ) {
					if ( ! is_string( $features ) || $features === '' ) {
						return;
					}
					$feature_ids   = explode( ',', $features );
					$abpet_feature = defined( 'ABPET_Feature' ) ? ABPET_Feature : [];
					if ( empty( $feature_ids ) || ! is_array( $abpet_feature ) ) {
						return;
					}
					//echo '<pre>';print_r($abpet_feature);echo '</pre>';
					?>
                    <div class="item_spec load_more">
                        <div class="_f_wrap_gap_xxs">
							<?php
								foreach ( $feature_ids as $fec_id ) {
									$feature = $abpet_feature[ $fec_id ] ?? null;
									if ( ! is_array( $feature ) ) {
										continue;
									}
									$label = $feature['label'] ?? '';
									$value = $feature['value'] ?? '';
									$icon  = $feature['icon'] ?? '';
									if ( $label || $value ) {
										echo '<span class="abp_tag" title="' . esc_attr( $label ) . '">';
										ABPET_Layout::image_icon( $icon );
										$output = implode( ' - ', array_filter( [ $label, $value ] ) );
										echo esc_html( $output );
										echo '</span>';
									}
								} ?>
                        </div>
                        <span class="load_more_action" data-less="<?php esc_html_e( '....Less ', 'abp-event-ticket' ); ?>" data-more="<?php esc_html_e( '.... More', 'abp-event-ticket' ); ?>"><?php esc_html_e( '.... More', 'abp-event-ticket' ); ?></span>
                    </div>
					<?php
				}
			}
			public static function item_select( $ticket_info, $key, $price = 0 ): void {
				if ( ! is_array( $ticket_info ) || empty( $key ) ) {
					return;
				}
				$total_qty     = intval( $ticket_info['qty'] ?? 0 );
				$max_qty       = $ticket_info['max_qty'] ?? '';
				$available_qty = $ticket_info['available'] ?? $total_qty;
				$max_qty       = ( $max_qty !== '' && intval( $max_qty ) <= $available_qty ) ? intval( $max_qty ) : $available_qty;
				$min_qty       = intval( $ticket_info['min_qty'] ?? 1 );
				//echo '<pre>';print_r($min_qty);echo '</pre>';
				if ( $max_qty >= $min_qty ) {
					$collapse_id = '#ticket_' . $key;
					?>
                    <div class="_divider_xxs"></div>
                    <div class="item_select">
                        <div class="custom_checkbox">
                            <input type="hidden" name="item_check[]" value="" data-id="<?php echo esc_attr( $collapse_id ); ?>"/>
                            <div class="checkbox_item" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                <h3 class="abp"><span data-icon class="far fa-square"></span></h3>
								<?php echo esc_html__( 'Select ', 'abp-event-ticket' ) . ' ' . esc_html( ABPET_Function::ticket_name( $key ) ); ?>
                            </div>
                        </div>
						<?php
							if ( $max_qty > $min_qty ) {
								$input_info = [
									'name'        => 'item_qty[]',
									'price'       => $price,
									'available'   => $available_qty,
									'min_qty'     => $min_qty,
									'max_qty'     => $max_qty,
									'collapse_id' => $collapse_id,
								];
								ABPET_Layout::quantity_input( $input_info );
							} else { ?>
                                <input type="hidden" name="item_qty[]" value="<?php echo esc_attr( $min_qty ); ?>" data-price="<?php echo esc_attr( $price ); ?>" data-min="<?php echo esc_attr( $min_qty ); ?>"/>
							<?php } ?>
                    </div>
				<?php } else { ?>
                    <span class="trash abp_tag"><?php esc_html_e( 'Sold Out !', 'abp-event-ticket' ); ?></span>
					<?php
				}
			}
			public static function create_client_form( $form, $name ): void {
				if ( ! is_array( $form ) ) {
					return;
				}
				$name     = is_string( $name ) ? $name . '[]' : '';
				$type     = $form['type'] ?? '';
				$required = ( ( $form['required'] ?? '' ) === 'on' ) ? 'required' : '';
				$label    = $form['label'] ?? '';
				$d_value  = $form['d_value'] ?? '';
				if ( $type === 'text' || $type === 'number' || $type === 'email' ) {
					$validation_class = match ( $type ) {
						'text' => 'validation_name',
						'number' => 'validation_number',
						default => '',
					};
					?>
                    <label class="_input_item">
						<?php ABPET_Layout::input_title( $label, $required ); ?>
                        <input type="<?php echo esc_attr( $type ); ?>"
                               name="<?php echo esc_attr( $name ); ?>"
                               value="<?php echo esc_attr( $d_value ); ?>"
                               class="_form_control <?php echo esc_attr( $validation_class ); ?>"
                               placeholder="<?php echo esc_attr( $label ); ?>"
                               title="<?php echo esc_attr( $label ); ?>"
							<?php echo esc_attr( $required ); ?> />
                    </label>
					<?php
					return;
				}
				if ( $type === 'date' ) {
					ABPET_Layout::input_date( $name, $d_value, $label, $required );
					return;
				}
				if ( $type === 'textarea' ) {
					ABPET_Layout::textarea( $name, $d_value, $label, $required );
					return;
				}
				// Options bound input layouts (Select, Checkbox, Radio)
				if ( $type === 'select' || $type === 'checkbox' || $type === 'radio' ) {
					$options_str = $form['option'] ?? '';
					$options     = ( $options_str !== '' ) ? explode( ',', $options_str ) : [];
					match ( $type ) {
						'select' => ABPET_Layout::select( $name, $d_value, $label, $required, $options ),
						'checkbox' => ABPET_Layout::checkbox( $name, $d_value, $label, $required, $options ),
						'radio' => ABPET_Layout::radio( $name, $d_value, $label, $required, $options ),
						default => null,
					};
				}
			}
			public static function sp( $id = '', $sp_info = [], $post_infos = [], $form_data = [] ): void {
				if ( empty( $sp_info ) ) {
					if ( ! empty( $id ) ) {
						$row = ABPET_Query::get_sp( $id );
						if ( ! empty( $row ) ) {
							$sp_info = current( $row );
						}
					}
				}
				if ( ! empty( $sp_info ) ) {
					$others      = json_decode( $sp_info['others'] ?? '', true ) ?: [];
					$cell_width  = $others['width'] ?? 50;
					$cell_height = $others['height'] ?? 50;
					$gap         = $others['gap'] ?? 0;
					$bg_image    = $others['bg_image'] ?? '';
					$img_url     = ! empty( $bg_image ) && $bg_image > 0 ? ABPET_Function::get_image_url( '', $bg_image ) : '';
					$bg_color    = $others['bg_color'] ?? '#fff';
					$radius      = $others['radius'] ?? 0;
					$layout      = json_decode( $sp_info['layout_data'] ?? '', true ) ?: [];
					//echo '<pre>';                print_r($ticket_types);                echo '</pre>';
					$cols         = intval( $others['column'] ?? 10 );
					$meta_info    = json_decode( $sp_info['seat_info'] ?? '', true ) ?: [];
					$hidden_cells = [];
					foreach ( $layout as $index => $cell ) {
						$c_span = intval( $cell['width_ratio'] ?? 1 );
						$r_span = intval( $cell['height_ratio'] ?? 1 );
						if ( $c_span > 1 || $r_span > 1 ) {
							for ( $r = 0; $r < $r_span; $r ++ ) {
								for ( $c = 0; $c < $c_span; $c ++ ) {
									if ( $r === 0 && $c === 0 ) {
										continue;
									}
									$target_idx                  = $index + ( $r * $cols ) + $c;
									$hidden_cells[ $target_idx ] = true;
								}
							}
						}
					}
					$post_id       = $form_data['post_id'] ?? '';
					$event_date    = $form_data['event_date'] ?? ($form_data['start_date'] ?? '');
					$session_time  = $form_data['session_time'] ?? ($form_data['start_time'] ?? '');
					$price_info    = [];
					$sold_seat     = [];
					$reserved_seat = [];
					$sale          = false;
					if ( ! empty( $event_date ) && ! empty( $session_time ) && ! empty( $post_id ) && $post_id > 0 ) {
						foreach ( $meta_info as $tic_id => $ticket_num ) {
							$price_info[ $tic_id ] = ABPET_Function::get_price( $post_infos, $tic_id, $session_time );
						}
						$form_data['sp_id'] = $id;
						$sold_seat          = ABPET_Query::get_sold_seat( $form_data );
						$sale               = true;
					}
					//echo '<pre>';                    print_r($form_data);                    echo '</pre>';
					// echo '<pre>';                    print_r($sold_seat);                    echo '</pre>';
					?>
                    <div class="sp_canvas _section_15_xs_mar_auto" style="grid-template-columns: repeat(<?php echo esc_attr( $cols ); ?>, 1fr); background-image: url('<?php echo esc_url( $img_url ); ?>'); background-color: <?php echo esc_attr( $bg_color ); ?>;gap: <?php echo esc_attr( $gap ); ?>px;">
						<?php foreach ( $layout as $index => $cell ) {
							if ( isset( $hidden_cells[ $index ] ) ) {
								continue;
							}
							$type_id         = $cell['id'] ?? '';
							$name            = $cell['name'] ?? '';
							$c_span          = intval( $cell['width_ratio'] ?? 1 );
							$r_span          = intval( $cell['height_ratio'] ?? 1 );
							$rotate          = intval( $cell['rotate'] ?? 0 );
							$fs              = $cell['fs'] ?? 12;
							$is_seat         = ( $cell['type'] === 'seat' );
							$seat_type_class = $sale && $is_seat ? 'available' : '';
							$title           = $sale && $is_seat ? __( 'Available !', 'abp-event-ticket' ) : '';
							$seat_type_class = $sale && in_array( $name, $sold_seat ) ? 'sold' : $seat_type_class;
							$title           = $sale && in_array( $name, $sold_seat ) ? __( 'Sold !', 'abp-event-ticket' ) : $title;
							$class           = $is_seat ? "sp_cell " . $seat_type_class : "sp_decor";
							$tag_attr        = $sale && $is_seat && $seat_type_class == 'available' ? 'data-name="' . $name . '" data-id="' . $type_id . '" data-price="' . ( $price_info[ $type_id ] ?? 0 ) . '"' : '';
							if ( ! empty( $title ) ) {
								$tag_attr = $tag_attr . '  title="' . $title . '"';
							}
							$color      = $is_seat ? ABPET_Function::ticket_color( $type_id ) : ABPET_Function::decor_color( $type_id );
							$icon_image = $is_seat ? ABPET_Function::ticket_icon( $type_id ) : ABPET_Function::decor_icon( $type_id );
							$width      = $cell_width * $c_span;
							$height     = $cell_height * $r_span;
							if ( $gap > 0 ) {
								$width  = $c_span > 1 ? $width + ( $c_span - 1 ) * $gap : $width;
								$height = $r_span > 1 ? $height + ( $r_span - 1 ) * $gap : $height;
							}
							$style = "color: {$color}; grid-column: span {$c_span}; grid-row: span {$r_span}; width:{$width}px;height:{$height}px; border:1px solid  {$color};font-size:{$fs}px;border-radius:{$radius}px;";
							$image = '';
							if ( ! empty( $icon_image ) ) {
								if ( is_numeric( $icon_image ) ) {
									$image = ABPET_Function::get_image_url( '', $icon_image );
								}
							}
							?>
                            <div class="<?php echo esc_attr( $class ); ?>" style="<?php echo esc_attr( $style ); ?>" <?php echo wp_kses_post( $tag_attr ); ?>>
                                <div class="cell_content <?php echo esc_attr( $rotate ? "rotate-{$rotate}" : "" ); ?>" style="background-image: url('<?php echo esc_url( $image ); ?>');">
									<?php ABPET_Layout::image_icon( $icon_image ); ?>
                                    <span class="cell_label"><?php echo esc_html( $name ); ?></span>
                                </div>
                            </div>
						<?php } ?>
                    </div>
					<?php
				} else {
					ABPET_Layout::layout_warning_info( 'no_sp_config' );
				}
			}
			//=============================//
			public static function ticket_info( $ticket_infos, $post_id, $seat_type = '', $sp_id = 0 ): void {
				if ( ! empty( $ticket_infos ) && is_array( $ticket_infos ) ) { ?>
                    <ul class=" abp">
						<?php foreach ( $ticket_infos as $tic_id => $ticket_info ) {
							if ( ! empty( $ticket_info ) && sizeof( $ticket_info ) > 0 ) {
								$current_seat_type = $ticket_info['seat_type'] ?? $seat_type;
								$qty       = $ticket_info['qty'] ?? 1;
								$price     = $ticket_info['price'] ?? 0;
								$total     = $price * $qty;
								$name      = $ticket_info['name'] ?? '';
								if ( $current_seat_type === 'sp' ) {
									$name = $name . ' - ' . ABPET_Function::sp_label( $post_id, ( $ticket_info['sp_id'] ?? $sp_id ) );
								}
								?>
                                <li>
                                    <strong><?php echo esc_html( $name ); ?></strong>
									<?php echo esc_html( ' X ' . $qty . ' = ' ) . ' ' . ( ! empty( $price ) && $total > 0 ? wp_kses_post( wc_price( $total ) ) : esc_html__( 'FREE', 'abp-event-ticket' ) ); ?>
                                </li>
								<?php
							}
						} ?>
                    </ul>
				<?php }
			}
			public static function additional_info( $additional_infos ): void {
				if ( ! empty( $additional_infos ) && is_array( $additional_infos ) ) { ?>
                    <ul class=" abp">
						<?php foreach ( $additional_infos as $ex_info ) {
							if ( ! empty( $ex_info ) && sizeof( $ex_info ) > 0 ) {
								$name       = $ex_info['name'] ?? '';
								$qty        = $ex_info['qty'] ?? 1;
								$price      = $ex_info['price'] ?? 0;
								$total      = $price * $qty;
								$returnable = $ex_info['returnable'] ?? 'no';
								if ( ! empty( $name ) ) { ?>
                                    <li>
                                        <strong><?php echo esc_html( $name ); ?></strong>
										<?php echo esc_html( ' X ' . $qty . ' = ' ) . ' ' . ( ! empty( $price ) && $total > 0 ? wp_kses_post( wc_price( $total ) ) : esc_html__( 'FREE', 'abp-event-ticket' ) ); ?>
										<?php
											if ( $returnable == 'yes' ) {
												?> <span class="_color_required"> - <?php esc_html_e( 'Returnable', 'abp-event-ticket' ); ?></span><?php
											} ?>
                                    </li>
									<?php
								}
							}
						} ?>
                    </ul>
				<?php }
			}
			public static function client_info( $passenger_infos ): void {
				if ( ! empty( $passenger_infos ) && is_array( $passenger_infos ) ) { ?>
                    <ul class=" abp">
						<?php foreach ( $passenger_infos as $pas_form ) {
							if ( ! empty( $pas_form ) && sizeof( $pas_form ) > 0 ) {
								foreach ( $pas_form as $info ) {
									$label = $info['label'] ?? '';
									$value = $info['value'] ?? '';
									if ( ! empty( $label ) && ! empty( $value ) ) { ?>
                                        <li>
                                            <strong><?php echo esc_html( $label ); ?></strong> : <?php echo esc_html( $value ); ?>
                                        </li>
										<?php
									}
								}
							}
						} ?>
                    </ul>
				<?php }
			}
			public static function billing_info( $booking_list ): void {
				if ( ! empty( $booking_list ) ) {
					$billing_name    = $booking_list['billing_name'] ?? '';
					$billing_email   = $booking_list['billing_email'] ?? '';
					$billing_phone   = $booking_list['billing_phone'] ?? '';
					$billing_address = $booking_list['billing_address'] ?? '';
					?>
                    <ul class=" abp">
						<?php if ( ! empty( $billing_name ) ) { ?>
                            <li><strong><?php esc_html_e( 'Name :', 'abp-event-ticket' ); ?></strong>&nbsp;<?php echo esc_html( $billing_name ); ?></li>
						<?php } ?>
						<?php if ( ! empty( $billing_email ) ) { ?>
                            <li><strong><?php esc_html_e( 'E-Mail :', 'abp-event-ticket' ); ?></strong>&nbsp;<?php echo esc_html( $billing_email ); ?></li>
						<?php } ?>
						<?php if ( ! empty( $billing_phone ) ) { ?>
                            <li><strong><?php esc_html_e( 'Phone :', 'abp-event-ticket' ); ?></strong>&nbsp;<?php echo esc_html( $billing_phone ); ?></li>
						<?php } ?>
						<?php if ( ! empty( $billing_address ) ) { ?>
                            <li><strong><?php esc_html_e( 'Address :', 'abp-event-ticket' ); ?></strong>&nbsp;<?php echo esc_html( $billing_address ); ?></li>
						<?php } ?>
                    </ul>
					<?php
				}
			}
			//=============================//
			public static function filter_post_list(): void {
				$label      = ABPET_Function::label();
				$brand_icon = ABPET_Function::icon();
				// echo '<pre>';print_r($configuration);echo '</pre>';
				?>
                <div class="_input_item abp_dropdown post_selection">
                    <label>
                        <span class="_gap_xxs"><?php ABPET_Layout::image_icon( $brand_icon ); ?><?php echo esc_html( $label ); ?></span>
                        <input type="hidden" name="post_id" value=""/>
                        <input type="text" class="_form_control_w_full" name="" placeholder="<?php echo esc_attr( $label ); ?>" value=""/>
                    </label>
					<?php if ( sizeof( ABPET_ids ) > 0 ) { ?>
                        <div class="dropdown_list">
                            <ul class="abp ">
								<?php foreach ( ABPET_ids as $all_post_id ) {
									$sku      = ABPET_Function::get_post_info( $all_post_id, 'post_sku' );
									$category = ABPET_Function::get_post_info( $all_post_id, 'abpet_category' );
									$category = ! empty( $category ) ? get_term( $category )->name : '';
									$title    = get_the_title( $all_post_id );
									?>
                                    <li class="_gap_xxs" data-value="<?php echo esc_attr( $all_post_id ); ?>" data-text="<?php echo esc_attr( $title ); ?>">
										<?php if ( ABPET_Function::on_off( 'post_icon' ) ) {
											ABPET_Layout::image_icon( ABPET_Function::get_post_info( $all_post_id, 'post_icon' ) );
										} ?>
                                        <span class="_fs_label"><?php echo esc_html( $title ); ?></span>
										<?php if ( ! empty( $category ) && ABPET_Function::on_off( 'category' ) ) { ?>
                                            <sub class="abp_color_gray"> - <?php echo esc_html( $category ); ?></sub>
										<?php } ?>
										<?php if ( ! empty( $sku ) && ABPET_Function::on_off( 'sku' ) ) { ?>
                                            <sub class="abp_color_info"> - <?php echo esc_html( $sku ); ?></sub>
										<?php } ?>
                                    </li>
								<?php } ?>
                            </ul>
                        </div>
					<?php } ?>
                </div>
				<?php
			}
			public static function filter_booking_date(): void {
				$now         = ABPET_Function::date_format( current_time( 'Y-m-d' ) );
				?>
                <div class="_input_item">
                    <label>
                        <span class="_gap_xs"><?php ABPET_Static::icon_svg( 'date_1' ); ?><?php esc_html_e( 'Event Date', 'abp-event-ticket' ) ?></span>
                        <input type="hidden" name="event_date" value=""/>
                        <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                        <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-event-ticket' ); ?>"></span>
                    </label>
                </div>
				<?php
			}
			public static function filter_booking_date_between(): void {
				$now         = ABPET_Function::date_format( current_time( 'Y-m-d' ) );
				?>
                <div class="_g_input_input_item_fd_column">
                    <label><span class="_gap_xs"><?php ABPET_Static::icon_svg( 'date_2' ); ?><?php esc_html_e( 'Event Date Between', 'abp-event-ticket' ) ?></span></label>
                    <div class="_f_equal">
                        <label>
                            <input type="hidden" name="event_date_from" value=""/>
                            <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-event-ticket' ); ?>"></span>
                        </label>
                        <label>
                            <input type="hidden" name="event_date_to" value=""/>
                            <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-event-ticket' ); ?>"></span>
                        </label>
                    </div>
                </div>
				<?php
			}
			public static function filter_order_date(): void {
				$now         = ABPET_Function::date_format( current_time( 'Y-m-d' ) );
				?>
                <div class="_input_item">
                    <label>
                        <span class="_gap_xs">🗓️ <?php esc_html_e( 'Order Date', 'abp-event-ticket' ) ?></span>
                        <input type="hidden" name="order_date" value=""/>
                        <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                        <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-event-ticket' ); ?>"></span>
                    </label>
                </div>
				<?php
			}
			public static function filter_order_date_between(): void {
				$now         = ABPET_Function::date_format( current_time( 'Y-m-d' ) );
				?>
                <div class="_g_input_input_item_fd_column" data-collapse="#view_more_filter_option">
                    <label class="_mar_b_xxs"><span class="_gap_xs">⏰ <?php esc_html_e( 'Order Date Between', 'abp-event-ticket' ); ?></span></label>
                    <div class="_f_equal">
                        <label>
                            <input type="hidden" name="order_date_from" value=""/>
                            <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-event-ticket' ); ?>"></span>
                        </label>
                        <label>
                            <input type="hidden" name="order_date_to" value=""/>
                            <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-event-ticket' ); ?>"></span>
                        </label>
                    </div>
                </div>
				<?php
			}
			public static function filter_user_id(): void {
				$all_users = get_users( array(
					'fields' => array( 'ID', 'display_name' ),
				) );
				?>
                <div class="_input_item abp_dropdown " data-collapse="#view_more_filter_option">
                    <label>
                        <span class="_gap_xs">👨‍💼  <?php esc_html_e( 'User Name', 'abp-event-ticket' ); ?></span>
                        <input type="hidden" name="user_id" value=""/>
                        <input type="text" class="_form_control_w_full" placeholder="<?php esc_attr_e( 'User Name', 'abp-event-ticket' ); ?>" value=""/>
                    </label>
					<?php if ( ! empty( $all_users ) ) { ?>
                        <div class="dropdown_list">
                            <ul class="abp ">
								<?php foreach ( $all_users as $user ) { ?>
                                    <li data-value="<?php echo esc_attr( $user->ID ); ?>" data-text="<?php echo esc_attr( $user->display_name ); ?>">
                                        <span class="_fs_label"><?php echo esc_html( $user->display_name ); ?></span>
                                    </li>
								<?php } ?>
                            </ul>
                        </div>
					<?php } ?>
                </div>
				<?php
			}
			public static function filter_order_id(): void {
				?>
                <div class="_input_item " data-collapse="#view_more_filter_option">
                    <label>
                        <span class="_gap_xs">📦 <?php esc_html_e( 'Order ID', 'abp-event-ticket' ); ?></span>
                        <input type="number" class="_form_control_w_full validation_number" name="order_id" placeholder="<?php esc_attr_e( 'Order ID', 'abp-event-ticket' ); ?>" value=""/>
                    </label>
                </div>
				<?php
			}
			public static function filter_bill_name(): void {
				?>
                <div class="_input_item " data-collapse="#view_more_filter_option">
                    <label>
                        <span class="_gap_xs">👤 <?php esc_html_e( 'Billing Name', 'abp-event-ticket' ); ?></span>
                        <input type="text" class="_form_control_w_full " name="billing_name" placeholder="<?php esc_attr_e( 'Billing Name', 'abp-event-ticket' ); ?>" value=""/>
                    </label>
                </div>
				<?php
			}
			public static function filter_bill_email(): void {
				?>
                <div class="_input_item " data-collapse="#view_more_filter_option">
                    <label>
                        <span class="_gap_xs">✉️ <?php esc_html_e( 'Billing Email', 'abp-event-ticket' ); ?></span>
                        <input type="email" class="_form_control_w_full " name="billing_email" placeholder="<?php esc_attr_e( 'Billing Email', 'abp-event-ticket' ); ?>" value=""/>
                    </label>
                </div>
				<?php
			}
			public static function filter_bill_phone(): void {
				?>
                <div class="_input_item " data-collapse="#view_more_filter_option">
                    <label>
                        <span class="_gap_xs">☎️ <?php esc_html_e( 'Billing phone', 'abp-event-ticket' ); ?></span>
                        <input type="text" class="_form_control_w_full " name="billing_phone" placeholder="<?php esc_attr_e( 'Billing phone', 'abp-event-ticket' ); ?>" value=""/>
                    </label>
                </div>
				<?php
			}
		}
		new ABPET_Layout();
	}