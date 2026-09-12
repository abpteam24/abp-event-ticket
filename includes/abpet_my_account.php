<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
if ( ! class_exists( 'ABPET_My_Account' ) ) {
		class ABPET_My_Account {
			const ENDPOINT = 'abpet-bookings';
			public function __construct() {
				add_action( 'init', array( $this, 'register_endpoint' ) );
				add_filter( 'woocommerce_get_query_vars', array( $this, 'query_vars' ) );
				add_filter( 'woocommerce_account_menu_items', array( $this, 'menu_items' ), 20 );
				add_action( 'woocommerce_account_' . self::ENDPOINT . '_endpoint', array( $this, 'render_bookings' ) );
				add_action( 'admin_init', array( $this, 'maybe_flush_rules' ) );
			}
			public function register_endpoint(): void {
				add_rewrite_endpoint( self::ENDPOINT, EP_ROOT | EP_PAGES );
			}
			public function query_vars( $vars ) {
				$vars[ self::ENDPOINT ] = self::ENDPOINT;
				return $vars;
			}
			public function menu_items( $items ) {
				$menu = array();
				foreach ( $items as $key => $value ) {
					$menu[ $key ] = $value;
					if ( 'orders' === $key ) {
						$menu[ self::ENDPOINT ] = ABPET_Function::label() . ' ' . __( 'Bookings', 'abp-event-ticket' );
					}
				}
				if ( 'dashboard' === self::ENDPOINT || ! array_key_exists( self::ENDPOINT, $menu ) ) {
					$menu[ self::ENDPOINT ] = ABPET_Function::label() . ' ' . __( 'Bookings', 'abp-event-ticket' );
				}
				return $menu;
			}
			public function maybe_flush_rules(): void {
				if ( ! get_option( 'abpet_my_account_flushed' ) ) {
					$this->register_endpoint();
					flush_rewrite_rules( false );
					update_option( 'abpet_my_account_flushed', 1 );
				}
			}
			public function render_bookings(): void {
				$user_id = get_current_user_id();
				?>
                <div class="abpet_area abpet_my_account">
                    <h4><?php
						/* translators: %s: booking post type label. */
						echo esc_html( sprintf( esc_html__( 'My %s Bookings', 'abp-event-ticket' ), ABPET_Function::label() ) ); ?></h4>
                    <div class="_divider_xs"></div>
					<?php
						if ( ! $user_id ) {
							echo '<p>' . esc_html__( 'Please login to see your bookings.', 'abp-event-ticket' ) . '</p>';
							return;
						}
						$limit = absint( ABPET_Function::get_option( 'abpet_per_page_item', 20 ) );
						if ( $limit < 1 ) {
							$limit = 20;
						}
						$page = isset( $_GET['abpet_page'] ) ? absint( $_GET['abpet_page'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( $page < 1 ) {
							$page = 1;
						}
						$offset     = ( $page - 1 ) * $limit;
						$user_email = wp_get_current_user()->user_email ?? '';
						$filters    = array(
							'user_id'   => $user_id,
							'status'    => 'all',
							'order_by'  => 'created_at',
							'order_dir' => 'DESC',
							'cache'     => false,
						);
						if ( ! empty( $user_email ) ) {
							$filters['user_email'] = $user_email;
						}
						$total    = ABPET_Query::get_booking_query( $filters, 0, 0, true );
						$bookings = ABPET_Query::get_booking_query( $filters, $limit, $offset );
						if ( ! empty( $bookings ) && is_array( $bookings ) ) {
							foreach ( $bookings as $booking_item ) {
								$this->booking_card( $booking_item );
							}
							$this->pagination( $total, $limit, $page );
						} else {
							echo '<p>' . esc_html__( 'You have no bookings yet.', 'abp-event-ticket' ) . '</p>';
						}
					?>
                </div>
				<?php
			}
			private function booking_card( $booking_item ): void {
				$post_id              = absint( $booking_item['post_id'] ?? 0 );
				$order_status         = $booking_item['order_status'] ?? '';
				$created_at           = $booking_item['created_at'] ?? '';
				$event_date           = $booking_item['event_date'] ?? '';
				$session_time         = $booking_item['session_time'] ?? '';
				$seat_type            = $booking_item['seat_type'] ?? '';
				$sp_id                = absint( $booking_item['sp_id'] ?? 0 );
				$total                = (float) ( $booking_item['total'] ?? 0 );
				$ticket_infos         = json_decode( $booking_item['ticket_info'] ?? '', true ) ?: array();
				$additional_infos     = json_decode( $booking_item['ex_info'] ?? '', true ) ?: array();
				$passenger_infos      = json_decode( $booking_item['pass_info'] ?? '', true ) ?: array();
				$show_additional_info = ABPET_Function::on_off( 'additional_info' ) && ! empty( $additional_infos );
				$show_client_info     = ABPET_Function::on_off( 'client_info' ) && ! empty( $passenger_infos );
				?>
                <div class="_section_card _mar_b">
                    <div class="_fj_between">
                        <div>
                            <h5 style="margin:0 0 4px;"><?php ABPET_Layout::title( array( 'post_id' => $post_id ) ); ?></h5>
                            <p style="margin:0;" class="abp_color_gray">#<?php echo esc_html( $booking_item['order_id'] ?? '' ); ?> - <?php echo esc_html( ABPET_Function::date_format( $created_at ) ); ?></p>
                        </div>
                        <span class="abp_tag"><?php echo esc_html( ABPET_Layout::status_text( $order_status ) ); ?></span>
                    </div>
                    <div class="_divider_xs"></div>
                    <ul class="abp">
						<?php if ( ! empty( $event_date ) ) { ?>
                            <li><strong><?php esc_html_e( 'Event Date', 'abp-event-ticket' ); ?></strong> <?php echo esc_html( ABPET_Function::date_format( $event_date . ' ' . $session_time ) ); ?></li>
						<?php } ?>
						<?php if ( ! empty( $booking_item['payment_method'] ) ) { ?>
                            <li><strong><?php esc_html_e( 'Payment Method', 'abp-event-ticket' ); ?></strong> <?php echo esc_html( $booking_item['payment_method'] ); ?></li>
						<?php } ?>
                    </ul>
					<?php if ( ! empty( $ticket_infos ) ) { ?>
                        <div class="_divider_xs"></div>
                        <div class="_group_content">
							<?php ABPET_Layout::ticket_info( $ticket_infos, $post_id, $seat_type, $sp_id ); ?>
                        </div>
					<?php } ?>
					<?php if ( $show_additional_info ) { ?>
                        <div class="_divider_xs"></div>
                        <div class="_group_content">
							<?php ABPET_Layout::additional_info( $additional_infos ); ?>
                        </div>
					<?php } ?>
					<?php if ( $show_client_info ) { ?>
                        <div class="_divider_xs"></div>
                        <div class="_group_content">
							<?php ABPET_Layout::client_info( $passenger_infos ); ?>
                        </div>
					<?php } ?>
                    <div class="_divider_xs"></div>
                    <p style="margin:0;"><strong><?php esc_html_e( 'Total', 'abp-event-ticket' ); ?></strong> : <?php echo $total > 0 ? wp_kses_post( function_exists( 'wc_price' ) ? wc_price( $total ) : number_format_i18n( $total, 2 ) ) : esc_html__( 'FREE', 'abp-event-ticket' ); ?></p>
					<?php do_action( 'abpet_my_account_partial_payment', $booking_item ); ?>
                    <div class="_divider_xs"></div>
                    <div class="_fj_start _f_wrap_gap_xxs">
						<?php do_action( 'abpet_my_account_booking_actions', $booking_item ); ?>
                    </div>
                </div>
				<?php
			}
			private function pagination( $total, $limit, $page ): void {
				if ( empty( $limit ) || (int) $total <= (int) $limit ) {
					return;
				}
				$pages = (int) ceil( (int) $total / (int) $limit );
				if ( $pages < 2 ) {
					return;
				}
				$base = wc_get_account_endpoint_url( self::ENDPOINT );
				?>
                <div class="_fj_center _mar_b">
                    <div class="_pagination">
						<?php for ( $i = 1; $i <= $pages; $i ++ ) {
							$is_current = ( $i === (int) $page );
							$url        = ( 1 === $i ) ? $base : add_query_arg( 'abpet_page', $i, $base ); ?>
							<?php if ( $is_current ) { ?>
                                <span class="_page_link _page_active"><?php echo esc_html( $i ); ?></span>
							<?php } else { ?>
                                <a class="_page_link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $i ); ?></a>
							<?php } ?>
						<?php } ?>
                    </div>
                </div>
				<?php
			}
		}
		new ABPET_My_Account();
	}
