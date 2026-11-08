<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abpet_timeline_modern_template', function ( $timeline_items = [], $post_id = 0 ) {
		if ( empty( $timeline_items ) || ! is_array( $timeline_items ) ) {
			return;
		}
		?>
        <section class="abpet_timeline abpet_timeline_modern">
            <div class="abpet_tl_modern_head">
                <span class="abpet_tl_modern_icon"><i class="fas fa-calendar-check" aria-hidden="true"></i></span>
                <div class="abpet_tl_modern_heading">
                    <span><?php esc_html_e( 'Schedule', 'abp-event-ticket' ); ?></span>
                    <h4><?php esc_html_e( 'Event Timeline', 'abp-event-ticket' ); ?></h4>
                </div>
            </div>
            <div class="abpet_tl_list">
				<?php foreach ( $timeline_items as $index => $item ) {
					$time  = $item['time'] ?? '';
					$title = $item['title'] ?? '';
					$des   = $item['des'] ?? '';
					?>
                    <div class="abpet_tl_item">
                        <div class="abpet_tl_badge">
                            <span class="abpet_tl_badge_core"></span>
                        </div>
                        <div class="abpet_tl_card">
							<?php if ( ! empty( $time ) ) { ?>
                                <div class="abpet_tl_time">
                                    <i class="far fa-clock" aria-hidden="true"></i>
                                    <span><?php echo esc_html( $time ); ?></span>
                                </div>
							<?php } ?>
                            <h5 class="abpet_tl_title"><?php echo esc_html( $title ); ?></h5>
							<?php if ( ! empty( $des ) ) { ?>
                                <div class="abpet_tl_des">
									<?php
										// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
										echo wp_kses_post( apply_filters( 'the_content', $des ) );
									?>
                                </div>
							<?php } ?>
                        </div>
                    </div>
				<?php } ?>
            </div>
        </section>
		<?php
	}, 10, 2 );