<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abpet_details_default_template', function ( $post_id, $form_data = [] ) {
		if ( ! empty( $post_id ) && $post_id > 0 && get_post_type( $post_id ) == ABPET_Function::get_cpt() && ( get_post_status( $post_id ) == 'publish' || is_admin() ) ) {
			$post_infos                   = ABPET_Function::get_all_meta( $post_id );
			$form_data['form']            = 'inline';
			$form_data['post_id']         = $post_id;
			$content                      = get_post_field( 'post_content', $post_id );
			$all_dates                    = ABPET_Function::date( $post_id );
			$time_infos                   = $post_infos['time_infos'] ?? [];
			[ $all_dates, $start_date, $all_times, $start_time ] = ABPET_Function::event_schedule_selection( $all_dates, $time_infos );
			$show_date_list               = ABPET_Function::on_off( 'event_date_list' ) && count( ABPET_Function::event_schedule_items( $post_id, $all_dates, $time_infos ) ) > 1;
			$upcoming_date                = '';
			if ( ! empty( $start_date ) ) {
				$upcoming_date = ! empty( $start_time ) ? gmdate( 'Y-m-d H:i', strtotime( $start_date . ' ' . $start_time ) ) : $start_date;
			}
			$form_data['all_dates']       = $all_dates;
			$form_data['all_times']       = $all_times;
			$form_data['start_date']      = $start_date;
			$form_data['start_time']      = $start_time;
			$form_data['session_time']    = $start_time;
			$form_data['event_date']      = $upcoming_date;
			$sale_continue                = $post_infos['sale_continue'] ?? 'on';
			$is_on_sale                   = ( $sale_continue === 'on' );
			?>
            <div id="abpet_area" class="abpet_area default_details_page">
                <div class="abp_container">
                    <section class="abpet_default_hero">
                        <div class="abpet_default_hero_media">
							<?php if ( ! empty( $post_infos['abpet_slider'] ) ) {
								do_action( 'abpet_slider', $post_infos['abpet_slider'], [ 'slider_style' => 'slider' ] );
							} else {
								ABPET_Layout::image( $post_id );
							} ?>
                        </div>
                        <div class="abpet_default_hero_body">
                            <div class="abpet_default_hero_kicker">
								<?php if ( ! empty( $upcoming_date ) ) { ?>
                                    <span class="abpet_default_date_chip">
										<?php ABPET_Static::icon_svg( 'date_1' ); ?>
										<?php echo esc_html( ABPET_Function::date_format( $upcoming_date ) ); ?>
                                    </span>
								<?php } ?>
								<?php if ( $is_on_sale ) { ?>
                                    <span class="abpet_default_badge_live">
										<i class="fas fa-check-circle" aria-hidden="true"></i>
										<?php esc_html_e( 'Booking Open', 'abp-event-ticket' ); ?>
                                    </span>
								<?php } else { ?>
                                    <span class="abpet_default_badge_off">
										<i class="fas fa-times-circle" aria-hidden="true"></i>
										<?php esc_html_e( 'Booking Closed', 'abp-event-ticket' ); ?>
                                    </span>
								<?php } ?>
                            </div>
                            <h1 class="abpet_details_title"><?php ABPET_Layout::title( $post_infos ); ?></h1>
							<?php ABPET_Layout::sub_title( $post_infos, 'sub_title abpet_default_subtitle' ); ?>
                            <div class="abpet_default_hero_meta _gap_xs_mar_t_xs">
								<?php ABPET_Layout::capacity( $post_infos );
									ABPET_Layout::category( $post_infos );
									ABPET_Layout::brand( $post_infos );
									ABPET_Layout::organizer( $post_infos, 'publish' );
									ABPET_Layout::location( $post_infos );
								?>
                            </div>
                            <div class="abpet_default_hero_content">
								<?php if ( ABPET_Function::on_off( 'feature' ) ) {
									ABPET_Layout::item_feature( $post_infos['post_feature'] ?? '' );
								}
									ABPET_Layout::description( $post_infos );
								?>
								<?php if ( $show_date_list ) { ?>
                                    <div class="abpet_default_schedule">
										<?php do_action( 'abpet_event_schedule_list', $post_id, $all_dates, $time_infos, $start_date, $start_time, 'dropdown' ); ?>
                                    </div>
								<?php } ?>
								<?php if ( ! empty( $content ) ) { ?>
                                    <div class="the_post_content abpet_default_content">
										<?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter 'the_content'. ?>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </section>

                    <section class="abpet_default_info_strip">
                        <div class="abpet_default_info_card">
                            <span class="abpet_default_info_icon"><i class="far fa-calendar-alt" aria-hidden="true"></i></span>
                            <div class="abpet_default_info_text">
                                <span class="abpet_default_info_label"><?php esc_html_e( 'Date', 'abp-event-ticket' ); ?></span>
                                <strong><?php echo ! empty( $start_date ) ? esc_html( ABPET_Function::date_format( $start_date ) ) : esc_html__( 'TBA', 'abp-event-ticket' ); ?></strong>
                            </div>
                        </div>
                        <div class="abpet_default_info_card">
                            <span class="abpet_default_info_icon"><i class="far fa-clock" aria-hidden="true"></i></span>
                            <div class="abpet_default_info_text">
                                <span class="abpet_default_info_label"><?php esc_html_e( 'Time', 'abp-event-ticket' ); ?></span>
                                <strong><?php echo ! empty( $start_time ) ? esc_html( ABPET_Function::date_format( $start_date . ' ' . $start_time ) ) : esc_html__( 'TBA', 'abp-event-ticket' ); ?></strong>
                            </div>
                        </div>
						<?php
							$location_enabled = ABPET_Function::on_off( 'location' );
							$display_location = ( $post_infos['display_location'] ?? ABPET_Function::get_post_info( $post_id, 'display_location', 'on' ) ) === 'on';
							if ( $location_enabled && $display_location ) {
							?>
                        <div class="abpet_default_info_card">
                            <span class="abpet_default_info_icon"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></span>
                            <div class="abpet_default_info_text">
                                <span class="abpet_default_info_label"><?php echo esc_html( ABPET_Function::location_label() ); ?></span>
                                <strong><?php
									$locations = ABPET_Function::get_post_info( $post_id, 'abpet_location' );
									if ( ! empty( $locations ) ) {
										$loc_values = array_filter( array_map( 'trim', explode( ',', $locations ) ) );
										$labels     = [];
										foreach ( $loc_values as $loc ) {
											$labels[] = ABPET_Function::location_value( $loc );
										}
										echo esc_html( implode( ', ', $labels ) );
									} else {
										esc_html_e( 'Online / TBA', 'abp-event-ticket' );
									}
								?></strong>
                            </div>
                        </div>
						<?php } ?>
						<?php
							$capacity_enabled = ABPET_Function::on_off( 'display_capacity' );
							$display_capacity = ( $post_infos['display_capacity'] ?? ABPET_Function::get_post_info( $post_id, 'display_capacity', 'on' ) ) === 'on';
							if ( $capacity_enabled && $display_capacity ) {
							?>
                        <div class="abpet_default_info_card">
                            <span class="abpet_default_info_icon"><i class="fas fa-users" aria-hidden="true"></i></span>
                            <div class="abpet_default_info_text">
                                <span class="abpet_default_info_label"><?php esc_html_e( 'Attendees', 'abp-event-ticket' ); ?></span>
                                <strong><?php echo esc_html( ABPET_Function::get_total_qty( $post_id, $post_infos ) ?: esc_html__( 'Open', 'abp-event-ticket' ) ); ?></strong>
                            </div>
                        </div>
						<?php } ?>
                    </section>

					<?php do_action( 'abpet_map', $post_infos, $post_id, 'default' ); ?>
					<?php do_action( 'abpet_timeline', $post_infos, $post_id, 'default' ); ?>

                    <div class="abpet_default_body">
                        <div class="abpet_default_main">
							<?php do_action( 'abpet_faq', $post_infos ); ?>
							<?php do_action( 'abpet_term_condition', $post_infos ); ?>
                        </div>
                        <aside class="abpet_default_sidebar">
                            <div class="abpet_default_booking_sticky">
                                <div class="abpet_booking">
									<?php
										if ( $is_on_sale ) {
											?>
                                                <div class="abpet_default_booking_head">
                                                    <i class="fas fa-ticket-alt" aria-hidden="true"></i>
                                                    <span><?php esc_html_e( 'Get Tickets', 'abp-event-ticket' ); ?></span>
                                                </div>
                                                <div class="post_top_filter">
												<?php ABPET_Layout::start_date( $all_dates, $start_date );
													ABPET_Layout::start_time( $form_data ); ?>
                                                </div>
											<?php
											do_action( 'abpet_registration', $post_infos, $form_data );
										} else {
											ABPET_Layout::layout_warning_info( 'sale_close_msg' );
										}
									?>
                                </div>
                            </div>
                        </aside>
                    </div>

                    <div class="abp_row">
                        <div class="_col_12"> <?php do_action( 'abpet_related_item', ( $post_infos['related_item'] ?? '' ), $post_id ); ?></div>
                    </div>
                </div>
            </div>
			<?php
		}
	}, 10, 2 );