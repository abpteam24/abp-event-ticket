<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
add_action( 'abpet_details_modern_template', function ( $post_id, $form_data = [] ) {
	if ( ! $post_id || get_post_type( $post_id ) !== ABPET_Function::get_cpt() || ( get_post_status( $post_id ) !== 'publish' && ! is_admin() ) ) {
		return;
	}
	$post_infos = ABPET_Function::get_all_meta( $post_id );
	$all_dates  = ABPET_Function::date( $post_id );
	$time_infos = $post_infos['time_infos'] ?? [];
	[ $all_dates, $start_date, $all_times, $start_time ] = ABPET_Function::event_schedule_selection( $all_dates, $time_infos );
	$show_date_list = ABPET_Function::on_off( 'event_date_list' ) && count( ABPET_Function::event_schedule_items( $post_id, $all_dates, $time_infos ) ) > 1;
	$content = get_post_field( 'post_content', $post_id );
	$sale_continue = $post_infos['sale_continue'] ?? 'on';
	$form_data  = array_merge( $form_data, [
		'form' => 'inline',
		'post_id' => $post_id,
		'all_dates' => $all_dates,
		'all_times' => $all_times,
		'start_date' => $start_date,
		'start_time' => $start_time,
		'event_date' => $start_date,
		'session_time' => $start_time,
	] );
	?>
	<div id="abpet_area" class="abpet_area abpet_modern_details">
		<div class="abpet_modern_banner">
			<div class="abp_container">
				<div class="abpet_modern_split">
					<div class="abpet_modern_sticky_media">
						<?php if ( ! empty( $post_infos['abpet_slider'] ) ) {
							do_action( 'abpet_slider', $post_infos['abpet_slider'], [ 'slider_style' => 'slider' ] );
						} else {
							ABPET_Layout::image( $post_id );
						} ?>
					</div>
					<div class="abpet_modern_copy">
						<div class="abpet_modern_kicker">
							<?php if ( ! empty( $form_data['event_date'] ) ) { ?>
								<span class="abpet_modern_kicker_date"><?php ABPET_Static::icon_svg( 'date_1' ); ?><?php echo esc_html( ABPET_Function::date_format( $form_data['event_date'] ) ); ?></span>
							<?php } ?>
							<?php if ( $sale_continue === 'on' ) { ?>
								<span class="abpet_modern_kicker_live"><?php esc_html_e( 'Booking Open', 'abp-event-ticket' ); ?></span>
							<?php } ?>
						</div>
						<h1 class="abpet_details_title"><?php ABPET_Layout::title( $post_infos ); ?></h1>
						<?php ABPET_Layout::sub_title( $post_infos, 'sub_title abpet_modern_subtitle' ); ?>
						<div class="abpet_modern_meta _gap_xs_mar_t_xs">
							<?php ABPET_Layout::capacity( $post_infos ); ABPET_Layout::category( $post_infos ); ABPET_Layout::brand( $post_infos ); ABPET_Layout::organizer( $post_infos, 'publish' ); ABPET_Layout::location( $post_infos ); ?>
						</div>
						<?php ABPET_Layout::description( $post_infos, 'abpet_modern_description' ); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="abp_container">
			<div class="abpet_modern_booking_card abpet_booking">
				<?php if ( $show_date_list ) { ?>
					<div class="abpet_modern_schedule">
						<?php do_action( 'abpet_event_schedule_list', $post_id, $all_dates, $time_infos, $start_date, $start_time, 'scroll' ); ?>
					</div>
				<?php } ?>
				<?php if ( $sale_continue === 'on' ) { ?>
					<div class="post_top_filter"><?php ABPET_Layout::start_date( $all_dates, $start_date ); ABPET_Layout::start_time( $form_data ); ?></div>
					<?php do_action( 'abpet_registration', $post_infos, $form_data ); ?>
				<?php } else { ABPET_Layout::layout_warning_info( 'sale_close_msg' ); } ?>
			</div>
			<?php if ( ! empty( $content ) ) { ?>
				<div class="_abp_row">
					<div class="_col_12"><div class="the_post_content"><?php echo wp_kses_post( apply_filters( 'the_content', $content ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter 'the_content'. ?></div></div>
				</div>
			<?php } ?>
			<div class="_abp_row">
				<div class="_f_equal_f_wrap_gap_w_full">
					<div class="abpet_details_column">
						<?php if ( ABPET_Function::on_off( 'feature' ) ) { ABPET_Layout::item_feature( $post_infos['post_feature'] ?? '' ); } ?>
						<?php do_action( 'abpet_faq', $post_infos ); ?>
					</div>
					<div class="abpet_details_column">
						<?php do_action( 'abpet_term_condition', $post_infos ); ?>
					</div>
				</div>
			</div>
			<?php if ( empty( $post_infos['abpet_slider'] ) ) { ?>
				<div class="_abp_row"><div class="_col_12"><?php do_action( 'abpet_slider', $post_infos['abpet_slider'] ?? [] ); ?></div></div>
			<?php } ?>
			<div class="_abp_row"><div class="_col_12"><?php do_action( 'abpet_related_item', $post_infos['related_item'] ?? '', $post_id ); ?></div></div>
		</div>
	</div>
	<?php
}, 10, 2 );