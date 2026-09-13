<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abpet_map_default_template', function ( $map_locations = [], $post_id = 0, $map_mode = 'js' ) {
		if ( empty( $map_locations ) || ! is_array( $map_locations ) ) {
			return;
		}
		$title  = ABPET_Function::location_label();
		$is_js  = $map_mode === 'js';
		?>
        <section class="abpet_map abpet_map_default">
            <div class="_panel_head _fj_between">
                <h4 class="abp"><i class="fas fa-map-marker-alt" aria-hidden="true"></i><?php echo esc_html( __( 'Event', 'abp-event-ticket' ) . ' ' . $title ); ?></h4>
                <?php if ( count( $map_locations ) > 1 ) { ?>
                    <span class="abpet_map_count"><?php echo esc_html( count( $map_locations ) ); ?> <?php echo esc_html( $title ); ?></span>
                <?php } ?>
            </div>
            <div class="_panel_body_xs">
				<?php foreach ( $map_locations as $map_loc ) { ?>
                    <div class="abpet_map_block">
                        <div class="abpet_map_name">
                            <i class="fas fa-map-pin" aria-hidden="true"></i>
                            <strong><?php echo esc_html( $map_loc['label'] ); ?></strong>
                        </div>
						<?php if ( $is_js ) { ?>
                        <div class="abpet_map_canvas" data-map-lat="<?php echo esc_attr( $map_loc['lat'] ); ?>" data-map-lng="<?php echo esc_attr( $map_loc['lng'] ); ?>" data-map-label="<?php echo esc_attr( $map_loc['label'] ); ?>" data-map-address="<?php echo esc_attr( $map_loc['address'] ); ?>"></div>
						<?php } else { ?>
                        <iframe class="abpet_map_iframe" src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . $map_loc['lat'] . ',' . $map_loc['lng'] . '&z=16&output=embed' ); ?>" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" title="<?php echo esc_attr( $map_loc['label'] ); ?>"></iframe>
						<?php } ?>
						<?php if ( ! empty( $map_loc['address'] ) ) { ?>
                            <div class="abpet_map_address">
                                <i class="fas fa-map-signs" aria-hidden="true"></i>
                                <span><?php echo esc_html( $map_loc['address'] ); ?></span>
                            </div>
						<?php } ?>
                    </div>
				<?php } ?>
            </div>
        </section>
		<?php
	}, 10, 3 );