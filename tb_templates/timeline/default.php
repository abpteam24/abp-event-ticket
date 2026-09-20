<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abpet_timeline_default_template', function ( $timeline_items = [], $post_id = 0 ) {
		if ( empty( $timeline_items ) || ! is_array( $timeline_items ) ) {
			return;
		}
		?>
        <section class="abpet_timeline abpet_timeline_default">
            <div class="_panel_head _fj_between">
                <h4 class="abp"><i class="fas fa-stream" aria-hidden="true"></i><?php esc_html_e( 'Event Timeline', 'abp-event-ticket' ); ?></h4>
            </div>
            <div class="_panel_body_xs">
                <div class="abpet_tl_list">
					<?php foreach ( $timeline_items as $index => $item ) {
						$time  = $item['time'] ?? '';
						$title = $item['title'] ?? '';
						$des   = $item['des'] ?? '';
						?>
                        <div class="abpet_tl_item">
                            <div class="abpet_tl_dot">
                                <span class="abpet_tl_dot_core"></span>
                            </div>
                            <div class="abpet_tl_content">
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
            </div>
        </section>
		<?php
	}, 10, 2 );