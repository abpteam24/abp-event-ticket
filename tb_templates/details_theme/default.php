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
			?>
            <div id="abpet_area" class="abpet_area default_details_page">
                <div class="abpet_default_cover">
                    <div class="abpet_default_cover_media">
						<?php if ( ! empty( $post_infos['abpet_slider'] ) ) {
							do_action( 'abpet_slider', $post_infos['abpet_slider'], [ 'slider_style' => 'slider' ] );
						} else {
							ABPET_Layout::image( $post_id );
						} ?>
                    </div>
                    <div class="abpet_default_cover_overlay">
                        <div class="abp_container">
                            <div class="abpet_default_cover_inner">
								<?php if ( ! empty( $upcoming_date ) ) { ?>
                                    <div class="abpet_default_date_chip">
										<?php ABPET_Static::icon_svg( 'date_1' ); ?>
                                        <span><?php echo esc_html( ABPET_Function::date_format( $upcoming_date ) ); ?></span>
                                    </div>
								<?php } ?>
                                <h1 class="abpet_details_title"><?php ABPET_Layout::title( $post_infos ); ?></h1>
								<?php ABPET_Layout::sub_title( $post_infos, 'sub_title abpet_default_subtitle' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="abp_container">
                    <div class="abpet_default_metabar _gap_xs_mar_t_xs">
						<?php ABPET_Layout::capacity( $post_infos );
							ABPET_Layout::category( $post_infos );
							ABPET_Layout::brand( $post_infos );
							ABPET_Layout::organizer( $post_infos, 'publish' );
							ABPET_Layout::location( $post_infos );
						?>
                    </div>
                    <div class="_abp_row">
                        <div class="_f_equal_f_wrap_gap_section_15 abpet_default_body">
                            <div class="abpet_details_column abpet_default_main">
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
                            <div class="abpet_details_column abpet_default_sidebar">
                                <div class="abpet_default_booking_sticky">
                                    <div class="abpet_booking">
										<?php
											if ( $sale_continue == 'on' ) {
												?>
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
                            </div>
                        </div>
                    </div>
                    <div class="_abp_row">
                        <div class="abpet_info_grid _f_equal_f_wrap_gap_w_full">
                            <div class="abpet_details_column">
								<?php do_action( 'abpet_faq', $post_infos ); ?>
                            </div>
                            <div class="abpet_details_column">
								<?php do_action( 'abpet_term_condition', $post_infos ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="_abp_row">
                        <div class="_col_12"> <?php do_action( 'abpet_related_item', ( $post_infos['related_item'] ?? '' ), $post_id ); ?></div>
                    </div>
                </div>
            </div>
			<?php
		}
	}, 10, 2 );