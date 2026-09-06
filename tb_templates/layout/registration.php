<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abpet_registration_template', function ( $post_infos, $form_data = [] ) {
		if ( ! empty( $post_infos ) ) {
			$sale_continue = $post_infos['sale_continue'] ?? 'on';
			$seat_type     = $post_infos['seat_type'] ?? 'sp';
			$seat_type     = ABPET_Function::on_off( 'sp' ) ? $seat_type : 'ticket';
			$post_id       = absint( $post_infos['post_id'] ?? 0 );
			//echo '<pre>';print_r($upcoming_date);echo '</pre>';
			if ( $sale_continue == 'on' ) { ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">
                    <input type="hidden" name="event_date" value="<?php echo esc_attr( $form_data['start_date'] ?? $form_data['event_date'] ?? '' ); ?>">
                    <input type="hidden" name="session_time" value="<?php echo esc_attr( $form_data['session_time'] ?? $form_data['start_time'] ?? '' ); ?>">
                    <input type="hidden" name="seat_type" value="<?php echo esc_attr( $seat_type ); ?>">
                    <input type="hidden" name="same_attendee" value="<?php echo esc_attr( $post_infos['display_single_form'] ?? 'on' ); ?>">
                    <input type="hidden" name="min_qty" value="<?php echo esc_attr( $post_infos['min_qty'] ?? 1 ); ?>">
                    <input type="hidden" name="max_qty" value="<?php echo esc_attr( $post_infos['max_qty'] ?? '' ); ?>" data-msg="<?php echo esc_attr__( 'You can buy max ticket :', 'abp-event-ticket' ) . ' ' . esc_attr( ( $post_infos['max_qty'] ?? '' ) ); ?>">
					<?php wp_nonce_field( 'abpet_registration_nonce' );
						do_action( 'abpet_admin_order', $post_id ); ?>
                    <div class="booking_area">
                        <div class="ticket_left">
                            <div class="ticket_content">
								<?php if ( $seat_type === 'ticket' ) {
									do_action( 'abpet_ticket_type', $post_infos, $form_data );
								} else {
									do_action( 'abpet_sp_type', $post_infos, $form_data );
								} ?>
                            </div>
							<?php do_action( 'abpet_additional', $post_infos ); ?>
                        </div>
                        <div class="ticket_right">
							<?php
								if ( $seat_type === 'sp' ) {
									?>
                                    <div class="seat_selection">
                                        <div class="_section_15_xs">
                                            <table class="_abp">
                                                <thead>
                                                <tr>
                                                    <th><?php esc_html_e( 'Seat', 'abp-event-ticket' ); ?></th>
                                                    <th><?php esc_html_e( 'Price', 'abp-event-ticket' ); ?></th>
                                                    <th class="_text_center"><?php esc_html_e( 'Action', 'abp-event-ticket' ); ?></th>
                                                </tr>
                                                </thead>
                                                <tbody class="insert_item ">
                                                </tbody>
                                                <tfoot>
                                                <tr class="_fs_h5">
                                                    <th><?php esc_html_e( 'Sub-Total', 'abp-event-ticket' ); ?></th>
                                                    <th class="_color_theme sub_total"></th>
                                                    <th></th>
                                                </tr>
                                                </tfoot>
                                            </table>
                                            <div class="abp_hidden">
                                                <table class="_abp">
                                                    <tbody class="hidden_content">
                                                    <tr class="delete_area">
                                                        <th class="seat_name"></th>
                                                        <th class="seat_price"></th>
                                                        <th>
                                                            <div class="_all_center"><?php ABPET_Layout::button_delete( 'seat_remove' ); ?></div>
                                                        </th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
									<?php
								}
								do_action( 'abpet_client_form', $post_infos );
								do_action( 'abpet_total_price', $post_infos, $form_data );
							?>
                        </div>
                    </div>
                </form>
				<?php
			} else {
				ABPET_Layout::layout_warning_info( 'sale_close_msg' );
			}
		}
		?>
		<?php
	}, 10, 3 );