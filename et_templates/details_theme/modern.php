<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	add_action( 'abpet_details_modern_template', function ( $post_id, $form_data = [] ) {
		if ( ! $post_id || get_post_type( $post_id ) !== ABPET_Function::get_cpt() || ( get_post_status( $post_id ) !== 'publish' && ! is_admin() ) ) {
			return;
		}
		$post_infos = ABPET_Function::get_all_meta( $post_id );
		$content    = get_post_field( 'post_content', $post_id );
		$all_dates  = ABPET_Function::date( $post_id );
		$time_infos = $post_infos['time_infos'] ?? [];
		[ $all_dates, $start_date, $all_times, $start_time ] = ABPET_Function::event_schedule_selection( $all_dates, $time_infos );
		$show_date_list            = ABPET_Function::on_off( 'event_date_list' ) && count( ABPET_Function::event_schedule_items( $post_id, $all_dates, $time_infos ) ) > 1;
		$upcoming_date             = ! empty( $start_date ) && ! empty( $start_time ) ? gmdate( 'Y-m-d H:i', strtotime( $start_date . ' ' . $start_time ) ) : $start_date;
		$form_data['post_id']      = $post_id;
		$form_data['all_dates']    = $all_dates;
		$form_data['all_times']    = $all_times;
		$form_data['start_date']   = $start_date;
		$form_data['start_time']   = $start_time;
		$form_data['session_time'] = $start_time;
		$form_data['event_date']   = $upcoming_date;
		$form_data['form']         = 'inline';
		$sale_continue             = $post_infos['sale_continue'] ?? 'on';
		?>
        <div id="abpet_area" class="abpet_area modern_theme">
            <div class="abp_container modern_banner">
                <section class="hero_area">
					<?php ABPET_Layout::post_thumb( $post_id ); ?>
                    <div class="hero_area_body">
						<?php ABPET_Layout::hero_kicker( $post_infos, $upcoming_date ); ?>
                        <h1 class="post_title"><?php ABPET_Layout::title( $post_infos ); ?></h1>
						<?php ABPET_Layout::sub_title( $post_infos ); ?>
                        <div class="_f_wrap_gap_xs">
							<?php ABPET_Layout::capacity( $post_infos );
								ABPET_Layout::category( $post_infos );
								ABPET_Layout::brand( $post_infos );
								ABPET_Layout::organizer( $post_infos, 'publish' );
								ABPET_Layout::location( $post_infos );
							?>
                        </div>
						<?php ABPET_Layout::item_feature( $post_infos['post_feature'] ?? '' );
							ABPET_Layout::description( $post_infos ); ?>
                    </div>
                </section>
            </div>
            <div class="abp_container">
                <div class="abpet_modern_booking_card abpet_booking">
					<?php if ( $show_date_list ) { ?>
                        <div class="abpet_modern_schedule">
							<?php do_action( 'abpet_event_schedule_list', $post_id, $all_dates, $time_infos, $start_date, $start_time, 'scroll' ); ?>
                        </div>
					<?php } ?>
					<?php if ( $sale_continue === 'on' ) { ?>
                        <div class="post_top_filter"><?php ABPET_Layout::start_date( $all_dates, $start_date, $all_times, $time_infos, $post_id, $start_time );
								ABPET_Layout::start_time( $form_data ); ?></div>
						<?php do_action( 'abpet_registration', $post_infos, $form_data ); ?>
					<?php } else {
						ABPET_Layout::layout_warning_info( 'sale_close_msg' );
					} ?>
                </div>
				<?php do_action( 'abpet_map', $post_infos, $post_id, 'modern' ); ?>
				<?php do_action( 'abpet_timeline', $post_infos, $post_id, 'modern' ); ?>
				<?php do_action( 'abpet_speaker', $post_infos, $post_id, 'modern' ); ?>
				<?php if ( ! empty( $content ) ) { ?>
                    <div class="abp_row">
                        <div class="_col_12">
                            <div class="the_post_content"><?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter 'the_content'. ?></div>
                        </div>
                    </div>
				<?php } ?>
                <div class="abp_row">
                    <div class="_f_equal_f_wrap_gap_w_full">
                        <div class="abpet_details_column">
							<?php do_action( 'abpet_faq', $post_infos ); ?>
                        </div>
                        <div class="abpet_details_column">
							<?php do_action( 'abpet_term_condition', $post_infos ); ?>
                        </div>
                    </div>
                </div>
				<?php if ( empty( $post_infos['abpet_slider'] ) ) { ?>
                    <div class="abp_row">
                        <div class="_col_12"><?php do_action( 'abpet_slider', $post_infos['abpet_slider'] ?? [] ); ?></div>
                    </div>
				<?php } ?>
                <div class="abp_row">
                    <div class="_col_12"><?php do_action( 'abpet_related_item', $post_infos['related_item'] ?? '', $post_id ); ?></div>
                </div>
            </div>
        </div>
		<?php
	}, 10, 2 );