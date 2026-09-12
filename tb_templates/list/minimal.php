<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abpet_minimal_template', function ( $params = [] ): void {
		$post_ids     = $params['all_post'] ?? [];
		$global_order = $params['global_order'] ?? '';
		$column       = $params['column'] ?? 3;
		$show_post    = absint( ( $params['show'] ?? 0 ) ?: ( $column * 3 ) );
		$post_count   = 0;
		$args         = [
			'total'     => count( $post_ids ),
			'page_item' => $show_post,
			'style'     => $params['pagination_style'] ?? ( $params['pagination-style'] ?? 'live' ),
		];
		//echo '<pre>';print_r($show_post);echo '</pre>';
		if ( empty( $post_ids ) ) {
			ABPET_Layout::layout_warning_info( 'not_found' );
			return;
		}
		?>
        <div class="abpet_minimal item_<?php echo esc_attr( $column ); ?>">
			<?php foreach ( $post_ids as $post_id ) :
				$post_count ++;
				$post_infos = ABPET_Function::get_all_meta( $post_id );
				$category   = $post_infos['abpet_category'] ?? '';
				$location   = $post_infos['abpet_location'] ?? '';
				$url        = get_permalink( $post_id );
				?>
                <div class="pagination_item minimal_item <?php echo esc_attr( $show_post >= $post_count ? '' : 'abp_close' ); ?>" data-cat_id="<?php echo esc_attr( $category ); ?>" data-loc_id="<?php echo esc_attr( $location ); ?>">
					<?php ABPET_Layout::image( $post_id, '', '', 'minimal_image' ); ?>
                    <div class="minimal_content">
                        <a class="abp list_title" href="<?php echo esc_url( $url ); ?>" target="_blank">
		                    <?php ABPET_Layout::title( $post_infos ); ?>
                        </a>
                        <div class="_divider_xxs"></div>
                        <div class="_fj_between">
		                    <?php $price = ABPET_Function::get_min_price( $post_id ); ?>
                            <span class="price_value">
                                                <?php esc_html_e( 'Min Price :', 'abp-event-ticket' );
	                                                echo $price > 0 ? wp_kses_post( wc_price( $price ) ) : esc_html__( 'Free', 'abp-event-ticket' ); ?>
                                            </span>
		                    <?php if ( ! empty( $global_order ) ) { ?>
                                <button type="button" class="_btn_light_theme_xxs select_post" data-post_id="<?php echo esc_attr( $post_id ); ?>">
				                    <?php esc_html_e( 'Book Now', 'abp-event-ticket' ); ?>
                                </button>
		                    <?php } else { ?>
                                <button type="button" class="_btn_light_theme_xxs" data-href="<?php echo esc_url( $url ); ?>" data-blank="_blank">
				                    <?php esc_html_e( 'Book Now', 'abp-event-ticket' ); ?>
                                </button>
		                    <?php } ?>
                        </div>
                    </div>
                </div>
			<?php endforeach; ?>
        </div>
		<?php
		if ( ( $params['pagination'] ?? 'yes' ) !== 'no' ) {
			do_action( 'abpet_pagination', $args );
		}
	}, 10, 2 );