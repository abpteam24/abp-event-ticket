<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPET_Documentation' ) ) {
		class ABPET_Documentation {
			public function __construct() {
				add_action( 'abpet_load_documentation', array( $this, 'load_documentation' ) );
			}
			public function load_documentation(): void {
				?>
                <div class="_abp_panel_max_1200_mar_auto abp_documentation">
                    <div class="_panel_head">
                        <h3 class="_abp_gap_xs"><span>📘</span> <?php esc_html_e( 'Documentation — How to Run Your Events', 'abp-event-ticket' ); ?></h3>
                    </div>
                    <div class="_panel_body_fd_column_gap_xs">
                        <div class="_section_xs">
                            <p class="_abp"><?php esc_html_e( 'This is the main workflow: what each section is, and what to do first. Follow the steps in order and you will be selling tickets in about 10–15 minutes.', 'abp-event-ticket' ); ?></p>
                        </div>
                        <div class="_divider_xs"></div>
                        <div class="_section_xs">
                            <h6 class="_abp_fj_between">
                                <span><b class="_color_theme"><?php esc_html_e( 'Step 1:', 'abp-event-ticket' ); ?></b> <?php esc_html_e( 'Check the Status tab', 'abp-event-ticket' ); ?></span>
                                <a class="_btn_white_xs" href="<?php echo esc_url( ABPET_Function::build_url( 'status' ) ); ?>"><?php esc_html_e( 'Open Status', 'abp-event-ticket' ); ?></a>
                            </h6>
                            <p><?php esc_html_e( 'The environment check shows if everything is ready (wordpress, PHP, WooCommerce). Everything green means you can start. If WooCommerce is missing, click Install & Activate right there.', 'abp-event-ticket' ); ?></p>
                        </div>
                        <div class="_section_xs">
                            <h6 class="_abp_fj_between">
                                <span><b class="_color_theme"><?php esc_html_e( 'Step 2:', 'abp-event-ticket' ); ?></b> <?php esc_html_e( 'Keep or change the settings (Configuration)', 'abp-event-ticket' ); ?></span>
                                <a class="_btn_white_xs" href="<?php echo esc_url( ABPET_Function::build_url( 'configuration' ) ); ?>"><?php esc_html_e( 'Open Configuration', 'abp-event-ticket' ); ?></a>
                            </h6>
                            <p><?php esc_html_e( 'Optional. Change the displayed names, currency, seat/ticket labels, and turn features on/off (Category, Brand, FAQ, Terms…). The default settings already work.', 'abp-event-ticket' ); ?></p>
                        </div>
                        <div class="_section_xs">
                            <h6 class="_abp_fj_between">
                                <span><b class="_color_theme"><?php esc_html_e( 'Step 3:', 'abp-event-ticket' ); ?></b> <?php esc_html_e( 'Prepare Global Data', 'abp-event-ticket' ); ?></span>
                                <a class="_btn_white_xs" href="<?php echo esc_url( ABPET_Function::build_url( 'global' ) ); ?>"><?php esc_html_e( 'Open Global Data', 'abp-event-ticket' ); ?></a>
                            </h6>
                            <p><?php esc_html_e( 'Create the shared lists first: Dates (event schedule), Locations, and — if enabled — Categories, Organizers and Brands. These appear as dropdown options when you create an event.', 'abp-event-ticket' ); ?></p>
                        </div>
                        <div class="_section_xs">
                            <h6 class="_abp_fj_between">
                                <span><b class="_color_theme"><?php esc_html_e( 'Step 4:', 'abp-event-ticket' ); ?></b> <?php esc_html_e( 'Create an Event', 'abp-event-ticket' ); ?></span>
                                <a class="_btn_white_xs" href="<?php echo esc_url( ABPET_Function::build_url( 'posts' ) ); ?>"><?php esc_html_e( 'Open Event List', 'abp-event-ticket' ); ?></a>
                            </h6>
                            <p><?php esc_html_e( 'Open the Event List, press the + button, then give the event a title, image, description, choose date & time, location/category, and add ticket types with prices. Done — the event appears on its own page automatically.', 'abp-event-ticket' ); ?></p>
                        </div>
                        <div class="_section_xs">
                            <h6 class="_abp_fj_between">
                                <span><b class="_color_theme"><?php esc_html_e( 'Step 5:', 'abp-event-ticket' ); ?></b> <?php esc_html_e( 'Set up the Seat/Ticket Plan', 'abp-event-ticket' ); ?></span>
                                <a class="_btn_white_xs" href="<?php echo esc_url( ABPET_Function::build_url( 'sp' ) ); ?>"><?php esc_html_e( 'Open Seat Plan', 'abp-event-ticket' ); ?></a>
                            </h6>
                            <p><?php esc_html_e( 'Optional, only for assigned seating. Build the hall layout, place seats, and set a ticket type per seat. If you skip it, the event simply sells tickets by quantity.', 'abp-event-ticket' ); ?></p>
                        </div>
                        <div class="_section_xs">
                            <h6 class="_abp_fj_between">
                                <span><b class="_color_theme"><?php esc_html_e( 'Step 6:', 'abp-event-ticket' ); ?></b> <?php esc_html_e( 'Check the storefront and orders', 'abp-event-ticket' ); ?></span>
                            </h6>
                            <p><?php esc_html_e( 'View the event page on your site — customers pick a date and buy. Every purchase shows under Orders, and a booked order status automatically reduces the available seats. The plugin also creates a Booking page and an Event List page (shortcodes: [abpet-booking] for booking, [abpet-post] for the list). Put the list page in your menu so customers can browse all events.', 'abp-event-ticket' ); ?></p>
                        </div>
                        <div class="_divider_xs"></div>
                        <div class="_section_xs">
                            <h6 class="_abp"><?php esc_html_e( 'What is what — quick look at the tabs', 'abp-event-ticket' ); ?></h6>
                            <div class="_fd_column">
                                <p class="_abp">• <b><?php echo esc_html( ABPET_Function::label() ); ?> List</b> — <?php esc_html_e( 'all your events; create and edit them.', 'abp-event-ticket' ); ?></p>
                                <p class="_abp">• <b>Orders</b> — <?php esc_html_e( 'customer purchases and booking status.', 'abp-event-ticket' ); ?></p>
                                <p class="_abp">• <b>Ticket/Seat Plan</b> — <?php esc_html_e( 'seat layouts for assigned seating.', 'abp-event-ticket' ); ?></p>
                                <p class="_abp">• <b>Global Data</b> — <?php esc_html_e( 'shared lists: Dates, Location, Category, Organizer, Brand, Additional Services, Client Form, Resources (FAQ / Terms).', 'abp-event-ticket' ); ?></p>
                                <p class="_abp">• <b>Configuration</b> — <?php esc_html_e( 'global settings and feature switches.', 'abp-event-ticket' ); ?></p>
                                <p class="_abp">• <b>Status</b> — <?php esc_html_e( 'health check and quick tools (create pages, demo data).', 'abp-event-ticket' ); ?></p>
                                <p class="_abp">• <b>Documentation</b> — <?php esc_html_e( 'this guide.', 'abp-event-ticket' ); ?></p>
                            </div>
                        </div>
                        <div class="_section_xs">
                            <h6 class="_abp"><?php esc_html_e( 'Good to know', 'abp-event-ticket' ); ?></h6>
                            <div class="_fd_column">
                                <p class="_abp">• <?php esc_html_e( 'The booked order status (Configuration → Order) decides when seats are deducted from the inventory.', 'abp-event-ticket' ); ?></p>
                                <p class="_abp">• <?php esc_html_e( 'Customers can pick a date and time from the list shown on the event page.', 'abp-event-ticket' ); ?></p>
                                <p class="_abp">• <?php esc_html_e( 'New to the plugin? Use Status → quick tools to create sample pages and demo data and try everything before going live.', 'abp-event-ticket' ); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}
		}
		new ABPET_Documentation();
	}