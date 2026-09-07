<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'ABPET_Customer_Account' ) ) {
	class ABPET_Customer_Account {
		public function __construct() {
			add_action( 'woocommerce_account_dashboard', [ $this, 'render_orders' ] );
		}

		public function render_orders(): void {
			if ( ! is_user_logged_in() ) {
				return;
			}
			$orders = ABPET_Function::get_customer_orders();
			?>
			<section class="abpet_customer_orders">
				<h2><?php esc_html_e( 'My Event Orders', 'abp-event-ticket' ); ?></h2>
				<?php if ( empty( $orders ) ) : ?>
					<p><?php esc_html_e( 'You have not placed any event orders yet.', 'abp-event-ticket' ); ?></p>
				<?php else : ?>
					<div class="abpet_customer_orders_list">
						<?php foreach ( $orders as $order ) : ?>
							<article class="abpet_customer_order">
								<div>
									<strong><?php
									/* translators: %s: WooCommerce order number. */
									printf( esc_html__( 'Order #%s', 'abp-event-ticket' ), esc_html( $order->get_order_number() ) );
									?></strong>
									<time datetime="<?php echo esc_attr( $order->get_date_created() ? $order->get_date_created()->date( 'c' ) : '' ); ?>">
										<?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
									</time>
								</div>
								<div>
									<span><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
									<strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
								</div>
								<a class="button" href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
									<?php esc_html_e( 'View Details', 'abp-event-ticket' ); ?>
								</a>
							</article>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</section>
			<?php
		}
	}
	new ABPET_Customer_Account();
}
