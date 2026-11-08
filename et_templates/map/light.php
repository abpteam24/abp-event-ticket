<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abpet_map_light_template', function ( $map_locations = [], $post_id = 0, $map_mode = 'js' ) {
		if ( empty( $map_locations ) || ! is_array( $map_locations ) ) {
			return;
		}
		$title  = ABPET_Function::location_label();
		$is_js  = $map_mode === 'js';
		?>
        <section class="abpet_map abpet_map_light">
			<?php foreach ( $map_locations as $map_loc ) { ?>
                <div class="abpet_map_card">
					<?php if ( $is_js ) { ?>
                    <div class="abpet_map_canvas" data-map-lat="<?php echo esc_attr( $map_loc['lat'] ); ?>" data-map-lng="<?php echo esc_attr( $map_loc['lng'] ); ?>" data-map-label="<?php echo esc_attr( $map_loc['label'] ); ?>" data-map-address="<?php echo esc_attr( $map_loc['address'] ); ?>"></div>
					<?php } else { ?>
                    <iframe class="abpet_map_iframe" src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . $map_loc['lat'] . ',' . $map_loc['lng'] . '&z=16&output=embed' ); ?>" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" title="<?php echo esc_attr( $map_loc['label'] ); ?>"></iframe>
					<?php } ?>
                    <div class="abpet_map_card_body">
                        <span class="abpet_map_card_icon"><i class="fas fa-location-arrow" aria-hidden="true"></i></span>
                        <div class="abpet_map_card_info">
                            <span class="abpet_map_eyebrow"><?php echo esc_html( $title ); ?></span>
                            <h4 class="abpet_map_card_title"><?php echo esc_html( $map_loc['label'] ); ?></h4>
							<?php if ( ! empty( $map_loc['address'] ) ) { ?>
                                <p class="abpet_map_card_text"><?php echo esc_html( $map_loc['address'] ); ?></p>
							<?php } ?>
                        </div>
                        <a class="abpet_map_dir" href="https://www.google.com/maps/search/?api=1&query=<?php echo esc_attr( urlencode( $map_loc['label'] ) ); ?>" target="_blank" rel="noopener">
                            <span class="fas fa-directions"></span>
                        </a>
                    </div>
                </div>
			<?php } ?>
        </section>
		<?php
	}, 10, 3 );