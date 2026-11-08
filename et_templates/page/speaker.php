<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( wp_is_block_theme() ) { ?>
        <!DOCTYPE html>
        <html lang="" <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <title><?php echo esc_html( wp_get_document_title() ); ?></title>
			<?php
				do_blocks( '<div class="wp-block-group"></div>' );
				wp_head();
			?>
        </head>
        <body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
        <div class="wp-site-blocks">
            <header class="wp-block-template-part site-header">
				<?php block_header_area(); ?>
            </header>
        </div>
		<?php
	} else {
		get_header();
		if ( have_posts() ) {
			the_post();
		}
	}
	/**
	 * Every value this template prints comes from a single filtered view model, so
	 * a site can surface its own speaker data through the abpet_speaker_* filters
	 * instead of overriding this template. See ABPET_Function::speaker_page_data().
	 */
	$abpet_speaker = ABPET_Function::speaker_page_data( get_queried_object() );
	if ( ! empty( $abpet_speaker ) ) {
		$abpet_name = $abpet_speaker['name'];
		?>
        <main class="abpet_area abpet_speaker_profile_page">
            <div class="abp_container">
                <article class="abpet_speaker_card">
                    <header class="abpet_speaker_card_head">
                        <div class="abpet_speaker_hero_ring">
                            <div class="abpet_speaker_hero_photo">
								<?php if ( '' !== $abpet_speaker['photo_img'] ) {
									echo wp_kses_post( $abpet_speaker['photo_img'] );
								} else { ?>
                                        <span class="abpet_sp_initials" aria-hidden="true"><?php echo esc_html( $abpet_speaker['initials'] ); ?></span>
									<?php } ?>
                            </div>
                        </div>
                        <h1 class="abpet_speaker_hero_name"><?php echo esc_html( $abpet_name ); ?></h1>
					<?php if ( ! empty( $abpet_speaker['role_parts'] ) ) { ?>
                        <div class="abpet_speaker_hero_role"><?php echo implode( ' <span aria-hidden="true">&bull;</span> ', $abpet_speaker['role_parts'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Each part is escaped via esc_html() above; the separator is static markup. ?></div>
						<?php } ?>
						<?php if ( ! empty( $abpet_speaker['facts'] ) ) { ?>
                            <dl class="abpet_speaker_facts">
								<?php foreach ( $abpet_speaker['facts'] as $abpet_fact ) { ?>
                                    <div class="abpet_speaker_fact">
                                        <dt class="abpet_speaker_fact_label">
                                            <span class="abpet_speaker_fact_icon" aria-hidden="true"><i class="<?php echo esc_attr( $abpet_fact['icon'] ); ?>"></i></span>
											<?php echo esc_html( $abpet_fact['label'] ); ?>
                                        </dt>
										<?php if ( '' !== $abpet_fact['url'] ) { ?>
                                            <dd class="abpet_speaker_fact_value"><a class="abpet_speaker_fact_link" href="<?php echo esc_url( $abpet_fact['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $abpet_fact['value'] ); ?></a></dd>
										<?php } else { ?>
                                            <dd class="abpet_speaker_fact_value"><?php echo esc_html( $abpet_fact['value'] ); ?></dd>
										<?php } ?>
                                    </div>
								<?php } ?>
                            </dl>
						<?php } ?>
						<?php if ( ! empty( $abpet_speaker['socials'] ) ) { ?>
                            <div class="abpet_speaker_hero_socials">
								<?php foreach ( $abpet_speaker['socials'] as $abpet_social ) { ?>
                                    <a class="abpet_speaker_social" href="<?php echo esc_url( $abpet_social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $abpet_social['label'] ); ?>">
                                        <i class="<?php echo esc_attr( $abpet_social['icon'] ); ?>" aria-hidden="true"></i>
                                        <span><?php echo esc_html( $abpet_social['label'] ); ?></span>
                                    </a>
								<?php } ?>
                            </div>
						<?php } ?>
                    </header>
					<?php if ( $abpet_speaker['has_bio'] ) { ?>
                        <div class="abpet_speaker_bio_wrap">
                            <h2 class="abpet_speaker_block_title">
                                <span class="abpet_speaker_block_icon" aria-hidden="true"><i class="fas fa-quote-left"></i></span>
								<?php esc_html_e( 'Biography', 'abp-event-ticket' ); ?>
                            </h2>
                            <div class="abpet_speaker_bio">
								<?php
									// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
									echo wp_kses_post( apply_filters( 'the_content', $abpet_speaker['bio'] ) );
								?>
                            </div>
                        </div>
					<?php } ?>
                </article>
            </div>
        </main>
		<?php
	}
	if ( wp_is_block_theme() ) {
		?>
        <footer class="wp-block-template-part">
			<?php block_footer_area(); ?>
        </footer>
		<?php wp_footer(); ?>
        </body>
        </html>
		<?php
	} else {
		get_footer();
	}
