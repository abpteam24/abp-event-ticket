<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action(
		'abpet_minimal_template',
		function ( $params = [] ): void {
			$post_ids     = $params['all_post'] ?? [];
			$global_order = $params['global_order'] ?? '';
			$column       = min( 4, max( 1, absint( $params['column'] ?? 3 ) ) );
			$show_post    = absint( ( $params['show'] ?? 0 ) ?: $column * 4 );
			$post_count   = 0;
			$args         = [
				'total'     => count( $post_ids ),
				'page_item' => $show_post,
				'style'     => $params['pagination_style'] ?? ( $params['pagination-style'] ?? 'live' ),
			];
			if ( empty( $post_ids ) ) {
				ABPET_Layout::layout_warning_info( 'not_found' );
				return;
			}
			?>
			<div class="abpet_minimal item_<?php echo esc_attr( $column ); ?>">
				<?php foreach ( $post_ids as $post_id ) :
					$post_count++;
					$post_infos = ABPET_Function::get_all_meta( $post_id );
					$category    = $post_infos['abpet_category'] ?? '';
					$location    = $post_infos['abpet_location'] ?? '';
					$url         = get_permalink( $post_id );
					?>
					<article class="pagination_item minimal_item <?php echo esc_attr( $show_post >= $post_count ? '' : 'abp_close' ); ?>" data-cat_id="<?php echo esc_attr( $category ); ?>" data-loc_id="<?php echo esc_attr( $location ); ?>">
						<?php ABPET_Layout::image( $post_id, '', '', 'minimal_image' ); ?>
						<div class="minimal_content">
							<?php if ( $global_order ) : ?>
								<button type="button" class="minimal_title select_post" data-post_id="<?php echo esc_attr( $post_id ); ?>"><?php ABPET_Layout::title( $post_infos ); ?></button>
							<?php else : ?>
								<a class="minimal_title" href="<?php echo esc_url( $url ); ?>"><?php ABPET_Layout::title( $post_infos ); ?></a>
							<?php endif; ?>
							<?php if ( $category || $location ) : ?>
								<div class="minimal_meta">
									<?php echo esc_html( implode( ' / ', array_filter( [ ABPET_Function::category_value( $category ), ABPET_Function::location_value( $location ) ] ) ) ); ?>
								</div>
							<?php endif; ?>
						</div>
						<?php if ( $global_order ) : ?>
							<button type="button" class="_btn_theme_xs minimal_action select_post" data-post_id="<?php echo esc_attr( $post_id ); ?>"><?php esc_html_e( 'Book Now', 'abp-event-ticket' ); ?></button>
						<?php else : ?>
							<a class="_btn_theme_xs minimal_action" href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'View Event', 'abp-event-ticket' ); ?></a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
			<?php
			if ( ( $params['pagination'] ?? 'yes' ) !== 'no' ) {
				do_action( 'abpet_pagination', $args );
			}
		},
		10,
		2
	);
