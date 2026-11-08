<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abpet_map_modern_template', function ( $map_locations = [], $post_id = 0, $map_mode = 'js' ) {
		if ( empty( $map_locations ) || ! is_array( $map_locations ) ) {
			return;
		}
		$title  = ABPET_Function::location_label();
		$is_js  = $map_mode === 'js';
		?>
        <section class="abpet_map abpet_map_modern">
            <div class="abpet_map_modern_head">
                <span class="abpet_map_modern_icon"><i class="fas fa-globe-americas" aria-hidden="true"></i></span>
                <div class="abpet_map_modern_heading">
                    <span class="abpet_map_eyebrow"><?php esc_html_e( 'Find Us', 'abp-event-ticket' ); ?></span>
                    <h4><?php echo esc_html( $title ); ?></h4>
                </div>
            </div>
            <div class="abpet_map_modern_body">
				<?php foreach ( $map_locations as $map_loc ) { ?>
                    <div class="abpet_map_modern_item">
						<?php if ( $is_js ) { ?>
                        <div class="abpet_map_canvas" data-map-lat="<?php echo esc_attr( $map_loc['lat'] ); ?>" data-map-lng="<?php echo esc_attr( $map_loc['lng'] ); ?>" data-map-label="<?php echo esc_attr( $map_loc['label'] ); ?>" data-map-address="<?php echo esc_attr( $map_loc['address'] ); ?>"></div>
						<?php } else { ?>
                        <iframe class="abpet_map_iframe" src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . $map_loc['lat'] . ',' . $map_loc['lng'] . '&z=16&output=embed' ); ?>" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" title="<?php echo esc_attr( $map_loc['label'] ); ?>"></iframe>
						<?php } ?>
						<?php if ( ! empty( $map_loc['address'] ) ) { ?>
                            <a class="abpet_map_modern_caption" href="https://www.google.com/maps/search/?api=1&query=<?php echo esc_attr( urlencode( $map_loc['address'] ) ); ?>" target="_blank" rel="noopener">
                                <span class="abpet_map_modern_pin"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></span>
                                <span class="abpet_map_modern_caption_text">
                                    <strong><?php echo esc_html( $map_loc['label'] ); ?></strong>
                                    <span><?php echo esc_html( $map_loc['address'] ); ?></span>
                                </span>
                                <span class="abpet_map_modern_go"><?php esc_html_e( 'Get Directions', 'abp-event-ticket' ); ?></span>
                            </a>
						<?php } ?>
                    </div>
				<?php } ?>
            </div>
        </section>
		<?php
	}, 10, 3 );