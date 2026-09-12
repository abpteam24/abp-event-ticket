<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abpet_default_template', function ( $params = [] ) {
		$post_ids = $params['all_post'] ?? [];
		if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
			$global_order      = $params['global_order'] ?? '';
			$style             = ( $params['style'] ?? 'grid' ) ?: 'grid';
			$column            = $params['column'] ?? 3;
			$related           = $params['related'] ?? '';
			$show_post         = absint( ( $params['show'] ?? 0 ) ?: ( $column * 3 ) );
			$class             = $style == 'grid' && $column > 1 ? 'abpet_grid item_' . $column : 'abpet_lists item_' . $column;
			$class             = ! empty( $related ) ? 'abpet_grid ' : $class;
			$post_count        = 0;
			$args['total']     = sizeof( $post_ids );
			$args['page_item'] = $show_post;
			?>
            <div class="<?php echo esc_attr( $class ); ?>">
				<?php foreach ( $post_ids as $post_id ) {
					$post_count ++;
					$post_infos = ABPET_Function::get_all_meta( $post_id );
					$cat_id     = $post_infos['abpet_category'] ?? '';
					$loc_id     = $post_infos['abpet_location'] ?? '';
					$url        = get_the_permalink( $post_id );
					$show_class = $show_post >= $post_count ? '' : 'abp_close';
					//echo '<pre>';print_r($filter_args);echo '</pre>';
					?>
                    <div class="pagination_item item_box_1 <?php echo esc_attr( $show_class ); ?>" data-cat_id="<?php echo esc_attr( $cat_id ); ?>" data-loc_id="<?php echo esc_attr( $loc_id ); ?>">
                        <div class="item_head">
							<?php
								ABPET_Layout::image( $post_id );
								ABPET_Layout::category( $post_infos, 'ribbon' );
								ABPET_Layout::capacity( $post_infos, 'ribbon publish' );
							?>
                        </div>
                        <div class="item_body">
                            <div class="">
                                <a class="abp list_title" href="<?php echo esc_url( $url ); ?>" target="_blank">
									<?php ABPET_Layout::title( $post_infos ); ?>
                                </a>
                                <div class="_divider_xxs"></div>
								<?php
									ABPET_Layout::brand( $post_infos, 'publish' );
									ABPET_Layout::organizer( $post_infos, 'publish' );
									ABPET_Layout::item_feature( $post_infos['post_feature'] ?? '' );
									ABPET_Layout::description( $post_infos ); ?>
                            </div>
                            <div>
                                <div class="_divider_xxs"></div>
                                <div class="_fj_between">
									<?php $price = ABPET_Function::get_min_price( $post_id ); ?>
                                    <span class="price_value">
                                                <?php esc_html_e( 'Min Price :', 'abp-event-ticket' );
	                                                echo $price > 0 ? wp_kses_post( wc_price( $price ) ) : esc_html__( 'Free', 'abp-event-ticket' ); ?>
                                            </span>
									<?php if ( ! empty( $global_order ) ) { ?>
                                        <button type="button" class="_btn_theme_xs select_post" data-post_id="<?php echo esc_attr( $post_id ); ?>">
											<?php esc_html_e( 'Book Now', 'abp-event-ticket' ); ?>
                                        </button>
									<?php } else { ?>
                                        <button type="button" class="_btn_theme_xs" data-href="<?php echo esc_url( $url ); ?>" data-blank="_blank">
											<?php esc_html_e( 'Book Now', 'abp-event-ticket' ); ?>
                                        </button>
									<?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
			<?php if ( empty( $related ) && ( $params['pagination'] ?? 'yes' ) !== 'no' ) {
				$args['style'] = $params['pagination_style'] ?? ( $params['pagination-style'] ?? 'live' );
				do_action( 'abpet_pagination', $args );
			} ?>
			<?php
		} else {
			ABPET_Layout::layout_warning_info( 'not_found' );
		}
	}, 10, 2 );