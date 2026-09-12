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
                <div class="abp_panel_max_1200_mar_auto abp_documentation">
                    <div class="_panel_head">
                        <h3 class="abp_gap_xs"><span>📘</span> <?php esc_html_e( 'Documentation — How to Use ABP Event Ticket', 'abp-event-ticket' ); ?></h3>
                    </div>
                    <div class="_panel_body_fd_column_gap">
                        <div class="dash_card">
                            <div class="dash_card_body">
                                <p class="dash_body_lead"><?php esc_html_e( 'Set up and start selling tickets in ~15 minutes.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text"><?php esc_html_e( 'This guide explains the plugin step by step: what every menu does, in which order to set things up, and how tickets and seat plans work.', 'abp-event-ticket' ); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🚀</span> <?php esc_html_e( 'Quick Start — Follow these steps in order', 'abp-event-ticket' ); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <div class="_grid_400">
<?php
					$this->step_card(
											'1',
											__( 'Check Status', 'abp-event-ticket' ),
											__( 'Open the Dashboard and verify everything is ready.', 'abp-event-ticket' ),
											__( 'The Dashboard shows your WordPress, PHP, and WooCommerce versions. Everything green means you can continue. If WooCommerce is missing, install it from the System Status section — it is required for ticket payments.', 'abp-event-ticket' ),
											'dashboard',
											__( 'Open Dashboard', 'abp-event-ticket' )
										);
										$this->step_card(
											'2',
											__( 'Configuration ON/OFF', 'abp-event-ticket' ),
											__( 'Decide which features you need.', 'abp-event-ticket' ),
											__( 'Configuration → ON/OFF turns features on/off for the whole site (Category, Organizer, Brand, Seat Plan, Additional Services, FAQ, Terms…). The defaults work out of the box, so you can skip this step.', 'abp-event-ticket' ),
											'configuration',
											__( 'Open Configuration', 'abp-event-ticket' )
										);
										$this->step_card(
											'3',
											__( 'Create Global Lists', 'abp-event-ticket' ),
											__( 'Build the reusable lists every event shares.', 'abp-event-ticket' ),
											__( 'Global Data holds Dates & Schedules, Locations, and (if enabled) Categories, Organizers, Brands, Features, Additional Services, Client Forms, and Resources. Create them once — they appear as dropdowns when creating an event.', 'abp-event-ticket' ),
											'global',
											__( 'Open Global Data', 'abp-event-ticket' )
										);
										$this->step_card(
											'4',
											__( 'Ticket Types & Seat Plans', 'abp-event-ticket' ),
											__( 'Create the ticket categories and seat layouts.', 'abp-event-ticket' ),
											__( 'Ticket Types are the reusable categories you sell (Adult, Child, VIP…). Seat Plans are visual layouts for assigned seating (theater, bus, hall). A full explanation of Ticket vs Seat Plan is below.', 'abp-event-ticket' ),
											'sp',
											__( 'Open Ticket/Seat Plan', 'abp-event-ticket' )
										);
										$this->step_card(
											'5',
											__( 'Create an Event', 'abp-event-ticket' ),
											__( 'Add your first event and set its tickets or seats.', 'abp-event-ticket' ),
											__( 'Press the + button, then add a title, image, description, schedule (date & time), location/category, and configure tickets or seat plan with prices and quantities. The event gets its own page automatically.', 'abp-event-ticket' ),
											'posts',
											__( 'Open Event List', 'abp-event-ticket' )
										);
										$this->step_card(
											'6',
											__( 'Add Shortcodes & Pages', 'abp-event-ticket' ),
											__( 'Publish the booking and event-list pages.', 'abp-event-ticket' ),
											__( 'The Dashboard creates a Booking page ([abpet-booking]) and an Event List page ([abpet-post]) for you via the System Status section. Put the list page in your menu so customers can browse every event; you can also insert the shortcodes into any page.', 'abp-event-ticket' ),
											'dashboard',
											__( 'Open Dashboard', 'abp-event-ticket' )
										);
										$this->step_card(
											'7',
											__( 'Test & Go Live', 'abp-event-ticket' ),
											__( 'Try it with demo data, then watch the orders.', 'abp-event-ticket' ),
											__( 'New to the plugin? Import the built-in demo data from the Dashboard’s System Status section to explore everything safely. When live, every purchase appears under Orders and paid orders reduce the available seats.', 'abp-event-ticket' ),
											'dashboard',
											__( 'Open Dashboard', 'abp-event-ticket' )
										);
									?>
                                </div>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🔛</span> <?php esc_html_e( 'The ON/OFF tab — what it is and why it matters', 'abp-event-ticket' ); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text"><?php esc_html_e( 'Configuration → ON/OFF is the master control panel of the plugin. Every feature is listed there with a simple switch. Each switch controls a whole feature area of the site — not just one event.', 'abp-event-ticket' ); ?></p>
                                <div class="_grid_400">
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">🟢 <?php esc_html_e( 'Switch ON (default)', 'abp-event-ticket' ); ?></p>
                                            <p class="dash_body_text"><?php esc_html_e( 'The feature is active everywhere. You see its menus, its global lists, and its fields on the event edit page.', 'abp-event-ticket' ); ?></p>
                                        </div>
                                    </div>
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">⚪ <?php esc_html_e( 'Switch OFF', 'abp-event-ticket' ); ?></p>
                                            <p class="dash_body_text"><?php esc_html_e( 'The feature is completely hidden from the whole site — its menus, forms, and fields disappear. This keeps your admin screen clean and shows only what you use.', 'abp-event-ticket' ); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <p class="dash_body_lead"><?php esc_html_e( 'Popular switches you may want to change:', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Seat Plan', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'turn OFF if you only sell tickets by quantity and never need reserved seating.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Category / Organizer / Brand', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'turn OFF the ones you do not use to hide their menus and event fields.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Additional Services', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'turn OFF if you have no extras (meals, merchandise) to sell.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Attendee Form (Custom Attendee)', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'turn OFF if you do not need to collect attendee names/details at checkout.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'FAQ / Terms & Conditions', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'turn OFF if you do not need these blocks on the event page.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text"><b><?php esc_html_e( 'Tip:', 'abp-event-ticket' ); ?></b> <?php esc_html_e( 'You can change the switches at any time. Turning a feature back ON restores its menus and event fields instantly, so feel free to experiment.', 'abp-event-ticket' ); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🎟️</span> <?php esc_html_e( 'Ticket Type vs Seat Plan — made clear', 'abp-event-ticket' ); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text"><?php esc_html_e( 'Every event sells tickets in one of two ways. You choose this on the event edit page in the Ticket Type settings:', 'abp-event-ticket' ); ?></p>
                                <div class="_grid_400">
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">🪑 <?php esc_html_e( 'Seat Plan (assigned seating)', 'abp-event-ticket' ); ?></p>
                                            <p class="dash_body_text"><?php esc_html_e( 'Customers buy a specific seat. You build a visual layout (theater, bus, hall) once, assign a ticket type to each seat area, and the customer picks their seat on the event page. Best for concerts, theaters, buses, and stadiums.', 'abp-event-ticket' ); ?></p>
                                        </div>
                                    </div>
                                    <div class="dash_card">
                                        <div class="dash_card_body">
                                            <p class="dash_body_lead">🎫 <?php esc_html_e( 'Ticket Type (general admission)', 'abp-event-ticket' ); ?></p>
                                            <p class="dash_body_text"><?php esc_html_e( 'Customers buy a quantity of a ticket type (Adult, Child, VIP) without a fixed seat. Best for festivals, conferences, workshops, and online events.', 'abp-event-ticket' ); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <p class="dash_body_lead">💡 <?php esc_html_e( 'What is a "Ticket Type"?', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text"><?php esc_html_e( 'A reusable category with a label, a color, an icon, and a seat prefix used for automatic numbering (e.g. VIP-01, B-01). Create them in Ticket/Seat Plan → Ticket Type List. Reuse them in many events and change the price per event.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_lead">🛠️ <?php esc_html_e( 'How to create a Seat Plan:', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text"><?php esc_html_e( 'Open Ticket/Seat Plan → press Add New Seat Plan, then drag and drop seats and other cells (entrance, aisle, window, stairs) to build your layout. Give it a name, assign ticket types to seats, and save. Reusable on any number of events.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_lead">💰 <?php esc_html_e( 'Where do prices go?', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text"><?php esc_html_e( 'Prices, quantities, reserved stock, and min/max per order are set per event (Event edit → Ticket Configuration). The global Ticket Type only defines the label and style; the price is decided on each event.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_lead">⚙️ <?php esc_html_e( 'One or many ticket types?', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text"><?php esc_html_e( 'On the event edit page you can enable "Multiple Ticket Type" to sell several categories (Adult + Child + VIP) at once — or keep it off to sell a single ticket type per event.', 'abp-event-ticket' ); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🗂️</span> <?php esc_html_e( 'Every menu explained', 'abp-event-ticket' ); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text">• <b><?php echo esc_html( ABPET_Function::label() ); ?> List</b> — <?php esc_html_e( 'all your events: create, edit, clone, trash, restore, and permanently delete events.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Orders', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'every customer purchase with booking details, status, check-in, and totals.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Ticket/Seat Plan', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'create reusable ticket types, seat plans, and decor (non-seat) cells for assigned seating.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Global Data', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'shared reusable lists used by all events (see the sub-tabs list below).', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Configuration', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'global settings: general (label, slug, currency…), the ON/OFF switches, contact information, colors, and CSS.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Dashboard', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'overview and charts, plus a System Status health check with quick tools: install WooCommerce, create the booking/list/gallery pages, and import or remove demo data.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Documentation', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'this guide.', 'abp-event-ticket' ); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🌍</span> <?php esc_html_e( 'Inside Global Data', 'abp-event-ticket' ); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Dates', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'global date and time formatting, sale buffer, and advance booking horizon.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Location', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'reusable venue list used by every event.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Category / Organizer / Brand', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'optional classifications; they only appear here when switched ON in Configuration.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Features', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'highlight badges shown on the event page (e.g. E-Ticket, VIP Access, Parking).', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Additional Services', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'optional add-ons with their own prices (meals, merchandise, transfers).', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Client Form', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'attendee fields collected at checkout (name, email, phone…).', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <b><?php esc_html_e( 'Resources', 'abp-event-ticket' ); ?></b> — <?php esc_html_e( 'reusable FAQ and Terms & Conditions blocks.', 'abp-event-ticket' ); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">🔑</span> <?php esc_html_e( 'Shortcodes you can place anywhere', 'abp-event-ticket' ); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text">• <code>[abpet-booking]</code> — <?php esc_html_e( 'full event listing with search, filters, ticket selection, and booking flow. Ideal for your Booking page.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <code>[abpet-post]</code> — <?php esc_html_e( 'event listing without the booking wrapper. Ideal for your Event List page.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <code>[abpet-gallery]</code> — <?php esc_html_e( 'displays event images in a gallery or slider.', 'abp-event-ticket' ); ?></p>
                            </div>
                        </div>
                        <div class="dash_card">
                            <div class="dash_card_head">
                                <h4><span class="dash_step">✅</span> <?php esc_html_e( 'Good to know', 'abp-event-ticket' ); ?></h4>
                            </div>
                            <div class="dash_card_body">
                                <p class="dash_body_text">• <?php esc_html_e( 'The booked order status (Configuration → General → Booked Status) decides when seats are deducted from inventory. Set it to the statuses you consider "paid".', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <?php esc_html_e( 'Customers pick a date and time from the schedule shown on the event page before choosing tickets.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <?php esc_html_e( 'You can configure a global attendee form and additional services once, then import them into any event and fine-tune them there.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <?php esc_html_e( 'The event post type and taxonomies support Gutenberg and Polylang, so events can be edited in the block editor and translated for multilingual sites.', 'abp-event-ticket' ); ?></p>
                                <p class="dash_body_text">• <?php esc_html_e( 'When you uninstall the plugin, your data is preserved by default so you can reinstall later without losing anything. Turn ON "Remove All Data on Uninstall" in Configuration → ON/OFF only if you really want to delete everything.', 'abp-event-ticket' ); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}
			private function step_card( string $step, string $title, string $lead, string $text, string $tab = '', string $btn_label = '' ): void {
				$url = $tab ? ABPET_Function::build_url( $tab ) : '';
				?>
                <div class="dash_card">
                    <div class="dash_card_head">
                        <h4><span class="dash_step"><?php echo esc_html( $step ); ?></span><?php echo esc_html( $title ); ?></h4>
						<?php if ( $url && $btn_label ) { ?>
                            <a class="_btn_light_active_xs" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $btn_label ); ?></a>
						<?php } ?>
                    </div>
                    <div class="dash_card_body">
                        <p class="dash_body_lead"><?php echo esc_html( $lead ); ?></p>
                        <p class="dash_body_text"><?php echo esc_html( $text ); ?></p>
                    </div>
                </div>
				<?php
			}
		}
		new ABPET_Documentation();
	}