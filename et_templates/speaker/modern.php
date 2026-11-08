<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abpet_speaker_modern_template', function ( $speakers = [], $post_id = 0 ) {
		if ( empty( $speakers ) || ! is_array( $speakers ) ) {
			return;
		}
		?>
        <section class="abpet_speaker abpet_speaker_modern">
            <div class="abpet_sp_modern_head">
                <div class="abpet_sp_modern_icon"><i class="fas fa-microphone-alt" aria-hidden="true"></i></div>
                <div class="abpet_sp_modern_heading">
                    <span><?php esc_html_e( 'Meet the', 'abp-event-ticket' ); ?></span>
                    <h4><?php echo esc_html( ABPET_Function::speaker_label() ); ?></h4>
                </div>
            </div>
            <div class="abpet_sp_modern_row">
				<?php foreach ( $speakers as $index => $speaker ) {
					$name        = $speaker['name'] ?? '';
					$designation = $speaker['designation'] ?? '';
					$company     = $speaker['company'] ?? '';
					$bio         = $speaker['description'] ?? '';
					$website     = $speaker['website'] ?? '';
					$photo_url   = $speaker['photo_url'] ?? '';
					$link        = $speaker['link'] ?? '';
					$social      = $speaker['social'] ?? [];
					$initials    = '';
					foreach ( preg_split( '/\s+/', trim( $name ) ) as $part ) {
						if ( '' !== $part ) {
							$initials .= mb_substr( $part, 0, 1 );
							if ( mb_strlen( $initials ) >= 2 ) {
								break;
							}
						}
					}
					$initials = mb_strtoupper( $initials );
					?>
                    <article class="abpet_sp_modern_item">
					<div class="abpet_sp_modern_photo">
						<?php $sp_photo_img = ABPET_Function::speaker_photo_img( $speaker['photo_id'] ?? 0, 'medium_large', $name, 'attachment-medium_large size-medium_large' );
						if ( '' !== $sp_photo_img ) { ?>
							<?php echo wp_kses_post( $sp_photo_img ); ?>
						<?php } else { ?>
							<span class="abpet_sp_initials" aria-hidden="true"><?php echo esc_html( $initials ); ?></span>
						<?php } ?>
					</div>
                        <h5 class="abpet_sp_modern_name">
							<?php if ( ! empty( $link ) ) { ?>
								<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $name ); ?></a>
							<?php } else { ?>
								<?php echo esc_html( $name ); ?>
							<?php } ?>
						</h5>
						<?php if ( '' !== $designation || '' !== $company ) { ?>
							<div class="abpet_sp_modern_role">
								<?php
									$role = array_map( 'esc_html', array_filter( [ $designation, $company ] ) );
									echo implode( ' <span aria-hidden="true">&bull;</span> ', $role ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Each part is escaped via esc_html() above; the separator is static markup.
								?>
							</div>
						<?php } ?>
						<?php if ( ! empty( $social ) || '' !== $website ) { ?>
							<div class="abpet_sp_modern_links">
								<?php if ( '' !== $website ) { ?>
									<a class="abpet_sp_link" href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( $website ); ?>"><i class="fas fa-globe" aria-hidden="true"></i><span class="screen-reader-text"><?php esc_html_e( 'Website', 'abp-event-ticket' ); ?></span></a>
								<?php } ?>
								<?php foreach ( $social as $item ) { ?>
									<a class="abpet_sp_link" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( $item['label'] ); ?>"><i class="<?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></i><span class="screen-reader-text"><?php echo esc_html( $item['label'] ); ?></span></a>
								<?php } ?>
							</div>
						<?php } ?>
						<?php if ( '' !== trim( wp_strip_all_tags( $bio ) ) ) { ?>
							<div class="abpet_sp_modern_bio">
								<?php
									// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
									echo wp_kses_post( apply_filters( 'the_content', $bio ) );
								?>
							</div>
						<?php } ?>
                    </article>
				<?php } ?>
            </div>
        </section>
		<?php
	}, 10, 2 );
